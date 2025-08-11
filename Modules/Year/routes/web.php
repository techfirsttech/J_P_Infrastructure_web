<?php

use Illuminate\Support\Facades\Route;
use Modules\Year\Http\Controllers\YearController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('years', YearController::class)->names('year');
});
