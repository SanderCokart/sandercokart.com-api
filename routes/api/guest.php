<?php

use App\Enums\ArticleTypeEnum;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ContactFormController;
use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;

Route::post('/contact', ContactFormController::class)->middleware('throttle:2,10,contact-form')->name('contact');

Route::get('/articles/paths', [ArticleController::class, 'paths'])->name('articles.paths');
Route::get('/articles/{type:name}', [ArticleController::class, 'index'])
    ->name('articles.index')
    ->whereIn('type', ArticleTypeEnum::names());
Route::get('/articles/{type:name}/{article:slug}', [ArticleController::class, 'show'])->name('articles.show')
    ->whereIn('type', ArticleTypeEnum::names());


Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('courses.show');
