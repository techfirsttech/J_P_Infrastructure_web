<?php

use Illuminate\Support\Facades\Route;
use Modules\Attendance\Http\Controllers\AttendanceController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('attendances', AttendanceController::class)->names('attendance');

    Route::post('attendance-list', [AttendanceController::class, 'list'])->name('attendance-list');

    Route::get('get-labours', [AttendanceController::class, 'getLaboursByDateContractor'])->name('get-labours');
   
    Route::get('get-contractor', [AttendanceController::class, 'getContractor'])->name('get-contractor');
    Route::get('get-contractor-labour', [AttendanceController::class, 'getContractorLabour'])->name('get-contractor-labour');
});
