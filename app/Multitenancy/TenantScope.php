<?php

declare(strict_types=1);

namespace App\Multitenancy;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Global scope that automatically filters queries by the current tenant.
 *
 * When applied to a model, all queries will automatically be filtered
 * to only include records belonging to the current tenant.
 */
class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $tenantContext = app(TenantContext::class);

        if ($tenantContext->has()) {
            $builder->where($model->getTable().'.tenant_id', $tenantContext->id());
        }
    }

    /**
     * Extend the query builder with the needed functions.
     */
    public function extend(Builder $builder): void
    {
        $builder->macro('withoutTenantScope', function (Builder $builder) {
            return $builder->withoutGlobalScope(self::class);
        });

        $builder->macro('forTenant', function (Builder $builder, int $tenantId) {
            return $builder->withoutGlobalScope(self::class)
                ->where($builder->getModel()->getTable().'.tenant_id', $tenantId);
        });
    }
}
