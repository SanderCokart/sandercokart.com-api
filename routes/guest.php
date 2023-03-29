<?php

use App\Http\Controllers\ContactFormController;
use App\Http\Controllers\HelloController;
use App\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

Route::post('/contact', ContactFormController::class)->middleware('throttle:2,10,contact-form')->name('contact');
