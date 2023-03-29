<?php

use App\Http\Controllers\MediaController;

Route::get('/media', [MediaController::class, 'show'])->middleware('signed')->name('media.show');
