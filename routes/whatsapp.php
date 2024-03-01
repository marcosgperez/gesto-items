<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsappController;

Route::prefix('whatsapp')
    ->controller(WhatsappController::class)
    ->group(function () {
        Route::get('/', 'getBotMode');
    });