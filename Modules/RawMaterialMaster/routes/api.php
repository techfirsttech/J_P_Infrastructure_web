<?php

use Illuminate\Support\Facades\Route;
use Modules\RawMaterialMaster\Http\Controllers\RawMaterialMasterController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('rawmaterialmasters', RawMaterialMasterController::class)->names('rawmaterialmaster');
});
