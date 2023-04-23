<?php

namespace App\Providers\Macros;

use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\ServiceProvider;

/** @mixin TextColumn */
class FilamentMacroServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot(): void
    {
        $macro = function () {
            $this->dateTime()
                ->formatStateUsing(function (Carbon|null|string $state) {
                    return $state ? $state->convertToLocal() : 'Not Published';
                });
            return $this;
        };

        TextColumn::macro('dateTimezone', $macro);
        DateTimePicker::macro('dateTimezone', $macro);
    }
}
