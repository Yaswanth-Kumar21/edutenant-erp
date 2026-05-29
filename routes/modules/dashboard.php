<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Student\StudentAttendanceController;
use App\Http\Controllers\Student\StudentCertificateController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentFeeController;
use App\Http\Controllers\Student\StudentProfileController;

/*
|--------------------------------------------------------------------------
| Dashboard Module Routes
|--------------------------------------------------------------------------
|
| /dashboard          → super_admin, college_admin, staff, teacher
| /student/*          → student only
|
*/

// ── Admin / Super Admin Dashboard ─────────────────────────────────────────────
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['tenant', 'role:super_admin,college_admin,staff,teacher'])
    ->name('dashboard');

// ── Student Portal ────────────────────────────────────────────────────────────
Route::middleware(['tenant', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

        // Dashboard
        Route::get('dashboard', [StudentDashboardController::class, 'index'])
            ->name('dashboard');

        // Fee History
        Route::get('fees', [StudentFeeController::class, 'index'])->name('fees.index');
        Route::get('fees/{payment}', [StudentFeeController::class, 'show'])->name('fees.show');
        Route::get('fees/{payment}/receipt', [StudentFeeController::class, 'downloadReceipt'])->name('fees.receipt');

        // Attendance Calendar
        Route::get('attendance', [StudentAttendanceController::class, 'index'])->name('attendance.index');

        // Certificates
        Route::get('certificates', [StudentCertificateController::class, 'index'])->name('certificates.index');
        Route::get('certificates/{certificate}/download', [StudentCertificateController::class, 'download'])->name('certificates.download');

        // Profile
        Route::get('profile', [StudentProfileController::class, 'index'])->name('profile.index');
        Route::get('profile/password', [StudentProfileController::class, 'editPassword'])->name('profile.password');
        Route::put('profile/password', [StudentProfileController::class, 'updatePassword'])->name('profile.password.update');

        // Settings
        Route::get('settings', function () {
            $user    = auth()->user();
            $student = $user->student;
            return view('student.settings', compact('user', 'student'));
        })->name('settings');
    });
