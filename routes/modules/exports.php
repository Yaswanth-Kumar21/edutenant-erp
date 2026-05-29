<?php

use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\PdfController;
use App\Http\Controllers\Admin\PaymentGatewayController;
use App\Http\Controllers\Admin\NotificationDispatchController;

/*
|--------------------------------------------------------------------------
| Exports, PDFs, Payment Gateway & Notification Dispatch Routes
|--------------------------------------------------------------------------
*/

// ── Excel / CSV Exports ───────────────────────────────────────────────────────
Route::prefix('exports')->name('exports.')->group(function () {
    Route::get('students',     [ExportController::class, 'students'])->name('students');
    Route::get('fee-payments', [ExportController::class, 'feePayments'])->name('fee-payments');
    Route::get('attendance',   [ExportController::class, 'attendance'])->name('attendance');
    Route::get('payroll',      [ExportController::class, 'payroll'])->name('payroll');
    Route::get('expenses',     [ExportController::class, 'expenses'])->name('expenses');
    Route::get('income',       [ExportController::class, 'income'])->name('income');
});

// ── PDF Downloads ─────────────────────────────────────────────────────────────
Route::prefix('pdf')->name('pdf.')->group(function () {
    Route::get('fee-receipt/{payment}',    [PdfController::class, 'feeReceipt'])->name('fee-receipt');
    Route::get('payroll-slip/{payroll}',   [PdfController::class, 'payrollSlip'])->name('payroll-slip');
    Route::get('student-report/{student}', [PdfController::class, 'studentReport'])->name('student-report');
    Route::get('admission/{student}',      [PdfController::class, 'admissionReceipt'])->name('admission-receipt');
    Route::get('attendance-report',        [PdfController::class, 'attendanceReport'])->name('attendance-report');
});

// ── Online Payment Gateway (Razorpay) ─────────────────────────────────────────
Route::prefix('payments')->name('payments.')->group(function () {
    Route::get('{payment}/pay-online',    [PaymentGatewayController::class, 'showPaymentPage'])->name('pay-online');
    Route::post('{payment}/create-order', [PaymentGatewayController::class, 'createOrder'])->name('create-order');
    Route::post('{payment}/verify',       [PaymentGatewayController::class, 'verifyPayment'])->name('verify');
});

// ── Email Notification Dispatch ───────────────────────────────────────────────
Route::prefix('notifications/send')->name('notifications.send.')->group(function () {
    Route::post('admission/{student}',  [NotificationDispatchController::class, 'sendAdmissionConfirmation'])->name('admission');
    Route::post('fee-receipt/{payment}',[NotificationDispatchController::class, 'sendFeeReceipt'])->name('fee-receipt');
    Route::post('payroll/{payroll}',    [NotificationDispatchController::class, 'sendPayrollNotification'])->name('payroll');
    Route::post('attendance-alerts',    [NotificationDispatchController::class, 'sendAttendanceAlerts'])->name('attendance-alerts');
});
