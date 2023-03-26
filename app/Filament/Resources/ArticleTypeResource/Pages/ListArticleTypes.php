<?php

namespace App\Filament\Resources\ArticleTypeResource\Pages;

use App\Filament\Resources\ArticleTypeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListArticleTypes extends ListRecords
{
    protected static string $resource = ArticleTypeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
