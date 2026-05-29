<?php

namespace App\Traits;

use App\Services\TenantService;

/**
 * TenantScoped Trait
 *
 * Provides tenant-isolation helpers for controllers.
 * Any controller that handles tenant-scoped data should use this trait.
 */
trait TenantScoped
{
    /**
     * Get the current tenant ID (safe, throws if missing).
     */
    protected function tenantId(): int
    {
        $id = TenantService::getTenantId();

        if (!$id) {
            abort(403, 'No active tenant context.');
        }

        return $id;
    }

    /**
     * Get the current tenant model.
     */
    protected function tenant(): \App\Models\Tenant
    {
        $tenant = TenantService::getTenant();

        if (!$tenant) {
            abort(403, 'No active tenant context.');
        }

        return $tenant;
    }

    /**
     * Assert a model belongs to the current tenant.
     * Aborts with 403 if it doesn't.
     */
    protected function assertTenant(\Illuminate\Database\Eloquent\Model $model): void
    {
        if (!isset($model->tenant_id)) {
            return; // Model doesn't have tenant_id — skip check
        }

        if ($model->tenant_id !== $this->tenantId()) {
            abort(403, 'Access denied: resource belongs to a different tenant.');
        }
    }
}
