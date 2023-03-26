<?php

namespace App\Filament\Resources\ArticleTypeResource\Pages;

use App\Filament\Resources\ArticleTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArticleType extends CreateRecord
{
    protected static string $resource = ArticleTypeResource::class;

    //redirect to index page after create
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
