<?php

namespace App\Filament\Resources;

use App\Enums\MediaCollectionEnum;
use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers\ArticlesRelationManager;
use App\Models\Course;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

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
                        ->rules('required|max:255')
                        ->placeholder('Learn Laravel | React | Vue | Tailwind'),

                    Forms\Components\Textarea::make('description')
                        ->required()
                        ->placeholder('Enter a description...')
                        ->rules('required|max:65535'),

                    Forms\Components\SpatieMediaLibraryFileUpload::make('media')
                        ->collection(MediaCollectionEnum::CourseBanners())
                        ->label('Banner')
                        ->imageCropAspectRatio('16:9')
                        ->required()
                        ->image(),

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
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('published_at')->dateTimezone(),
                Tables\Columns\SpatieMediaLibraryImageColumn::make('media')
                    ->label('Banner')
                    ->collection(MediaCollectionEnum::CourseBanners()),
                Tables\Columns\ToggleColumn::make('Published')
                    ->getStateUsing(fn(Course $record) => isset($record->published_at))
                    ->updateStateUsing(function ($state, Course $record) {
                        $record->setAttribute('published_at', $state ? now() : null);
                        $record->save();
                    }),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->after(
                    fn(Course $record) => $record->redact()
                ),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->after(
                    fn(Collection $records) => $records->each(fn(Course $record) => $record->redact())
                ),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
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
            'index'  => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit'   => Pages\EditCourse::route('/{record}/edit'),
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
