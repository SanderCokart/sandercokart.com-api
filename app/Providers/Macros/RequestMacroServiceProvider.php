<?php

namespace App\Providers\Macros;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

/**
 * @mixin Request
 */
class RequestMacroServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerMacros();
    }

    private function registerMacros(): void
    {
        $doesntHave = fn(string $key): bool => ! $this->has($key);
        $doesntHaveAny = fn(array $keys) => ! $this->hasAny($keys);

        Request::macro('doesntHave', $doesntHave);
        Request::macro('doesntHaveAny', $doesntHaveAny);
    }
}
