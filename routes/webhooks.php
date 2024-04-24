
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsappController;
Route::prefix('webhooks')
    ->controller(WhatsappController::class)
    ->group(function () {
        Route::post('/', 'webhook_evolution');
    });