<?php

use Illuminate\Support\Facades\Route;
use Modules\Attendance\Http\Controllers\AttendanceController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('attendances', AttendanceController::class)->names('attendance');

    Route::post('labour-dropdown', [AttendanceController::class, 'labourDropdown']);
    Route::post('attendance-add', [AttendanceController::class, 'store']);
    Route::post('attendance-list', [AttendanceController::class, 'index']);

});
