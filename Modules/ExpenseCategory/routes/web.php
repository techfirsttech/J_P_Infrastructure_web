<?php

use Illuminate\Support\Facades\Route;
use Modules\ExpenseCategory\Http\Controllers\ExpenseCategoryController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('expensecategories', ExpenseCategoryController::class)->names('expensecategory');
    Route::post('expense-category-status-change', [ExpenseCategoryController::class, 'statusChange'])->name('expense-category-status-change');
});
