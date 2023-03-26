<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleTypeResource\Pages;
use App\Filament\Resources\ArticleTypeResource\RelationManagers;
use App\Models\ArticleType;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class ArticleTypeResource extends Resource
{
    protected static ?string $model = ArticleType::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->autofocus()
                    ->placeholder('Enter a name...'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('articles_count')->counts('articles'),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn($record) => $record->articles()->exists()),
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
            'index'  => Pages\ListArticleTypes::route('/'),
            'create' => Pages\CreateArticleType::route('/create'),
            'edit'   => Pages\EditArticleType::route('/{record}/edit'),
        ];
    }
}
