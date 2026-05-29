<?php

use App\Http\Controllers\SuperAdmin\TenantController;

/*
|--------------------------------------------------------------------------
| Tenant Module Routes
|--------------------------------------------------------------------------
| Super admin tenant management routes.
*/

Route::middleware(['role:super_admin'])
    ->prefix('super-admin')
    ->name('super.')
    ->group(function () {
        Route::resource('tenants', TenantController::class);
        Route::post('tenants/{tenant}/switch', [TenantController::class, 'switchTenant'])
            ->name('tenants.switch');
    });
