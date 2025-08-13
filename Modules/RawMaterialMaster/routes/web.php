<?php

use Illuminate\Support\Facades\Route;
use Modules\RawMaterialMaster\Http\Controllers\RawMaterialMasterController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('rawmaterialmasters', RawMaterialMasterController::class)->names('rawmaterialmaster');
});
