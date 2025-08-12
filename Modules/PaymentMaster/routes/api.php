<?php

use Illuminate\Support\Facades\Route;
use Modules\PaymentMaster\Http\Controllers\PaymentMasterController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('paymentmasters', PaymentMasterController::class)->names('paymentmaster');
});
