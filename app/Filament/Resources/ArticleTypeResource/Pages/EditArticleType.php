<?php

namespace App\Filament\Resources\ArticleTypeResource\Pages;

use App\Filament\Resources\ArticleTypeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArticleType extends EditRecord
{
    protected static string $resource = ArticleTypeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
