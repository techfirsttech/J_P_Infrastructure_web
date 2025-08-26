<?php

use Illuminate\Support\Facades\Route;
use Modules\Labour\Http\Controllers\LabourApiController;
use Modules\Labour\Http\Controllers\LabourController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('labours', LabourController::class)->names('labour');

    Route::post('labour-list', [LabourApiController::class, 'index']);
    Route::post('labour-add', [LabourApiController::class, 'store']);
    Route::post('labour-update', [LabourApiController::class, 'update']);
    Route::delete('labour-delete', [LabourApiController::class, 'destroy']);
    Route::post('labour-status-change', [LabourApiController::class, 'statusChange']);
});
