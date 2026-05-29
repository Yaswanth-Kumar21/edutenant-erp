<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — EduTenant ERP
|--------------------------------------------------------------------------
|
| Version: v1
| Auth: Laravel Sanctum (Bearer token)
| Tenant isolation: enforced per user's tenant_id
|
| Base URL: /api/v1/
|
*/

Route::prefix('v1')->name('api.v1.')->group(function () {

    // ── Public: Authentication ────────────────────────────────────────────
    Route::post('login',  [AuthController::class, 'login'])->name('login');

    // ── Protected: Requires valid Sanctum token ───────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('me',      [AuthController::class, 'me'])->name('me');

        // ── Student Portal API (student role only) ────────────────────────
        Route::middleware('role:student')
            ->prefix('student')
            ->name('student.')
            ->group(function () {
                Route::get('dashboard',     [StudentApiController::class, 'dashboard'])->name('dashboard');
                Route::get('fees',          [StudentApiController::class, 'fees'])->name('fees');
                Route::get('attendance',    [StudentApiController::class, 'attendance'])->name('attendance');
                Route::get('notifications', [StudentApiController::class, 'notifications'])->name('notifications');
            });
    });
});
