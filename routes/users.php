<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')
    ->controller(UsersController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/index-by-id', 'indexById');
        Route::post('/', 'store');
        Route::post('/remove', 'remove');
    });


Route::prefix('auth')
    ->controller(AuthController::class)
    ->group(function () {
        Route::post('login', 'login');
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
        Route::get('me', 'me');
    });