<?php

namespace App\Filament\Resources;

use App\Enums\ArticleTypeEnum;
use App\Enums\DiskEnum;
use App\Enums\MediaCollectionEnum;
use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->autofocus()
                            ->required()
                            ->reactive()
                            ->placeholder('Enter a title...')
                            ->rules('required|max:255'),
                        Forms\Components\Select::make('article_type_id')
                            ->relationship('type', 'name')
                            ->options(ArticleTypeEnum::getAssocArray(fn($value) => Str::title($value)))
                            ->required(),
                        Forms\Components\Select::make('courses')
                            ->relationship('courses', 'title')
                            ->preload()
                            ->multiple()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('title')
                                    ->autofocus()
                                    ->required()
                                    ->reactive()
                                    ->placeholder('Learn Laravel | React | Vue | Tailwind')
                                    ->rules('required|max:255'),
                                Forms\Components\SpatieMediaLibraryFileUpload::make('banner')
                                    ->required()
                                    ->image()
                                    ->collection(MediaCollectionEnum::CourseBanners())
                                    ->imageCropAspectRatio('3:2')
                                    ->placeholder('Upload a banner...')
                                    ->columnSpan(2)
                                    ->disk(DiskEnum::public()),
                            ])
                            ->searchable()
                            ->nullable(),
                    ]),

                Forms\Components\SpatieMediaLibraryFileUpload::make('banner')
                    ->required()
                    ->image()
                    ->collection(MediaCollectionEnum::ArticleBanners())
                    ->imageCropAspectRatio('3:2')
                    ->placeholder('Upload a banner...')
                    ->columnSpan(2)
                    ->disk(DiskEnum::public()),

                Forms\Components\Toggle::make('published_at')
                    ->visibleOn('create')
                    ->dehydrateStateUsing(function ($state) {
                        return $state ? now() : null;
                    })
                    ->label('Publish Immediately'),

                Forms\Components\Toggle::make('published_at')
                    ->visibleOn('edit')
                    ->label('Published')
                    ->dehydrateStateUsing(function ($state, Article $record) {
                        if (isset($record->published_at) !== $state) {
                            if ($state) {
                                return now();
                            }

                            return null;
                        }
                        return $record->published_at;
                    })
                    ->reactive(),

                Forms\Components\Textarea::make('excerpt')
                    ->placeholder('Enter an excerpt...')
                    ->required()
                    ->columnSpan(2)
                    ->autosize()
                    ->rules('required'),

                Forms\Components\MarkdownEditor::make('body')
                    ->placeholder('Enter a body...')
                    ->fileAttachmentsDirectory('markdown-attachments')
                    ->fileAttachmentsDisk(DiskEnum::public())
                    ->required()
                    ->columnSpan(2)
                    ->rules('required'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('type.name')
                    ->formatStateUsing(fn($state) => Str::headline($state)),
                Tables\Columns\SpatieMediaLibraryImageColumn::make('banners')
                    ->collection(
                        MediaCollectionEnum::ArticleBanners()
                    )
                    ->label('Banner'),
                Tables\Columns\TextColumn::make('created_at')
                    ->toggleable()
                    ->since()
                    ->tooltip(fn($record) => $record->created_at)
                    ->sortable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->since()
                    ->tooltip(fn($record) => $record->updated_at)
                    ->toggleable()
                    ->sortable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('published_at')
                    ->tooltip(fn($record) => $record->published_at)
                    ->since()
                    ->placeholder('Draft')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('Published')
                    ->getStateUsing(fn(Article $record) => isset($record->published_at))
                    ->updateStateUsing(function ($state, Article $record) {
                        $state
                            ? $record->publish()
                            : $record->unpublish();
                    }),
            ])
            ->filtersFormWidth(MaxWidth::Large)
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
                    ->preload()
                    ->options(
                        (fn() => collect(ArticleTypeEnum::all())
                            ->mapWithKeys(
                                fn(ArticleTypeEnum $articleType) => [
                                    $articleType->getId() => Str::headline(
                                        $articleType()
                                    )
                                ]
                            )
                            ->toArray())
                    )
                    ->placeholder('All')
                    ->label('Type'),
                Tables\Filters\Filter::make('published_at')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('published_from')
                                    ->displayFormat('D d F / d-m-Y')
                                    ->native(false)
                                    ->minDate(Article::min('published_at'))
                                    ->maxDate(Article::max('published_at'))
                                    ->required(),
                                Forms\Components\DatePicker::make('published_until')
                                    ->displayFormat('D d F / d-m-Y')
                                    ->minDate(Article::min('published_at'))
                                    ->maxDate(Article::max('published_at'))
                                    ->native(false)
                                    ->required(),
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['published_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('published_at', '>=', $date),
                            )
                            ->when(
                                $data['published_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('published_at', '<=', $date),
                            );
                    }),
            ])
            ->defaultSort('published_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
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
