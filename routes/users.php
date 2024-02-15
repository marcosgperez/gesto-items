<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')
    ->controller(UsersController::class)
    ->group(function () {
        Route::get('/', 'indexById');
        Route::get('/index', 'index');
        Route::post('/store', 'store');
        Route::put('/update', 'update');
        Route::put('/delete', 'delete');
        Route::get('/delete', 'trash');
        Route::put('/restore', 'restore');
    });

Route::prefix('auth')
    ->controller(AuthController::class)
    ->group(function () {
        Route::post('login', 'login');
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
        Route::get('me', 'me');
    });