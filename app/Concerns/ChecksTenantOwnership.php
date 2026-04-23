<?php

namespace App\Concerns;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;

trait ChecksTenantOwnership
{
    protected function belongsToCurrentTenant(Model $model): bool
    {
        if (! method_exists($model, 'getAttribute')) {
            return true;
        }

        $tenantId = $model->getAttribute('account_tenant_id');

        if ($tenantId === null) {
            return false;
        }

        $currentTenant = Tenant::current();

        return $currentTenant !== null && $tenantId === $currentTenant->id;
    }
}
