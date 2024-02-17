<?php

use App\Http\Controllers\ItemsController;
use Illuminate\Support\Facades\Route;

Route::prefix('items')
    ->controller(ItemsController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::post('remove', 'remove');
    });