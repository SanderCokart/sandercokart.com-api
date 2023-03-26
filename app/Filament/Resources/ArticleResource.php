<?php

namespace App\Filament\Resources;

use App\Enums\Disk;
use App\Filament\Resources\ArticleResource\Pages;
use App\Filament\Resources\ArticleResource\RelationManagers;
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

    protected static ?string $navigationIcon = 'heroicon-o-collection';

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
                Forms\Components\FileUpload::make('banner')
                    ->required()
                    ->image()
                    ->imageCropAspectRatio('3:2')
                    ->placeholder('Upload a banner...')
                    ->columnSpan(2)
                    ->directory('banners')
                    ->disk(Disk::publishedArticles->name)
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
                    ->dehydrateStateUsing(function ($state, $record) {
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
                    ->fileAttachmentsDirectory('attachments')
                    ->fileAttachmentsDisk(Disk::publishedArticles->name)
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
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('articleType.name'),
                Tables\Columns\ImageColumn::make('banner'),
                Tables\Columns\TextColumn::make('created_at'),
                Tables\Columns\TextColumn::make('updated_at'),
                Tables\Columns\TextColumn::make('published_at'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
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
