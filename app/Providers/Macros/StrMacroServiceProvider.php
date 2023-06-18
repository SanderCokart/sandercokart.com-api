<?php

namespace App\Providers\Macros;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class StrMacroServiceProvider extends ServiceProvider
{

    public function boot(): void
    {
        Str::macro('readDuration', function($body, $wordsPerMinute = 175) {
            return (int)ceil(Str::wordCount(strip_tags($body)) / $wordsPerMinute);
        });
    }
}
