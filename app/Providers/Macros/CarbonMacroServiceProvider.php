<?php

namespace App\Providers\Macros;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use JamesMills\LaravelTimezone\Facades\Timezone;

class CarbonMacroServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerConvertToLocalMacro();
    }

    private function registerConvertToLocalMacro(): void
    {
        Carbon::macro(
            'convertToLocal',
            function (?Carbon $date = null, $format = null, $format_timezone = false, $enableTranslation = null) {
                return Timezone::convertToLocal($date ?? $this, $format, $format_timezone, $enableTranslation);
            }
        );

        Carbon::macro(
            'convertFromLocal',
            function ($date = null) {
                return Timezone::convertFromLocal($date ?? $this);
            }
        );
    }
}
