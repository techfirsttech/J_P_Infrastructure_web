<?php

use Illuminate\Support\Facades\Route;
use Modules\ExpenseMaster\Http\Controllers\ExpenseMasterController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('expensemasters', ExpenseMasterController::class)->names('expensemaster');

    Route::post('expense-status-change', [ExpenseMasterController::class, 'statusChange'])->name('expense-status-change');

    Route::get('payment-ledger', [ExpenseMasterController::class, 'paymentLedger'])->name('payment-ledger');


});
