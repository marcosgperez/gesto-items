<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventsTypesController;

Route::prefix('history')
    ->controller(EventsTypesController::class)
    ->group(function () {
        Route::get('/', 'getEvents');
        Route::post('/', 'store');
    });
