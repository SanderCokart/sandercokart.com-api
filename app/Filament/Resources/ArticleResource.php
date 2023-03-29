<?php

namespace App\Filament\Resources;

use App\Enums\Disk;
use App\Enums\MediaCollection;
use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
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
                            ->afterStateUpdated(function ($set, $state) {
                                $set('slug', Str::slug($state));
                            })
                            ->rules('required|max:255'),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->placeholder('Enter a slug...')
                            ->unique(ignoreRecord: true)
                            ->rules('required|max:255'),
                        Forms\Components\Select::make('article_type_id')
                            ->relationship('articleType', 'name')
                            ->required(),
                    ]),

                Forms\Components\SpatieMediaLibraryFileUpload::make('banner')
                    ->required()
                    ->image()
                    ->collection(MediaCollection::ArticleBanners->name)
                    ->imageCropAspectRatio('3:2')
                    ->placeholder('Upload a banner...')
                    ->columnSpan(2)
                    ->disk(Disk::public->name)
                    ->rules('required'),

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
                    ->fileAttachmentsDisk(Disk::public->name)
                    ->required()
                    ->columnSpan(2)
                    ->rules('required'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('slug')->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('articleType.name'),
                Tables\Columns\SpatieMediaLibraryImageColumn::make('banner')->collection(MediaCollection::ArticleBanners->name),
                Tables\Columns\TextColumn::make('created_at')->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('updated_at')->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('published_at')->default('Draft'),
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
                    ->falseLabel('Drafts')
                    ->trueLabel('Published')
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('published_at'),
                        false: fn(Builder $query) => $query->whereNull('published_at'),
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
