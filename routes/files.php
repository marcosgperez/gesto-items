<?php
use App\Http\Controllers\FilesController;
use Illuminate\Support\Facades\Route;


Route::prefix('files')
    ->controller(FilesController::class)
    ->group(function () {
        Route::get('/', 'getPresignedUrl');
    });
