<?php

use Illuminate\Support\Facades\Route;
use Modules\ExpenseMaster\Http\Controllers\ExpenseMasterApiController;
use Modules\ExpenseMaster\Http\Controllers\ExpenseMasterController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('expensemasters', ExpenseMasterController::class)->names('expensemaster');

    Route::get('expense-category-dropdown', [ExpenseMasterApiController::class, 'expenseCategoryDropdown']);
    Route::post('expense-list', [ExpenseMasterApiController::class, 'index']);
    Route::post('expense-add', [ExpenseMasterApiController::class, 'store']);
    Route::post('expense-update', [ExpenseMasterApiController::class, 'update']);
    Route::delete('expense-delete', [ExpenseMasterApiController::class, 'destroy']);
});
