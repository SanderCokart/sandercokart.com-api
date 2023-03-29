<?php

use App\Http\Controllers\ContactFormController;
use App\Http\Controllers\HelloController;
use App\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'title'   => 'Welcome to the API',
        'openapi' => '3.0.0',
        'version' => '1.0.0',
        'license' => [
            'name' => 'MIT',
            'url'  => 'https://opensource.org/licenses/MIT',
        ],
        'author'  => [
            'name'            => 'Sander Cokart',
            'github'          => 'https://github.com/sandercokart',
            'email'           => 'api@sandercokart.com',
            'website'         => 'https://sandercokart.com',
            'company-website' => 'https://codehouse.sandercokart.com',
        ],
        'links'   => [
            'github'    => 'https://github.com/sandercokart/sandercokart.com-api',
            'issues'    => 'https://github.com/sanderCokart/sandercokart.com-api/issues',
            'docs'      => url('/docs'),
            'json-docs' => url('/json-docs'),
        ],
    ]);
})->name('home');

Route::get('/hello-world', HelloController::class)->name('hello-world');

Route::post('/contact', ContactFormController::class)->middleware('throttle:2,10,contact-form')->name('contact');
