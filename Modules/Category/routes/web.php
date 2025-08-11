<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Controllers\CategoryController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('categories', CategoryController::class)->names('category');
    Route::get('get-sub-categories/{id}', [CategoryController::class, 'byParentId']);
});
