<?php

use Illuminate\Support\Facades\Route;
use Modules\Party\Http\Controllers\PartyController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('parties', PartyController::class)->names('party');
});
