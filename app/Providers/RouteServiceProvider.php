<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {

            Route::group(['middleware' => ['web'], 'as' => 'web.'], function () {
                Route::group(['middleware' => ['guest']], base_path('routes/web/guest.php'));
            });

            Route::group(['middleware' => ['api'], 'prefix' => 'api/v1', 'as' => 'api.'], function () {
                Route::group([
                    'middleware' => ['auth:sanctum'],
                ], base_path('routes/api/authenticated.php'));

                Route::group([
                    'middleware' => ['guest'],
                ], base_path('routes/api/guest.php'));

                Route::group([
                    'middleware' => ['auth:sanctum', 'verified'],
                ], base_path('routes/api/verified.php'));
            });
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
