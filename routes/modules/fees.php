<?php

use App\Http\Controllers\Admin\FeePaymentController;
use App\Http\Controllers\Admin\FeeTypeController;
use App\Http\Controllers\Admin\Fees\FeeDashboardController;
use App\Http\Controllers\Admin\Fees\FeeAssignmentController;
use App\Http\Controllers\Admin\Fees\FeeCollectionController;
use App\Http\Controllers\Admin\Fees\FeeExemptionController;
use App\Http\Controllers\Admin\Fees\FeeReceiptController;
use App\Http\Controllers\Admin\Fees\FeeStructureController;
use App\Http\Controllers\Admin\Fees\TransportFeeController;

/*
|--------------------------------------------------------------------------
| Fees Module Routes
|--------------------------------------------------------------------------
*/

Route::prefix('fees')->name('fees.')->group(function () {

    // Fee Dashboard
    Route::get('dashboard', [FeeDashboardController::class, 'index'])->name('dashboard');

    // Fee Types
    Route::resource('types', FeeTypeController::class);

    // Fee Structures
    Route::resource('structures', FeeStructureController::class);

    // Fee Assignments
    Route::get('assignments', [FeeAssignmentController::class, 'index'])->name('assignments.index');
    Route::post('assignments/bulk', [FeeAssignmentController::class, 'bulkAssign'])->name('assignments.bulk');
    Route::post('assignments/{student}', [FeeAssignmentController::class, 'assignToStudent'])->name('assignments.student');

    // Fee Collection (payments)
    Route::resource('payments', FeeCollectionController::class);
    Route::get('payments/{payment}/receipt', [FeeCollectionController::class, 'receipt'])->name('payments.receipt');
    Route::get('payments/{payment}/receipt/print', [FeeCollectionController::class, 'printReceipt'])->name('payments.receipt.print');

    // Legacy receipt route (backward compat)
    Route::get('receipt/{payment}', [FeePaymentController::class, 'receipt'])->name('receipt');

    // Fee Exemptions
    Route::resource('exemptions', FeeExemptionController::class)->only(['index', 'store', 'destroy']);

    // Transport / Vehicle Fees
    Route::prefix('transport')->name('transport.')->group(function () {
        Route::get('/', [TransportFeeController::class, 'index'])->name('index');
        Route::post('collect/{student}', [TransportFeeController::class, 'collect'])->name('collect');
        Route::get('summary', [TransportFeeController::class, 'summary'])->name('summary');
    });

    // Student Fee Profile
    Route::get('student/{student}', [FeeDashboardController::class, 'studentProfile'])->name('student.profile');
});
