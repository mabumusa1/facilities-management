<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\PasswordValidationRules;
use App\Enums\RolesEnum;
use App\Http\Controllers\Controller;
use App\Models\AccountMembership;
use App\Models\Building;
use App\Models\Community;
use App\Models\Role;
use App\Models\ServiceManagerType;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function show(User $user): Response
    {
        $tenant = Tenant::current();
        abort_unless($tenant !== null, 404);

        $this->authorize('manage-user-role-assignments', $user);

        $roles = Role::get()->map(fn (Role $role): array => [
            'id' => $role->id,
            'name' => $role->name,
            'name_en' => $role->name_en,
            'name_ar' => $role->name_ar,
            'scope_level' => RolesEnum::tryFrom($role->name)?->scopeLevel() ?? 'none',
        ])->values();

        $communities = Community::get(['id', 'name'])->values();
        $buildings = Building::get(['id', 'name', 'rf_community_id'])->values();
        $serviceTypes = ServiceManagerType::get(['id', 'name'])->values();

        $modelHasRoles = config('permission.table_names.model_has_roles');

        return Inertia::render('admin/users/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'roles' => $roles,
            'communities' => $communities,
            'buildings' => $buildings,
            'serviceTypes' => $serviceTypes,
            'assignments' => Inertia::defer(fn () => DB::table($modelHasRoles)
                ->where("{$modelHasRoles}.model_type", User::class)
                ->where("{$modelHasRoles}.model_id", $user->id)
                ->leftJoin('roles', 'roles.id', '=', "{$modelHasRoles}.role_id")
                ->leftJoin('rf_communities', 'rf_communities.id', '=', "{$modelHasRoles}.community_id")
                ->leftJoin('rf_buildings', 'rf_buildings.id', '=', "{$modelHasRoles}.building_id")
                ->leftJoin('rf_service_manager_types', 'rf_service_manager_types.id', '=', "{$modelHasRoles}.service_type_id")
                ->select([
                    "{$modelHasRoles}.id",
                    "{$modelHasRoles}.role_id",
                    'roles.name_en as role_name_en',
                    'roles.name_ar as role_name_ar',
                    "{$modelHasRoles}.community_id",
                    'rf_communities.name as community_name',
                    "{$modelHasRoles}.building_id",
                    'rf_buildings.name as building_name',
                    "{$modelHasRoles}.service_type_id",
                    'rf_service_manager_types.name as service_type_name',
                ])
                ->orderBy("{$modelHasRoles}.id")
                ->get()),
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

        $user = $membership->user;
        if ($user !== null) {
            // Remove only null-scope base role rows before re-assigning, so that
            // scoped role rows (manager/serviceManager) are preserved.
            // We use removeScopedRole() with all-null scope tuple to avoid
            // the LogicException guard in the overridden removeRole().
            $mhrTable = config('permission.table_names.model_has_roles');
            $nullScopeRoleNames = DB::table($mhrTable)
                ->join('roles', 'roles.id', '=', "{$mhrTable}.role_id")
                ->where("{$mhrTable}.model_type", User::class)
                ->where("{$mhrTable}.model_id", $user->id)
                ->whereNull("{$mhrTable}.community_id")
                ->whereNull("{$mhrTable}.building_id")
                ->whereNull("{$mhrTable}.service_type_id")
                ->pluck('roles.name');

            foreach ($nullScopeRoleNames as $roleName) {
                $user->removeScopedRole($roleName, null, null, null);
            }

            $user->assignRole($validated['role']);
        }

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
