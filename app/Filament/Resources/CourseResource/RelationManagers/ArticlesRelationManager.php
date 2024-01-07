<?php

namespace App\Filament\Resources\CourseResource\RelationManagers;

use App\Enums\ArticleTypeEnum;
use App\Enums\DiskEnum;
use App\Enums\MediaCollectionEnum;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Str;

class ArticlesRelationManager extends RelationManager
{
    protected static string $relationship = 'articles';

    protected static ?string $recordTitleAttribute = 'title';

    public function form(Form $form): Form
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
                        Forms\Components\Select::make('type')
                            ->relationship('type', 'name')
                            ->options(ArticleTypeEnum::getAssocArray(fn($option) => Str::headline($option)))
                            ->default(ArticleTypeEnum::courses->getId())
                            ->disabled()
                            ->required(),
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

    protected function getTableReorderColumn(): ?string
    {
        return 'order_column';
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('article_id'),
                Tables\Columns\TextColumn::make('course_id'),
                Tables\Columns\TextColumn::make('order_column'),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('published_at')
                    ->tooltip(fn($record) => $record->published_at)
                    ->sortable()
                    ->placeholder('Draft'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
