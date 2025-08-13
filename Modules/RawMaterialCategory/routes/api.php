<?php

use Illuminate\Support\Facades\Route;
use Modules\RawMaterialCategory\Http\Controllers\RawMaterialCategoryController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('rawmaterialcategories', RawMaterialCategoryController::class)->names('rawmaterialcategory');
});
