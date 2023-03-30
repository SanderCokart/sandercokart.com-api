<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ContactFormController;
use Illuminate\Support\Facades\Route;

Route::post('/contact', ContactFormController::class)->middleware('throttle:2,10,contact-form')->name('contact');

Route::get('/articles/paths', [ArticleController::class, 'paths'])->name('articles.paths');
Route::apiResource('articles', ArticleController::class)->only(['index', 'show']);
