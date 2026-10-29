<?php

use App\Http\Controllers\SuperAdmin\TenantController;
use App\Http\Controllers\SuperAdmin\TenantUserController;

/*
|--------------------------------------------------------------------------
| Tenant Module Routes
|--------------------------------------------------------------------------
| Super admin tenant management routes.
|
| IMPORTANT: Specific nested routes must be defined BEFORE Route::resource()
| to prevent the resource's {tenant} wildcard from swallowing them.
*/

Route::middleware(['role:super_admin'])
    ->prefix('super-admin')
    ->name('super.')
    ->group(function () {

        // ── College Admin Management (defined BEFORE resource to avoid conflicts) ──
        Route::get('tenants/{tenant}/admin/create', [TenantUserController::class, 'create'])
            ->name('tenants.admin.create');
        Route::post('tenants/{tenant}/admin', [TenantUserController::class, 'store'])
            ->name('tenants.admin.store');
        Route::post('tenants/{tenant}/admin/{user}/reset-password', [TenantUserController::class, 'resetPassword'])
            ->name('tenants.admin.reset-password');

        // ── Resend Credentials Email ──────────────────────────────────────────
        Route::post('tenants/{tenant}/resend-credentials', [TenantController::class, 'resendCredentials'])
            ->name('tenants.resend-credentials');

        // ── Impersonation ─────────────────────────────────────────────────────
        Route::post('tenants/{tenant}/impersonate', [TenantController::class, 'impersonate'])
            ->name('tenants.impersonate');

        // ── Institution CRUD (resource defined AFTER specific routes) ─────────
        Route::resource('tenants', TenantController::class);
    });

// ── Stop Impersonating (accessible while impersonating, no super_admin role check) ──
Route::middleware(['auth'])
    ->post('super-admin/stop-impersonating', [TenantController::class, 'stopImpersonating'])
    ->name('super.stop-impersonating');
