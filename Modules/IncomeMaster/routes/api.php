<?php

use Illuminate\Support\Facades\Route;
use Modules\IncomeMaster\Http\Controllers\IncomeMasterApiController;
use Modules\IncomeMaster\Http\Controllers\IncomeMasterController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('incomemasters', IncomeMasterController::class)->names('incomemaster');

    Route::post('income-list', [IncomeMasterApiController::class, 'index']);
    Route::post('income-add', [IncomeMasterApiController::class, 'store']);
    Route::post('income-update', [IncomeMasterApiController::class, 'update']);
    Route::delete('income-delete', [IncomeMasterApiController::class, 'destroy']);
});
