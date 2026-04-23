<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRoleAssignmentRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\Permission\PermissionRegistrar;

class UserRoleAssignmentController extends Controller
{
    public function store(StoreUserRoleAssignmentRequest $request, User $user): RedirectResponse
    {
        $this->authorize('manage-user-role-assignments', $user);

        $validated = $request->validated();

        $modelHasRoles = config('permission.table_names.model_has_roles');

        // Duplicate guard
        $exists = DB::table($modelHasRoles)
            ->where('role_id', $validated['role_id'])
            ->where('model_type', User::class)
            ->where('model_id', $user->id)
            ->where('community_id', $validated['community_id'] ?? null)
            ->where('building_id', $validated['building_id'] ?? null)
            ->where('service_type_id', $validated['service_type_id'] ?? null)
            ->exists();

        if ($exists) {
            return back()->withErrors(['role_id' => __('This role assignment already exists.')]);
        }

        DB::table($modelHasRoles)->insert([
            'role_id' => $validated['role_id'],
            'model_type' => User::class,
            'model_id' => $user->id,
            'community_id' => $validated['community_id'] ?? null,
            'building_id' => $validated['building_id'] ?? null,
            'service_type_id' => $validated['service_type_id'] ?? null,
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Role assigned successfully.'),
        ]);

        return to_route('admin.users.show', $user);
    }

    public function destroy(User $user, int $assignment): RedirectResponse
    {
        $this->authorize('manage-user-role-assignments', $user);

        $modelHasRoles = config('permission.table_names.model_has_roles');

        $row = DB::table($modelHasRoles)
            ->where('id', $assignment)
            ->where('model_type', User::class)
            ->where('model_id', $user->id)
            ->first();

        abort_if($row === null, 404);

        $user->removeScopedRole(
            $row->role_id,
            $row->community_id,
            $row->building_id,
            $row->service_type_id,
        );

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Role assignment removed.'),
        ]);

        return to_route('admin.users.show', $user);
    }
}
