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
        Route::get('auth', 'auth')->name('get.auth');
        Route::post('auth', 'auth')->name('post.auth');
    });

Route::controller(App\Http\Controllers\PlayerController::class)
    ->middleware('auth:sanctum')
    ->name('player.')
    ->prefix('player')
    ->group(function () {
        Route::get('profile', 'getProfile')->name('get.profile');
        Route::post('update', 'postProfileUpdate')->name('post.profile_update');
        Route::post('handle', 'postHandleUrl')->name('post.handle');
    });
