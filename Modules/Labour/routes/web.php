<?php

use Illuminate\Support\Facades\Route;
use Modules\Labour\Http\Controllers\LabourController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('labours', LabourController::class)->names('labour');
});
