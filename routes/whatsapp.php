<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsappController;

Route::prefix('whatsapp')
    ->controller(WhatsappController::class)
    ->group(function () {
        Route::get('/', 'getBotMode');
        Route::get('/send-messages', 'send_fifo_messages');
    });