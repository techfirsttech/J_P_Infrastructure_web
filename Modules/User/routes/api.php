<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserApiController;
use Modules\User\Http\Controllers\UserController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('users', UserController::class)->names('user');
    Route::get('dashboard', [UserApiController::class, 'dashboard']);
    Route::post('supervisor-list', [UserApiController::class, 'supervisorList']);
    Route::post('site-supervisor-list', [UserApiController::class, 'siteSupervisorList']);
    Route::post('all-user-dropdown', [UserApiController::class, 'userDropdown']);
});
