<?php

use Illuminate\Support\Facades\Route;

Route::controller(App\Http\Controllers\RegisterController::class)
    ->name('register.')
    ->prefix('register')
    ->group(function () {
        Route::get('create_email', 'getEmailAuthKey')->name('get.get_emailAuthKey');
        // Route::post('create_email', 'getEmailAuthKey')->name('post.get_emailAuthKey');
    });
