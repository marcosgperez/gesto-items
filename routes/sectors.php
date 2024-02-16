<?php

use App\Http\Controllers\SectorsController;
use Illuminate\Support\Facades\Route;

Route::prefix('sectors')
    ->controller(SectorsController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::post('remove', 'remove');
    });