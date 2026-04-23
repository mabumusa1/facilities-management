<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PermissionAction;
use App\Enums\PermissionSubject;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\SyncRolePermissionsRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
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

    public function permissions(Role $role): Response
    {
        $this->authorize('viewAny', Role::class);

        $role->load('permissions');

        $subjects = array_map(fn (PermissionSubject $s) => $s->value, PermissionSubject::cases());
        $actions = array_map(fn (PermissionAction $a) => $a->value, PermissionAction::cases());

        return Inertia::render('admin/roles/Permissions', [
            'role' => [
                'id' => $role->id,
                'name_en' => $role->name_en,
                'name_ar' => $role->name_ar,
                'type' => $role->type?->value,
                'is_system' => $role->isSystemRole(),
            ],
            'subjects' => $subjects,
            'actions' => $actions,
            'presets' => $this->buildPresets(),
            'permissions' => Inertia::defer(fn () => $role->permissions->pluck('name')->all()),
        ]);
    }

    public function syncPermissions(SyncRolePermissionsRequest $request, Role $role): JsonResponse
    {
        $this->authorize('managePermissions', $role);

        $validated = $request->validated();

        $permissionIds = Permission::whereIn('name', $validated['permissions'])->pluck('id');
        $role->permissions()->sync($permissionIds);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return response()->json(['message' => __('Permissions saved')]);
    }

    /**
     * Build the preset list from the same subject/action definitions used in RbacSeeder.
     *
     * @return array<int, array{label: string, permissions: string[]}>
     */
    private function buildPresets(): array
    {
        $allSubjects = PermissionSubject::cases();
        $allActions = PermissionAction::cases();

        $adminSubjects = array_values(array_filter(
            $allSubjects,
            fn (PermissionSubject $s) => ! in_array($s, [
                PermissionSubject::CompanyProfile,
                PermissionSubject::InvoiceSettings,
                PermissionSubject::LeaseSettings,
            ])
        ));

        $managerSubjects = [
            PermissionSubject::Communities,
            PermissionSubject::Buildings,
            PermissionSubject::Units,
            PermissionSubject::Leases,
            PermissionSubject::Transactions,
            PermissionSubject::Payments,
            PermissionSubject::Owners,
            PermissionSubject::Tenants,
            PermissionSubject::Dependents,
            PermissionSubject::Professionals,
            PermissionSubject::FacilityBookings,
            PermissionSubject::ManagerRequests,
            PermissionSubject::Facilities,
            PermissionSubject::Announcements,
            PermissionSubject::Directories,
            PermissionSubject::Suggestions,
            PermissionSubject::Complaints,
            PermissionSubject::MarketPlaces,
            PermissionSubject::MarketPlaceBookings,
            PermissionSubject::MarketPlaceVisits,
            PermissionSubject::OfferRequests,
            PermissionSubject::Reports,
            PermissionSubject::VisitorAccess,
            PermissionSubject::HomeServices,
            PermissionSubject::NeighbourhoodServices,
        ];

        $ownerSubjects = [
            PermissionSubject::Units,
            PermissionSubject::Leases,
            PermissionSubject::Transactions,
            PermissionSubject::Payments,
            PermissionSubject::FacilityBookings,
            PermissionSubject::Facilities,
            PermissionSubject::Announcements,
            PermissionSubject::MarketPlaces,
            PermissionSubject::MarketPlaceBookings,
            PermissionSubject::OfferRequests,
            PermissionSubject::Reports,
        ];
        $ownerActions = [PermissionAction::View, PermissionAction::Create, PermissionAction::Update];

        $tenantSubjects = [
            PermissionSubject::Leases,
            PermissionSubject::Transactions,
            PermissionSubject::Payments,
            PermissionSubject::FacilityBookings,
            PermissionSubject::Facilities,
            PermissionSubject::Announcements,
            PermissionSubject::Directories,
            PermissionSubject::Suggestions,
            PermissionSubject::Complaints,
            PermissionSubject::MarketPlaces,
            PermissionSubject::VisitorAccess,
            PermissionSubject::HomeServices,
            PermissionSubject::NeighbourhoodServices,
        ];
        $tenantActions = [PermissionAction::View, PermissionAction::Create];

        $dependentSubjects = [
            PermissionSubject::Announcements,
            PermissionSubject::Facilities,
            PermissionSubject::FacilityBookings,
        ];

        $professionalSubjects = [
            PermissionSubject::ManagerRequests,
            PermissionSubject::HomeServices,
            PermissionSubject::NeighbourhoodServices,
        ];
        $professionalActions = [PermissionAction::View, PermissionAction::Update];

        return [
            [
                'label' => 'Admin',
                'permissions' => $this->buildPermissionNames($allSubjects, $allActions),
            ],
            [
                'label' => 'Manager',
                'permissions' => $this->buildPermissionNames($managerSubjects, $allActions),
            ],
            [
                'label' => 'Accounting Manager',
                'permissions' => $this->buildPermissionNames([
                    PermissionSubject::Transactions,
                    PermissionSubject::Payments,
                    PermissionSubject::Leases,
                    PermissionSubject::Reports,
                    PermissionSubject::InvoiceSettings,
                    PermissionSubject::LeaseSettings,
                    PermissionSubject::Tenants,
                    PermissionSubject::Owners,
                ], $allActions),
            ],
            [
                'label' => 'Service Manager',
                'permissions' => $this->buildPermissionNames([
                    PermissionSubject::ManagerRequests,
                    PermissionSubject::HomeServices,
                    PermissionSubject::NeighbourhoodServices,
                    PermissionSubject::Facilities,
                    PermissionSubject::FacilityBookings,
                    PermissionSubject::Professionals,
                    PermissionSubject::Suggestions,
                    PermissionSubject::Complaints,
                ], $allActions),
            ],
            [
                'label' => 'Owner',
                'permissions' => $this->buildPermissionNames($ownerSubjects, $ownerActions),
            ],
            [
                'label' => 'Tenant',
                'permissions' => $this->buildPermissionNames($tenantSubjects, $tenantActions),
            ],
            [
                'label' => 'Professional',
                'permissions' => $this->buildPermissionNames($professionalSubjects, $professionalActions),
            ],
            [
                'label' => 'Dependent',
                'permissions' => $this->buildPermissionNames($dependentSubjects, [PermissionAction::View]),
            ],
            [
                'label' => 'No permissions',
                'permissions' => [],
            ],
        ];
    }

    /**
     * @param  list<PermissionSubject>  $subjects
     * @param  list<PermissionAction>  $actions
     * @return list<string>
     */
    private function buildPermissionNames(array $subjects, array $actions): array
    {
        $names = [];
        foreach ($subjects as $subject) {
            foreach ($actions as $action) {
                $names[] = "{$subject->value}.{$action->value}";
            }
        }

        return $names;
    }
}
