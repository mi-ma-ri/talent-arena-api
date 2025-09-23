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

    // 変数名や処理に適した意味で考慮(変数名、定数、メソッドなど)
    // 本登録完了処理時、メールが送付されるようにする
    // auth_keyがちゃんと削除されているかどうか
    // 本登録完了処理時、ログイン状態にする