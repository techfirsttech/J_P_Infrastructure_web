<?php

use Illuminate\Support\Facades\Route;
use Modules\Contractor\Http\Controllers\ContractorController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('contractors', ContractorController::class)->names('contractor');
});
