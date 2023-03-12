<?php

use App\Http\Controllers\ContactFormController;
use Illuminate\Support\Facades\Route;

Route::get('/hello-world', function () {
    return response()->json([
        'message' => 'Hello World',
    ]);
});

Route::post('/contact', ContactFormController::class)->middleware('throttle:2,10,contact-form');
