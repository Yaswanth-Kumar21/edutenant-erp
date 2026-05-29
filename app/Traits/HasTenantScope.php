<?php

namespace App\Traits;

use App\Services\TenantService;
use Illuminate\Database\Eloquent\Builder;

/**
 * HasTenantScope Trait — for Eloquent Models
 *
 * Adds a local scope `forCurrentTenant()` to any model.
 * Usage: Model::forCurrentTenant()->get()
 *
 * NOTE: We intentionally do NOT use a global scope here because
 * super admins need to query across all tenants.
 */
trait HasTenantScope
{
    /**
     * Scope a query to the current tenant.
     */
    public function scopeForCurrentTenant(Builder $query): Builder
    {
        return $query->where('tenant_id', TenantService::getTenantId());
    }

    /**
     * Scope a query to a specific tenant.
     */
    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }
}
