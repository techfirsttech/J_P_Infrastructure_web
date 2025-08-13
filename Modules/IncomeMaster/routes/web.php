<?php

use Illuminate\Support\Facades\Route;
use Modules\IncomeMaster\Http\Controllers\IncomeMasterController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('incomemasters', IncomeMasterController::class)->names('incomemaster');
});
