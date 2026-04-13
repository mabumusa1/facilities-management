<?php

declare(strict_types=1);

namespace App\Multitenancy;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait for models that belong to a tenant.
 *
 * This trait:
 * - Adds the tenant relationship
 * - Applies the TenantScope global scope
 * - Automatically sets tenant_id when creating records
 */
trait BelongsToTenant
{
    /**
     * Boot the belongs to tenant trait.
     */
    public static function bootBelongsToTenant(): void
    {
        // Apply tenant scope for filtering queries
        static::addGlobalScope(new TenantScope);

        // Automatically set tenant_id when creating records
        static::creating(function (Model $model) {
            if (! $model->tenant_id) {
                $tenantContext = app(TenantContext::class);
                if ($tenantContext->has()) {
                    $model->tenant_id = $tenantContext->id();
                }
            }
        });
    }

    /**
     * Get the tenant that owns this model.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scope to query without tenant filtering.
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeWithoutTenant($query)
    {
        return $query->withoutGlobalScope(TenantScope::class);
    }

    /**
     * Scope to query for a specific tenant.
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeForTenant($query, Tenant|int $tenant)
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;

        return $query->withoutGlobalScope(TenantScope::class)
            ->where($this->getTable().'.tenant_id', $tenantId);
    }
}
