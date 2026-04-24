<?php

namespace App\Http\Responses;

use App\Enums\RolesEnum;
use App\Models\AccountMembership;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        $this->setTenantForUser($request);

        if ($request->wantsJson()) {
            return new JsonResponse('', 204);
        }

        return redirect()->intended($this->resolveRedirectUrl());
    }

    /**
     * Resolve the post-login redirect URL based on the authenticated user's primary role.
     *
     * Priority order (first match wins):
     *   1. Account Admins / Admins → /dashboard (admin area)
     *   2. Managers              → /dashboard
     *   3. Owners                → /dashboard (TODO: replace with route('owner.index') once #225 lands)
     *   4. Tenants / Dependents  → /dashboard (TODO: replace with route('resident.index') once portal story lands)
     *   5. Professionals         → /dashboard (TODO: replace with role-specific route once portal story lands)
     *   6. Fallback              → config('fortify.home') (/dashboard)
     *
     * When the dedicated resident/owner portal routes exist, only the matching
     * case below needs updating — no structural change to this method.
     */
    protected function resolveRedirectUrl(): string
    {
        /** @var User $user */
        $user = auth()->user();
        $roles = $user->getRoleNames()->toArray();

        if (
            in_array(RolesEnum::ACCOUNT_ADMINS->value, $roles, strict: true) ||
            in_array(RolesEnum::ADMINS->value, $roles, strict: true)
        ) {
            return route('dashboard', absolute: false);
        }

        if (in_array(RolesEnum::MANAGERS->value, $roles, strict: true)) {
            return route('dashboard', absolute: false);
        }

        // TODO: replace with route('owner.index', absolute: false) once the owner-portal story lands
        if (in_array(RolesEnum::OWNERS->value, $roles, strict: true)) {
            return config('fortify.home');
        }

        // TODO: replace with route('resident.index', absolute: false) once the resident-portal story lands
        if (
            in_array(RolesEnum::TENANTS->value, $roles, strict: true) ||
            in_array(RolesEnum::DEPENDENTS->value, $roles, strict: true)
        ) {
            return config('fortify.home');
        }

        return config('fortify.home');
    }

    protected function setTenantForUser($request): void
    {
        /** @var User $user */
        $user = auth()->user();

        $membership = AccountMembership::where('user_id', $user->id)->first();

        if (! $membership) {
            return;
        }

        $tenant = Tenant::find($membership->account_tenant_id);

        if ($tenant) {
            $request->session()->put('tenant_id', $tenant->id);
            $tenant->makeCurrent();
        }
    }
}
