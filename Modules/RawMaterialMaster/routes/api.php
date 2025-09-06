<?php

use Illuminate\Support\Facades\Route;
use Modules\RawMaterialMaster\Http\Controllers\RawMaterialMasterController;
use Modules\RawMaterialMaster\Http\Controllers\RawMaterialMasterInOutApiController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('rawmaterialmasters', RawMaterialMasterController::class)->names('rawmaterialmaster');

    Route::get('raw-material-dropdown', [RawMaterialMasterInOutApiController::class, 'rawMaterialDropdown']);
    Route::get('unit-dropdown', [RawMaterialMasterInOutApiController::class, 'unitDropdown']);

    Route::post('raw-material-transaction-add', [RawMaterialMasterInOutApiController::class, 'store']);
    Route::post('raw-material-transaction-update', [RawMaterialMasterInOutApiController::class, 'update']);
    Route::post('raw-material-transaction-list', [RawMaterialMasterInOutApiController::class, 'index']);
    Route::delete('raw-material-transaction-delete', [RawMaterialMasterInOutApiController::class, 'destroy']);
    Route::post('raw-material-stock-list', [RawMaterialMasterInOutApiController::class, 'materialStock']);

    Route::post('raw-material-add', [RawMaterialMasterInOutApiController::class, 'save']);

});
