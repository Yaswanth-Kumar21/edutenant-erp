<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — EduTenant ERP
|--------------------------------------------------------------------------
*/

// ── Root Redirect ─────────────────────────────────────────────────────────────
Route::get('/', function () {
    if (!auth()->check()) return redirect()->route('login');
    $user = auth()->user();
    if ($user->isSuperAdmin()) return redirect()->route('dashboard');
    if ($user->isStudent())    return redirect()->route('student.dashboard');
    return redirect()->route('dashboard');
});

// ── Auth Routes (Laravel Breeze) ──────────────────────────────────────────────
require __DIR__ . '/auth.php';

// ── Authenticated Routes ──────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // ── Dashboard Routes (role-based) ─────────────────────────────────────
    require __DIR__ . '/modules/dashboard.php';

    // ── Profile & Settings — all authenticated roles (no tenant required for super admin) ──
    Route::prefix('admin')->name('admin.')->group(function () {
        require __DIR__ . '/modules/profile.php';
    });

    // ── Admin Panel — college_admin, staff, teacher only ─────────────────
    Route::middleware(['tenant', 'role:college_admin,staff,teacher'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            require __DIR__ . '/modules/setup.php';
            require __DIR__ . '/modules/admission.php';
            require __DIR__ . '/modules/students.php';
            require __DIR__ . '/modules/fees.php';
            require __DIR__ . '/modules/attendance.php';
            require __DIR__ . '/modules/staff.php';
            require __DIR__ . '/modules/finance.php';
            require __DIR__ . '/modules/reports.php';
            require __DIR__ . '/modules/notifications.php';
            require __DIR__ . '/modules/exports.php';
        });

    // ── Super Admin Routes ────────────────────────────────────────────────
    require __DIR__ . '/modules/tenant.php';
});

// ── Razorpay Webhook (no auth, no CSRF) ──────────────────────────────────────
Route::post('/webhook/razorpay', [\App\Http\Controllers\Admin\PaymentGatewayController::class, 'webhook'])
    ->name('webhook.razorpay')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
