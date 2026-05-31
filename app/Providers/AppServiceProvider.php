<?php

namespace App\Providers;

use App\Services\AdmissionService;
use App\Services\AttendanceService;
use App\Services\DashboardService;
use App\Services\FeeAnalyticsService;
use App\Services\FeeAssignmentService;
use App\Services\FeeCollectionService;
use App\Services\StaffService;
use App\Services\TenantService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

/**
 * AppServiceProvider
 *
 * Registers all service bindings into the Laravel container.
 * Standard Laravel MVC architecture — all services in app/Services/.
 */
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Fee services
        $this->app->singleton(FeeCollectionService::class);
        $this->app->singleton(FeeAnalyticsService::class);
        $this->app->singleton(FeeAssignmentService::class);

        // Core services
        $this->app->singleton(AdmissionService::class);
        $this->app->singleton(AttendanceService::class);
        $this->app->singleton(StaffService::class);
        $this->app->singleton(DashboardService::class);
        $this->app->singleton(TenantService::class);
    }

    public function boot(): void
    {
        // Force HTTPS in production (Render deployment)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
