<?php

use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\StaffController;

/*
|--------------------------------------------------------------------------
| Staff Module Routes
|--------------------------------------------------------------------------
*/

// ── Leave Management (MUST be defined BEFORE resource to avoid conflict) ──
Route::prefix('staff')->name('staff.')->group(function () {
    Route::get('leaves',                    [StaffController::class, 'leaves'])->name('leaves');
    Route::post('leaves',                   [StaffController::class, 'storeLeave'])->name('leaves.store');
    Route::patch('leaves/{leave}/approve',  [StaffController::class, 'approveLeave'])->name('leaves.approve');
    Route::patch('leaves/{leave}/reject',   [StaffController::class, 'rejectLeave'])->name('leaves.reject');

    // Staff Roles (also before resource)
    Route::get('roles',                     [StaffController::class, 'roles'])->name('roles');
    Route::post('roles',                    [StaffController::class, 'storeRole'])->name('roles.store');
});

// Staff CRUD (resource defined AFTER custom routes to avoid URL conflicts)
Route::resource('staff', StaffController::class);

// Payroll
Route::prefix('payroll')->name('payroll.')->group(function () {
    Route::get('/',                         [PayrollController::class, 'index'])->name('index');
    Route::get('generate',                  [PayrollController::class, 'generate'])->name('generate');
    Route::post('/',                        [PayrollController::class, 'store'])->name('store');
    Route::get('summary/month',             [PayrollController::class, 'summary'])->name('summary');
    Route::get('{payroll}',                 [PayrollController::class, 'show'])->name('show');
    Route::get('{payroll}/slip',            [PayrollController::class, 'slip'])->name('slip');
    Route::patch('{payroll}/approve',       [PayrollController::class, 'approve'])->name('approve');
    Route::patch('{payroll}/mark-paid',     [PayrollController::class, 'markPaid'])->name('mark-paid');
});
