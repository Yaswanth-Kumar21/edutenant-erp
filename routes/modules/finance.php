<?php

use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\IncomeController;

/*
|--------------------------------------------------------------------------
| Finance Module Routes
|--------------------------------------------------------------------------
*/

Route::resource('expenses', ExpenseController::class);
Route::resource('incomes', IncomeController::class);
