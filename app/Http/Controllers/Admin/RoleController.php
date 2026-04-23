<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Role::class);

        $search = request('search', '');
        $type = request('type', '');

        $roles = Role::withCount('users')
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%");
            }))
            ->when($type, fn ($q) => $q->where('type', $type))
            ->latest('id')
            ->paginate(25)
            ->through(fn (Role $role): array => [
                'id' => $role->id,
                'name' => $role->name,
                'name_en' => $role->name_en,
                'name_ar' => $role->name_ar,
                'type' => $role->type?->value,
                'users_count' => $role->users_count,
                'is_system' => $role->isSystemRole(),
            ]);

        return Inertia::render('admin/roles/Index', [
            'roles' => $roles,
            'filters' => [
                'search' => $search,
                'type' => $type,
            ],
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $this->authorize('create', Role::class);

        $validated = $request->validated();

        Role::create([
            'name' => $validated['name_en'],
            'name_en' => $validated['name_en'],
            'name_ar' => $validated['name_ar'],
            'type' => $validated['type'],
            'guard_name' => 'web',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Role created successfully.'),
        ]);

        return to_route('admin.roles.index');
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $this->authorize('update', $role);

        $validated = $request->validated();

        $role->update([
            'name' => $validated['name_en'],
            'name_en' => $validated['name_en'],
            'name_ar' => $validated['name_ar'],
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Role updated.'),
        ]);

        return to_route('admin.roles.index');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('delete', $role);

        $role->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Role deleted.'),
        ]);

        return to_route('admin.roles.index');
    }
}
