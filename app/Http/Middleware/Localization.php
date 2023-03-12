<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Localization
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->cookie('NEXT_LOCALE')) {
            app()->setLocale($request->cookie('NEXT_LOCALE'));
        } else {
            app()->setLocale($request->getPreferredLanguage());
        }

        return $next($request);
    }
}
