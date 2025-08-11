<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('users', UserController::class);
    Route::post('assign-user', [UserController::class, 'assignUserWise'])->name('assign-user');
    Route::post('assign-user-store', [UserController::class, 'assignUserStore'])->name('assign-user-store');
    Route::post('assign-user-delete', [UserController::class, 'assignUserRemove'])->name('assign-user-delete');
    Route::post('change-password', [UserController::class, 'changePassword'])->name('change-password');
    Route::get('change/lang', [UserController::class, 'language'])->name('language');
    Route::get('year/lang', [UserController::class, 'yearChange'])->name('years');
    Route::post('change-layout', [UserController::class, 'changeLayout'])->name('change-layout');

    Route::post('user-login-status-change', [UserController::class, 'statusChange'])->name('user-login-status-change');


});
