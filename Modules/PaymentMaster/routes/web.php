<?php

use Illuminate\Support\Facades\Route;
use Modules\PaymentMaster\Http\Controllers\PaymentMasterController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('paymentmasters', PaymentMasterController::class)->names('paymentmaster');
});
