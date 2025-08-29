<?php

use Illuminate\Support\Facades\Route;
use Modules\StockTransfer\Http\Controllers\StockTransferController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('stocktransfers', StockTransferController::class)->names('stocktransfer');

    Route::get('stock',[StockTransferController::class, 'StockList'])->name('stock');

});
