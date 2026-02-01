<?php

use Illuminate\Support\Facades\Route;

Route::controller(App\Http\Controllers\RegisterController::class)
    ->name('register.')
    ->prefix('register')
    ->group(function () {
        Route::get('create_email', 'getRegisterAuthKey')->name('get.get_registerAuthKey');
        Route::post('create_email', 'getRegisterAuthKey')->name('post.get_registerAuthKey');
        Route::get('exist_key', 'existsKey')->name('get.existskey');
        Route::post('exist_key', 'existsKey')->name('get.existskey');
        Route::get('get_register_player', 'getRegisterPlayer')->name('get.get_register_player');
        Route::post('join', 'postJoin')->name('post.join');
    });

Route::controller(App\Http\Controllers\LoginController::class)
    ->name('login.')
    ->prefix('login')
    ->group(function () {
        Route::get('restore', 'login')->name('get.restore');
        Route::post('restore', 'login')->name('post.restore');
    });
