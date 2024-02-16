<?php

use App\Http\Controllers\FloorsController;
use Illuminate\Support\Facades\Route;

Route::prefix('floors')
    ->controller(FloorsController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::post('remove', 'remove');
    });