<?php

use App\Http\Controllers\EventsTypesController;
use Illuminate\Support\Facades\Route;

Route::prefix('events-types')
    ->controller(EventsTypesController::class)
    ->group(function () {
        Route::get('/', 'index');
    });