<?php

use Illuminate\Support\Facades\Route;
use Modules\StockTransfer\Http\Controllers\StockTransferApiController;
use Modules\StockTransfer\Http\Controllers\StockTransferController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('stocktransfers', StockTransferController::class)->names('stocktransfer');

    Route::post('raw-material-stock-transfer', [StockTransferApiController::class, 'store']);
    Route::post('raw-material-stock-transfer-list', [StockTransferApiController::class, 'index']);

});
