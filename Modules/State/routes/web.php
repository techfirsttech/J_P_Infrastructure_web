<?php

use Illuminate\Support\Facades\Route;
use Modules\State\Http\Controllers\StateController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('states', StateController::class)->names('state');
    Route::post('change-state', [StateController::class, 'show'])->name('change-state');

});
