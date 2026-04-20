<?php

namespace App\TenantFinder;

use Illuminate\Http\Request;
use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\TenantFinder\TenantFinder;

class HeaderTenantFinder extends TenantFinder
{
    public function findForRequest(Request $request): ?IsTenant
    {
        $tenantId = $request->header('X-Tenant');

        if (! $tenantId) {
            return null;
        }

        return app(IsTenant::class)::find($tenantId);
    }
}
