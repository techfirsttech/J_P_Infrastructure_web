<?php

use Illuminate\Support\Facades\Route;
use Modules\Attendance\Http\Controllers\AttendanceApiController;
use Modules\Attendance\Http\Controllers\AttendanceController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('attendances', AttendanceController::class)->names('attendance');

    Route::post('labour-dropdown', [AttendanceApiController::class, 'labourDropdown']);
    Route::post('attendance-add', [AttendanceApiController::class, 'store']);
    Route::post('attendance-list', [AttendanceApiController::class, 'index']);
    // Route::post('attendance-list', [AttendanceController::class, 'index']);

});
