<?php

use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\IncomeController;
use App\Http\Controllers\Admin\PaymentRegisterController;

/*
|--------------------------------------------------------------------------
| Finance Module Routes
|--------------------------------------------------------------------------
*/

Route::resource('expenses', ExpenseController::class);
Route::resource('incomes', IncomeController::class);

// Payments Register (institutional ledger with 20 fixed payment categories)
Route::get('payment-register/report', [PaymentRegisterController::class, 'report'])
    ->name('payment-register.report');
Route::resource('payment-register', PaymentRegisterController::class);
