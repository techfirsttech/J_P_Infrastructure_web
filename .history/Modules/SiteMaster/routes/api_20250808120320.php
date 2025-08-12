<?php

use Illuminate\Support\Facades\Route;
use Modules\SiteMaster\Http\Controllers\SiteMasterController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitemasters', SiteMasterController::class)->names('sitemaster');
});
