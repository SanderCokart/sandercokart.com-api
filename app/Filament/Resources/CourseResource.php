<?php

namespace App\Filament\Resources;

use App\Enums\DiskEnum;
use App\Enums\MediaCollectionEnum;
use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers\ArticlesRelationManager;
use App\Models\Course;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->autofocus()
                    ->placeholder('Learn Laravel | React | Vue | Tailwind'),
                Forms\Components\SpatieMediaLibraryFileUpload::make('banner')
                    ->required()
                    ->image()
                    ->collection(MediaCollectionEnum::CourseBanners())
                    ->imageCropAspectRatio('3:2')
                    ->placeholder('Upload a banner...')
                    ->columnSpan(2)
                    ->disk(DiskEnum::public()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('slug')->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\SpatieMediaLibraryImageColumn::make('banners')->collection(MediaCollectionEnum::CourseBanners())->label('Banner'),
                Tables\Columns\TextColumn::make('created_at')->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('updated_at')->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('published_at')
                    ->tooltip(fn($record) => $record->published_at)
                    ->formatStateUsing(fn($state) => $state->diffForHumans())
                    ->default('Draft')->sortable()->searchable()
                    ->sortable()
                    ->searchable(),

            ])
            ->defaultSort('published_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ArticlesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
