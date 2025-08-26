<?php

use Illuminate\Support\Facades\Route;
use Modules\Contractor\Http\Controllers\ContractorApiController;
use Modules\Contractor\Http\Controllers\ContractorController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('contractors', ContractorController::class)->names('contractor');

     Route::post('contractor-dropdown', [ContractorApiController::class, 'contractorDropdown']);
     Route::post('contractor-labour-dropdown', [ContractorApiController::class, 'contractorLabourDropdown']);
     Route::post('contractor-list', [ContractorApiController::class, 'index']);
    Route::post('contractor-add', [ContractorApiController::class, 'store']);
    Route::post('contractor-update', [ContractorApiController::class, 'update']);
    Route::delete('contractor-delete', [ContractorApiController::class, 'destroy']);
});
