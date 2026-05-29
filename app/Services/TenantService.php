<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;

/**
 * TenantService
 *
 * Central service for resolving and managing the current tenant context.
 * Supports resolution by subdomain, custom domain, or session.
 */
class TenantService
{
    protected static ?Tenant $currentTenant = null;

    /**
     * Set the active tenant for this request.
     */
    public static function setTenant(Tenant $tenant): void
    {
        static::$currentTenant = $tenant;
        app()->instance('current_tenant', $tenant);
    }

    /**
     * Get the currently active tenant.
     */
    public static function getTenant(): ?Tenant
    {
        return static::$currentTenant ?? app()->bound('current_tenant')
            ? app('current_tenant')
            : null;
    }

    /**
     * Resolve tenant from HTTP request.
     * Priority: custom domain → subdomain → session (for super admin switching)
     */
    public static function resolveFromRequest(\Illuminate\Http\Request $request): ?Tenant
    {
        $host = $request->getHost();

        // 1. Try custom domain match
        $tenant = Cache::remember("tenant_domain_{$host}", 300, function () use ($host) {
            return Tenant::where('domain', $host)->where('status', 'active')->first();
        });

        if ($tenant) {
            return $tenant;
        }

        // 2. Try subdomain match (e.g. college1.edutenant.com)
        $parts = explode('.', $host);
        if (count($parts) >= 3) {
            $slug = $parts[0];
            $tenant = Cache::remember("tenant_slug_{$slug}", 300, function () use ($slug) {
                return Tenant::where('slug', $slug)->where('status', 'active')->first();
            });
            if ($tenant) return $tenant;
        }

        // 3. Try session-based tenant (super admin panel switching)
        if (session()->has('tenant_id')) {
            $tenantId = session('tenant_id');
            return Cache::remember("tenant_id_{$tenantId}", 300, function () use ($tenantId) {
                return Tenant::where('id', $tenantId)->where('status', 'active')->first();
            });
        }

        return null;
    }

    /**
     * Resolve tenant from authenticated user.
     */
    public static function resolveFromUser(\App\Models\User $user): ?Tenant
    {
        if ($user->isSuperAdmin()) return null;
        return $user->tenant;
    }

    /**
     * Clear tenant cache.
     */
    public static function clearCache(Tenant $tenant): void
    {
        Cache::forget("tenant_domain_{$tenant->domain}");
        Cache::forget("tenant_slug_{$tenant->slug}");
        Cache::forget("tenant_id_{$tenant->id}");
    }

    /**
     * Get current tenant ID safely.
     */
    public static function getTenantId(): ?int
    {
        return static::getTenant()?->id;
    }
}
