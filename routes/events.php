<?php

use App\Http\Controllers\EventsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventTypesController;

Route::prefix('history')
    ->controller(EventsController::class)
    ->group(function () {
        Route::get('/', 'getEvents');
        Route::post('/', 'store');
    });

Route::prefix('event-types')
    ->controller(EventTypesController::class)
    ->group(function () {
        Route::get('/', 'index');
    });
