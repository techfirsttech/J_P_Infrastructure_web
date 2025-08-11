<?php

use Illuminate\Support\Facades\Route;
use Modules\SiteMaster\Http\Controllers\SiteMasterController;
use Modules\State\Http\Controllers\StateController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('sitemasters', SiteMasterController::class)->names('sitemaster');
});