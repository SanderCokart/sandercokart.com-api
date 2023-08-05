<?php

namespace App\Filament\Resources;

use App\Enums\ArticleTypeEnum;
use App\Enums\DiskEnum;
use App\Enums\MediaCollectionEnum;
use App\Filament\Resources\ArticleResource\Pages;
use App\Forms\Components\MDXEditor;
use App\Models\Article;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Spatie\FilamentMarkdownEditor\MarkdownEditor;
use Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make([

                    Forms\Components\TextInput::make('title')
                        ->autofocus()
                        ->required()
                        ->reactive()
                        ->placeholder('Enter a title...')
                        ->rules('required|max:255'),

                    Forms\Components\Select::make('article_type_id')
                        ->relationship('type', 'name')
                        ->options(ArticleTypeEnum::getAssocArray(fn($option) => Str::headline($option)))
                        ->reactive()
                        ->required(),

                    Forms\Components\Select::make('courses')
                        ->relationship('courses', 'title')
                        ->multiple()
                        ->reactive()
                        ->preload()
                        ->hidden(fn($get) => (int)$get('article_type_id') !== ArticleTypeEnum::courses->getId())
                        ->createOptionForm([
                            Forms\Components\TextInput::make('title')
                                ->autofocus()
                                ->required()
                                ->reactive()
                                ->placeholder('Enter a title...')
                                ->rules('required|max:255')
                                ->placeholder('Learn Laravel | React | Vue | Tailwind'),

                            Forms\Components\SpatieMediaLibraryFileUpload::make('media')
                                ->collection(MediaCollectionEnum::CourseBanners())
                                ->label('Banner')
                                ->imageCropAspectRatio('16:9')
                                ->required()
                                ->image(),
                            Forms\Components\Toggle::make('published_at')
                                ->dehydrateStateUsing(fn($state) => $state ? now() : null)
                                ->label('Publish Now'),

                        ])
                        ->searchable()
                        ->nullable(),

                ])->columns(3),

                Forms\Components\Card::make([
                    Forms\Components\SpatieMediaLibraryFileUpload::make('media')
                        ->label('Banner')
                        ->collection(MediaCollectionEnum::ArticleBanners())
                        ->imageCropAspectRatio('16:9')
                        ->columnSpan(2)
                        ->required()
                        ->image(),

                    Forms\Components\Textarea::make('excerpt')
                        ->placeholder('Enter an excerpt...')
                        ->required()
                        ->columnSpan(2)
                        ->autosize()
                        ->rules('required'),

                    MarkdownEditor::make('body')
                        ->placeholder('Enter a body...')
                        ->fileAttachmentsDirectory('markdown-attachments')
                        ->fileAttachmentsDisk(DiskEnum::public())
                        ->required()
                        ->columnSpan(2)
                        ->rules('required'),

                    Forms\Components\Toggle::make('published_at')
                        ->dehydrateStateUsing(fn($state) => $state ? now() : null)
                        ->label(fn($context) => match ($context) {
                            'edit'   => 'Published',
                            'create' => 'Publish Now',
                        }),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                Tables\Columns\TextColumn::make('type.name')
                    ->formatStateUsing(fn($state) => Str::headline($state)),

                Tables\Columns\SpatieMediaLibraryImageColumn::make('media')
                    ->label('Banner')
                    ->collection(MediaCollectionEnum::ArticleBanners()),

                Tables\Columns\TextColumn::make('created_at')
                    ->toggleable()
                    ->dateTimezone()
                    ->toggledHiddenByDefault()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->toggleable()
                    ->dateTimezone()
                    ->toggledHiddenByDefault()
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->dateTimezone()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\ToggleColumn::make('Published')
                    ->getStateUsing(fn(Article $record) => isset($record->published_at))
                    ->updateStateUsing(function ($state, Article $record) {
                        $record->setAttribute('published_at', $state ? now() : null);
                        $record->save();
                    }),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                //filter drafts and published
                Tables\Filters\TernaryFilter::make('published_at')
                    ->placeholder('All')
                    ->label('Show')
                    ->falseLabel('Drafts')
                    ->trueLabel('Published')
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('published_at'),
                        false: fn(Builder $query) => $query->whereNull('published_at'),
                    ),
                Tables\Filters\SelectFilter::make('type')
                    ->relationship('type', 'name')
                    ->multiple()
                    ->options((fn() => collect(ArticleTypeEnum::all())->mapWithKeys(fn(ArticleTypeEnum $articleType) => [$articleType->getId() => Str::headline($articleType())])->toArray()))
                    ->placeholder('All')
                    ->label('Type'),
            ])
            ->defaultSort('published_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
                Tables\Actions\BulkAction::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-check-circle')
                    ->action(fn(Collection $records) => $records->each(fn(Article $record) => $record->publish())),
                Tables\Actions\BulkAction::make('unpublish')
                    ->label('Redact')
                    ->icon('heroicon-o-x-circle')
                    ->action(fn(Collection $records) => $records->each(fn(Article $record) => $record->redact())),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit'   => Pages\EditArticle::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
