<?php

use Illuminate\Support\Facades\Route;
use Modules\RawMaterialCategory\Http\Controllers\RawMaterialCategoryController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('rawmaterialcategories', RawMaterialCategoryController::class)->names('rawmaterialcategory');
});
