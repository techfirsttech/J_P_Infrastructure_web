<?php

use Illuminate\Support\Facades\Route;
use Modules\SiteMaster\Http\Controllers\SiteMasterApiController;
use Modules\SiteMaster\Http\Controllers\SiteMasterController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitemasters', SiteMasterController::class)->names('sitemaster');

    Route::get('site-dropdown', [SiteMasterApiController::class, 'siteDropdown']);
    Route::post('site-list', [SiteMasterApiController::class, 'index']);
    Route::post('site-add', [SiteMasterApiController::class, 'store']);
    Route::post('site-update', [SiteMasterApiController::class, 'update']);
    Route::delete('site-delete', [SiteMasterApiController::class, 'destroy']);

});
