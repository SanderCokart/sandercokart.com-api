<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\URL;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\Action::make('Preview')
                ->url($this->generateSignedUrl(), true),
        ];
    }

    private function generateSignedUrl(): string
    {
        $signedRoute = URL::signedRoute('api.articles.show', [
            'article' => $this->record->slug,
            'type'    => $this->record->type->name,
        ], now()->addHour(), false);

        ['path' => $path, 'query' => $query] = parse_url($signedRoute);

        $newPath = preg_replace('/\/api\/v[1-9]+/', config('frontend.url'), $path) . '/preview';
        return $newPath . '?' . $query;
    }
}
