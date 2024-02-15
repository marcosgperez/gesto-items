<?php

use App\Http\Controllers\UserTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('user-types')
    ->controller(UserTypeController::class)
    ->group(function () {
        Route::get('/index', 'index');
        Route::post('/store', 'store');
        Route::put('/update', 'update');
        Route::put('/delete', 'delete');
        Route::get('/delete', 'trash');
        Route::put('/restore', 'restore');
    });