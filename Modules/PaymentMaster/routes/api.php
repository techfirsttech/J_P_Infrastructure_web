<?php

use Illuminate\Support\Facades\Route;
use Modules\IncomeMaster\Http\Controllers\IncomeMasterApiController;
use Modules\PaymentMaster\Http\Controllers\PaymentMasterApiController;
use Modules\PaymentMaster\Http\Controllers\PaymentMasterController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('paymentmasters', PaymentMasterController::class)->names('paymentmaster');

    Route::post('payment-transfer-list', [PaymentMasterApiController::class, 'index']);
    Route::post('payment-transfer-add', [PaymentMasterApiController::class, 'store']);
    Route::post('payment-transfer-update', [PaymentMasterApiController::class, 'update']);
    Route::delete('payment-transfer-delete', [PaymentMasterApiController::class, 'destroy']);
});
