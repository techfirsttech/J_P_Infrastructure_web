<?php

use Illuminate\Support\Facades\Route;
use Modules\Report\Http\Controllers\ReportController;

Route::middleware(['web', 'auth', 'verified'])->group(function () {
    Route::get('report', [ReportController::class, 'index'])->name('report');
    Route::get('report-ledger', [ReportController::class, 'ledgerReport'])->name('report-ledger');
    Route::get('report-ledger-pdf', [ReportController::class, 'ledgerPdf'])->name('report-ledger-pdf');
    Route::get('report-attendance', [ReportController::class, 'attendanceReport'])->name('report-attendance');
    Route::get('report-attendance-pdf', [ReportController::class, 'attendancePdf'])->name('report-attendance-pdf');

    Route::get('get-site-supervisor', [ReportController::class, 'getSupervisor'])->name('get-site-supervisor');

});
