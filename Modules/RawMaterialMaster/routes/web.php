<?php

use Illuminate\Support\Facades\Route;
use Modules\RawMaterialMaster\Http\Controllers\RawMaterialMasterController;
use Modules\RawMaterialMaster\Models\RawMaterialStockTransaction;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('rawmaterialmasters', RawMaterialMasterController::class)->names('rawmaterialmaster');
    Route::get('transaction',[RawMaterialMasterController::class, 'materialTransaction'])->name('transaction');

    Route::delete('transaction-delete/{id}',[RawMaterialMasterController::class, 'delete'])->name('transaction-delete');

});
