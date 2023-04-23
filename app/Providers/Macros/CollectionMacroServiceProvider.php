<?php

namespace App\Providers\Macros;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class CollectionMacroServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $macro = function (int $count = 1): Collection {
            $items = $this->random($count);

            $this->items = array_diff($this->items, $items->all());

            return $items;
        };
        Collection::macro('pullRandom', $macro);
    }
}
