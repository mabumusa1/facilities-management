<?php

namespace App\Http\Responses;

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

        return redirect()->intended(config('fortify.home'));
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
