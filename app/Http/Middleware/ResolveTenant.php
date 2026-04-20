<?php

namespace App\Http\Middleware;

use App\Models\AccountMembership;
use App\Models\Tenant;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Tenant::current()) {
            return $next($request);
        }

        if (auth()->check()) {
            $this->setTenantFromSession($request) || $this->setDefaultTenant($request);
        }

        return $next($request);
    }

    protected function setTenantFromSession(Request $request): bool
    {
        $tenantId = $request->session()->get('tenant_id');

        if (! $tenantId) {
            return false;
        }

        $tenant = Tenant::find($tenantId);

        if ($tenant) {
            $tenant->makeCurrent();

            return true;
        }

        return false;
    }

    protected function setDefaultTenant(Request $request): bool
    {
        /** @var User $user */
        $user = auth()->user();

        $membership = AccountMembership::where('user_id', $user->id)->first();

        if (! $membership) {
            return false;
        }

        $tenant = Tenant::find($membership->account_tenant_id);

        if (! $tenant) {
            return false;
        }

        $request->session()->put('tenant_id', $tenant->id);
        $tenant->makeCurrent();

        return true;
    }
}
