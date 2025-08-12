<?php

use Illuminate\Support\Facades\Route;
use Modules\ExpenseMaster\Http\Controllers\ExpenseMasterController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('expensemasters', ExpenseMasterController::class)->names('expensemaster');
});
