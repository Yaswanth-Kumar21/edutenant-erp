<?php

use App\Http\Controllers\Admin\ReportController;

/*
|--------------------------------------------------------------------------
| Reports Module Routes
|--------------------------------------------------------------------------
*/

Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('daily',    [ReportController::class, 'daily'])->name('daily');
    Route::get('annual',   [ReportController::class, 'annual'])->name('annual');
    Route::get('students', [ReportController::class, 'students'])->name('students');
    Route::get('fees',     [ReportController::class, 'fees'])->name('fees');
});
