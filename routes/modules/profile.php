<?php

use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminSettingsController;

/*
|--------------------------------------------------------------------------
| Profile & Settings Routes — All Authenticated Roles
|--------------------------------------------------------------------------
| These routes work for: super_admin, college_admin, staff, teacher
| Prefix: /admin  Name: admin.
*/

// ── Profile ───────────────────────────────────────────────────────────────────
Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('/',              [AdminProfileController::class, 'index'])->name('index');
    Route::post('/photo',        [AdminProfileController::class, 'updatePhoto'])->name('photo');
    Route::put('/info',          [AdminProfileController::class, 'updateInfo'])->name('info');
    Route::put('/password',      [AdminProfileController::class, 'updatePassword'])->name('password');
});

// ── Settings ──────────────────────────────────────────────────────────────────
Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/',              [AdminSettingsController::class, 'index'])->name('index');
    Route::put('/general',       [AdminSettingsController::class, 'updateGeneral'])->name('general');
    Route::put('/notifications', [AdminSettingsController::class, 'updateNotifications'])->name('notifications');
    Route::put('/security',      [AdminSettingsController::class, 'updateSecurity'])->name('security');
    Route::put('/smtp',          [AdminSettingsController::class, 'updateSmtp'])->name('smtp');
    Route::put('/integrations',  [AdminSettingsController::class, 'updateIntegrations'])->name('integrations');
});
