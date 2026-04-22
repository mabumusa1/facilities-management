<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\PasswordValidationRules;
use App\Enums\RolesEnum;
use App\Http\Controllers\Controller;
use App\Models\AccountMembership;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AccountUserController extends Controller
{
    use PasswordValidationRules;

    public function index(): Response
    {
        $tenant = Tenant::current();
        abort_unless($tenant !== null, 404);

        $memberships = AccountMembership::query()
            ->with('user:id,name,email')
            ->where('account_tenant_id', $tenant->id)
            ->latest('id')
            ->paginate(15)
            ->through(fn (AccountMembership $membership): array => [
                'id' => $membership->id,
                'user_id' => $membership->user_id,
                'name' => $membership->user?->name,
                'email' => $membership->user?->email,
                'role' => $membership->role,
                'created_at' => $membership->created_at?->toJSON(),
            ]);

        return Inertia::render('admin/users/Index', [
            'memberships' => $memberships,
            'roles' => collect(RolesEnum::cases())
                ->map(fn (RolesEnum $role): array => [
                    'value' => $role->value,
                    'label' => $role->label(),
                ])
                ->values(),
            'currentTenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = Tenant::current();
        abort_unless($tenant !== null, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => $this->passwordRules(),
            'role' => ['required', Rule::in(array_map(static fn (RolesEnum $role): string => $role->value, RolesEnum::cases()))],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => $validated['role'],
        ]);

        $user->syncRoles([$validated['role']]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Account user created.'),
        ]);

        return to_route('admin.users.index');
    }

    public function update(Request $request, AccountMembership $membership): RedirectResponse
    {
        $this->ensureMembershipBelongsToCurrentTenant($membership);

        $validated = $request->validate([
            'role' => ['required', Rule::in(array_map(static fn (RolesEnum $role): string => $role->value, RolesEnum::cases()))],
        ]);

        $membership->update([
            'role' => $validated['role'],
        ]);

        $membership->user?->syncRoles([$validated['role']]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Account user role updated.'),
        ]);

        return back();
    }

    public function destroy(Request $request, AccountMembership $membership): RedirectResponse
    {
        $this->ensureMembershipBelongsToCurrentTenant($membership);

        if ((int) $membership->user_id === (int) $request->user()?->id) {
            Inertia::flash('toast', [
                'type' => 'warning',
                'message' => __('You cannot remove your own account access.'),
            ]);

            return back();
        }

        $membership->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Account user removed.'),
        ]);

        return back();
    }

    private function ensureMembershipBelongsToCurrentTenant(AccountMembership $membership): void
    {
        $tenant = Tenant::current();

        abort_unless(
            $tenant !== null && (int) $membership->account_tenant_id === (int) $tenant->id,
            403,
        );
    }
}
