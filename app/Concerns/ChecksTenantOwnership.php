<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Tenant;

trait ChecksTenantOwnership
{
    protected function belongsToCurrentTenant(Model $model): bool
    {
        if (! method_exists($model, 'getAttribute')) {
            return true;
        }

        $tenantId = $model->getAttribute('account_tenant_id');

        if ($tenantId === null) {
            return true;
        }

        $currentTenant = Tenant::current();

        return $currentTenant !== null && $tenantId === $currentTenant->id;
    }
}
