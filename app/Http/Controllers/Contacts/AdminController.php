<?php

namespace App\Http\Controllers\Contacts;

use App\Enums\AdminRole;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Building;
use App\Models\Community;
use App\Models\Country;
use App\Models\ManagerRole;
use App\Models\ServiceManagerType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function index(Request $request): JsonResponse|Response
    {
        $admins = Admin::query()
            ->latest()
            ->paginate($this->perPage($request));

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            return response()->json([
                'data' => collect($admins->items())->map(fn (Admin $admin): array => [
                    'id' => $admin->id,
                    'name' => trim($admin->first_name.' '.$admin->last_name),
                    'image' => $admin->image,
                    'phone_number' => $admin->full_phone_number ?: $admin->phone_number,
                    'phone_country_code' => $admin->phone_country_code,
                    'national_id' => $admin->national_id,
                    'email' => $admin->email,
                    'role' => $admin->role?->value ?? (string) $admin->role,
                    'created_at' => $admin->created_at?->toJSON(),
                    'types' => [],
                ]),
                'meta' => $this->meta($admins),
            ]);
        }

        return Inertia::render('contacts/admins/Index', [
            'admins' => $admins,
        ]);
    }

    public function managerRoles(Request $request): JsonResponse
    {
        $serviceManagerTypes = ServiceManagerType::query()
            ->select('id', 'name')
            ->orderBy('id')
            ->get()
            ->map(fn (ServiceManagerType $type): array => [
                'id' => $type->id,
                'name' => $type->name,
            ])
            ->values()
            ->all();

        $roles = ManagerRole::query()
            ->orderBy('id')
            ->paginate($this->perPage($request));

        return response()->json([
            'data' => collect($roles->items())->map(fn (ManagerRole $role): array => [
                'id' => $role->id,
                'role' => $role->role,
                'name_ar' => $role->name_ar,
                'name_en' => $role->name_en,
                'types' => $role->role === AdminRole::ServiceManagers->value ? $serviceManagerTypes : null,
            ]),
            'meta' => $this->meta($roles),
        ]);
    }

    public function checkValidate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone_country_code' => ['required', 'string', 'max:5'],
            'phone_number' => ['required', 'string', 'max:20'],
        ]);

        $duplicateAdmin = Admin::query()
            ->where('first_name', $validated['first_name'])
            ->where('last_name', $validated['last_name'])
            ->where('phone_country_code', $validated['phone_country_code'])
            ->where('phone_number', $validated['phone_number'])
            ->exists();

        return response()->json([
            'code' => 200,
            'message' => __('Operation completed successfully.'),
            'data' => $duplicateAdmin ? [['is_duplicate' => true]] : [],
            'meta' => [],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('contacts/admins/Create', [
            'countries' => Country::select('id', 'name', 'name_en')->orderBy('name')->get(),
            'communities' => Community::select('id', 'name')->orderBy('name')->get(),
            'buildings' => Building::select('id', 'name', 'rf_community_id')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'phone_country_code' => ['required', 'string', 'max:5'],
            'role' => ['required', 'in:Admins,accountingManagers,serviceManagers,marketingManagers,salesAndLeasingManagers'],
            'national_id' => ['nullable', 'string', 'max:50'],
            'nationality_id' => ['nullable', 'integer', 'exists:countries,id'],
            'gender' => ['nullable', 'in:male,female'],
            'georgian_birthdate' => ['nullable', 'date'],
            'active' => ['sometimes', 'boolean'],
            'communities' => ['nullable', 'array'],
            'communities.*' => ['integer', 'exists:rf_communities,id'],
            'buildings' => ['nullable', 'array'],
            'buildings.*' => ['integer', 'exists:rf_buildings,id'],
        ]);

        $admin = Admin::create(collect($validated)->except(['communities', 'buildings'])->all());

        if (isset($validated['communities'])) {
            $admin->communities()->sync($validated['communities']);
        }
        if (isset($validated['buildings'])) {
            $admin->buildings()->sync($validated['buildings']);
        }

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $admin->load(['communities', 'buildings', 'serviceManagerTypes']);

            return response()->json([
                'data' => $this->adminDetails($admin),
                'message' => __('Admin created.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Admin created.')]);

        return to_route('admins.show', $admin);
    }

    public function show(Request $request, Admin $admin): JsonResponse|Response
    {
        $admin->load(['communities', 'buildings', 'serviceManagerTypes']);

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            return response()->json([
                'data' => $this->adminDetails($admin),
                'message' => __('Admin retrieved.'),
            ]);
        }

        return Inertia::render('contacts/admins/Show', [
            'admin' => $admin,
        ]);
    }

    public function edit(Admin $admin): Response
    {
        $admin->load(['communities', 'buildings']);

        return Inertia::render('contacts/admins/Edit', [
            'admin' => $admin,
            'countries' => Country::select('id', 'name', 'name_en')->orderBy('name')->get(),
            'communities' => Community::select('id', 'name')->orderBy('name')->get(),
            'buildings' => Building::select('id', 'name', 'rf_community_id')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Admin $admin): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'phone_country_code' => ['required', 'string', 'max:5'],
            'role' => ['required', 'in:Admins,accountingManagers,serviceManagers,marketingManagers,salesAndLeasingManagers'],
            'national_id' => ['nullable', 'string', 'max:50'],
            'nationality_id' => ['nullable', 'integer', 'exists:countries,id'],
            'gender' => ['nullable', 'in:male,female'],
            'georgian_birthdate' => ['nullable', 'date'],
            'active' => ['sometimes', 'boolean'],
            'communities' => ['nullable', 'array'],
            'communities.*' => ['integer', 'exists:rf_communities,id'],
            'buildings' => ['nullable', 'array'],
            'buildings.*' => ['integer', 'exists:rf_buildings,id'],
        ]);

        $admin->update(collect($validated)->except(['communities', 'buildings'])->all());

        if (array_key_exists('communities', $validated)) {
            $admin->communities()->sync($validated['communities'] ?? []);
        }
        if (array_key_exists('buildings', $validated)) {
            $admin->buildings()->sync($validated['buildings'] ?? []);
        }

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $admin->load(['communities', 'buildings', 'serviceManagerTypes']);

            return response()->json([
                'data' => $this->adminDetails($admin),
                'message' => __('Admin updated.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Admin updated.')]);

        return to_route('admins.show', $admin);
    }

    public function destroy(Request $request, Admin $admin): JsonResponse|RedirectResponse
    {
        $adminId = $admin->id;
        $admin->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $adminId,
                ],
                'message' => __('Admin deleted.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Admin deleted.')]);

        return to_route('admins.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function adminDetails(Admin $admin): array
    {
        return [
            'id' => $admin->id,
            'name' => trim($admin->first_name.' '.$admin->last_name),
            'first_name' => $admin->first_name,
            'last_name' => $admin->last_name,
            'image' => $admin->image,
            'email' => $admin->email,
            'georgian_birthdate' => $admin->georgian_birthdate?->toDateString(),
            'gender' => $admin->gender,
            'national_id' => $admin->national_id,
            'full_phone_number' => $admin->full_phone_number ?: $admin->phone_number,
            'phone_number' => $admin->phone_number,
            'phone_country_code' => $admin->phone_country_code,
            'nationality' => null,
            'role' => $admin->role?->value ?? (string) $admin->role,
            'selects' => [
                'is_all_buildings' => null,
                'is_all_communities' => null,
            ],
            'created_at' => $admin->created_at?->toJSON(),
            'last_login_at' => $admin->last_login_at?->toJSON(),
            'active' => $admin->active ? '1' : '0',
            'types' => $admin->serviceManagerTypes
                ->map(fn (ServiceManagerType $type): array => [
                    'id' => $type->id,
                    'name' => $type->name,
                ])
                ->values()
                ->all(),
        ];
    }

    private function perPage(Request $request): int
    {
        return min(max((int) $request->integer('per_page', 10), 1), 50);
    }

    /**
     * @return array<string, mixed>
     */
    private function meta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'from' => $paginator->firstItem(),
            'last_page' => $paginator->lastPage(),
            'path' => $paginator->path(),
            'per_page' => $paginator->perPage(),
            'to' => $paginator->lastItem(),
            'total' => $paginator->total(),
        ];
    }
}
