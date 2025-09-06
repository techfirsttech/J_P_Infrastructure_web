<?php

use Illuminate\Support\Facades\Route;
use Modules\Party\Http\Controllers\PartyApiController;
use Modules\Party\Http\Controllers\PartyController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('parties', PartyController::class)->names('party');

    Route::post('party-dropdown',[PartyApiController::class,'index']);
    Route::post('party-add',[PartyApiController::class,'store']);
});
