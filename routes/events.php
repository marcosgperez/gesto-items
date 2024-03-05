<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventsController;

Route::prefix('history')
    ->controller(EventsController::class)
    ->group(function () {
        Route::get('/', 'getEvents');
        Route::post('/', 'store');
    });
