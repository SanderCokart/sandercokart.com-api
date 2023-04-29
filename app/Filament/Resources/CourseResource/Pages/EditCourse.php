<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use App\Models\Course;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCourse extends EditRecord
{
    protected static string $resource = CourseResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()->after(
                fn(Course $record) => $record->redact()
            ),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return self::getResource()::getUrl('index');
    }
}
