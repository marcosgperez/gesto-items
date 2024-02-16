<?php

use App\Http\Controllers\UserTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('user-types')
    ->controller(UserTypeController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::post('remove', 'remove');
    });