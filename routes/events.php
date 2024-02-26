<?php

use App\Http\Controllers\EventsController;
use Illuminate\Support\Facades\Route;


Route::prefix('history')
    ->controller(EventsController::class)
    ->group(function () {
        Route::get('/', 'getEvents');
        Route::post('/', 'store');
    });
