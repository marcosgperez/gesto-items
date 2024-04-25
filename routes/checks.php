
<?php

use App\Http\Controllers\ChecksController;
use Illuminate\Support\Facades\Route;

Route::prefix('checks')
    ->controller(ChecksController::class)
    ->group(function () {
        Route::post('/', 'createCheck');
        Route::get('/remind', 'remind_check');
    });

    