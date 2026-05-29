<?php

use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\StaffController;

/*
|--------------------------------------------------------------------------
| Staff Module Routes
|--------------------------------------------------------------------------
*/

// Staff CRUD
Route::resource('staff', StaffController::class);

// Leave Management
Route::prefix('staff')->name('staff.')->group(function () {
    Route::get('leaves',                    [StaffController::class, 'leaves'])->name('leaves');
    Route::post('leaves',                   [StaffController::class, 'storeLeave'])->name('leaves.store');
    Route::patch('leaves/{leave}/approve',  [StaffController::class, 'approveLeave'])->name('leaves.approve');
    Route::patch('leaves/{leave}/reject',   [StaffController::class, 'rejectLeave'])->name('leaves.reject');

    // Staff Roles
    Route::get('roles',                     [StaffController::class, 'roles'])->name('roles');
    Route::post('roles',                    [StaffController::class, 'storeRole'])->name('roles.store');
});

// Payroll
Route::prefix('payroll')->name('payroll.')->group(function () {
    Route::get('/',                         [PayrollController::class, 'index'])->name('index');
    Route::get('generate',                  [PayrollController::class, 'generate'])->name('generate');
    Route::post('/',                        [PayrollController::class, 'store'])->name('store');
    Route::get('{payroll}',                 [PayrollController::class, 'show'])->name('show');
    Route::get('{payroll}/slip',            [PayrollController::class, 'slip'])->name('slip');
    Route::patch('{payroll}/approve',       [PayrollController::class, 'approve'])->name('approve');
    Route::patch('{payroll}/mark-paid',     [PayrollController::class, 'markPaid'])->name('mark-paid');
    Route::get('summary/month',             [PayrollController::class, 'summary'])->name('summary');
});
