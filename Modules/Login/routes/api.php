<?php

use Illuminate\Support\Facades\Route;
use Modules\Login\Http\Controllers\LoginApiController;
use Modules\Login\Http\Controllers\LoginController;

Route::prefix('v1')->group(function () {
    // Route::apiResource('logins', LoginController::class)->names('login');

    Route::post('login', [LoginApiController::class, 'index']);
});

// Route::post('login', [LoginApiController::class, 'index']);
