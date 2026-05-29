<?php

use App\Http\Controllers\Admin\AttendanceController;

/*
|--------------------------------------------------------------------------
| Attendance Module Routes
|--------------------------------------------------------------------------
*/

Route::prefix('attendance')->name('attendance.')->group(function () {

    // Student Attendance
    Route::get('students',          [AttendanceController::class, 'students'])->name('students');
    Route::post('students/mark',    [AttendanceController::class, 'markStudents'])->name('students.mark');
    Route::get('students/report',   [AttendanceController::class, 'studentReport'])->name('students.report');
    Route::get('students/analytics',[AttendanceController::class, 'studentAnalytics'])->name('students.analytics');

    // Staff Attendance
    Route::get('staff',             [AttendanceController::class, 'staff'])->name('staff');
    Route::post('staff/mark',       [AttendanceController::class, 'markStaff'])->name('staff.mark');
    Route::get('staff/report',      [AttendanceController::class, 'staffReport'])->name('staff.report');
});
