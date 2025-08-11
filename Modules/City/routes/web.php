<?php

use Illuminate\Support\Facades\Route;
use Modules\City\Http\Controllers\CityController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('cities', CityController::class)->names('city');
    Route::post('change-city', [CityController::class, 'show'])->name('change-city');
    Route::get('/get-cities/{state_id}', [CityController::class, 'getCities'])->name('get.cities');
});