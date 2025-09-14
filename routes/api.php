<?php

use Illuminate\Support\Facades\Route;

Route::controller(App\Http\Controllers\RegisterController::class)
    ->name('register.')
    ->prefix('register')
    ->group(function () {
        Route::get('create_email', 'getRegisterAuthKey')->name('get.get_registerAuthKey');
        Route::post('create_email', 'getRegisterAuthKey')->name('post.get_registerAuthKey');
        Route::get('exist_key', 'existKey')->name('get.existskey');
        Route::post('exist_key', 'existKey')->name('get.existskey');
    });
