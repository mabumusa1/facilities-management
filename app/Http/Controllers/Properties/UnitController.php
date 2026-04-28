<?php

namespace App\Http\Controllers\Properties;

use App\Enums\UnitStatus;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\City;
use App\Models\Community;
use App\Models\Currency;
use App\Models\District;
use App\Models\Feature;
use App\Models\Owner;
use App\Models\Resident;
use App\Models\Status;
use App\Models\Unit;
use App\Models\UnitCategory;
use App\Services\UnitStateMachine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UnitController extends Controller
{
    public function rfIndex(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->integer('per_page', 10), 1), 50);
        $statusId = (int) $request->integer('status_id', 0);
        $search = trim((string) $request->input('search', ''));

        $units = Unit::query()
            ->with([
                'community:id,name,city_id,district_id,is_market_place,is_buy,community_marketplace_type,is_off_plan_sale,sales_commission_rate,rental_commission_rate,total_income,count_selected_property,is_selected_property',
                'community.city:id,name',
                'community.district:id,name',
                'building:id,name',
                'category:id,name,name_ar,name_en,icon',
                'type:id,name,name_ar,name_en,icon',
                'status:id,name,name_ar,name_en,priority',
                'owner:id,first_name,last_name',
                'tenant:id,first_name,last_name',
                'city:id,name',
                'district:id,name',
            ])
            ->when($statusId > 0, fn ($query) => $query->where('status_id', $statusId))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nestedQuery) use ($search): void {
                    $nestedQuery->where('name', 'like', "%{$search}%");

                    if (is_numeric($search)) {
                        $nestedQuery->orWhere('id', (int) $search);
                    }
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'code' => 200,
            'message' => 'messages.units_retrieved',
            'data' => collect($units->items())
                ->map(fn (Unit $unit): array => $this->rfUnitListPayload($unit))
                ->values()
                ->all(),
            'meta' => $this->meta($units),
        ]);
    }

    public function rfCreate(): JsonResponse
    {
        return response()->json([
            'data' => [
                'communities' => Community::select('id', 'name')->orderBy('name')->get(),
                'buildings' => Building::select('id', 'name', 'rf_community_id')->orderBy('name')->get(),
                'categories' => UnitCategory::with('types')->get(),
                'statuses' => Status::where('type', 'unit')->select('id', 'name', 'name_en')->get(),
                'owners' => Owner::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
                'residents' => Resident::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
                'cities' => City::select('id', 'name', 'name_en', 'country_id')->orderBy('name')->get(),
                'districts' => District::select('id', 'name', 'name_en', 'city_id')->orderBy('name')->get(),
            ],
            'meta' => [],
        ]);
    }

    public function rfShow(Unit $unit): JsonResponse
    {
        $unit->load([
            'community:id,name,city_id,district_id,is_market_place,is_buy,community_marketplace_type,is_off_plan_sale,sales_commission_rate,rental_commission_rate,total_income,count_selected_property,is_selected_property',
            'community.city:id,name',
            'community.district:id,name',
            'building:id,name',
            'category:id,name,name_ar,name_en,icon',
            'type:id,name,name_ar,name_en,icon',
            'status:id,name,name_ar,name_en,priority,created_at',
            'owner:id,first_name,last_name',
            'tenant:id,first_name,last_name',
            'city:id,name',
            'district:id,name',
            'photos:id,mediable_id,mediable_type,name',
            'floorPlans:id,mediable_id,mediable_type,name',
            'documents:id,mediable_id,mediable_type,name',
            'specifications:id,unit_id,key,value,name_ar,name_en',
            'rooms:id,unit_id,name,name_ar,name_en,count',
            'areas:id,unit_id,type,name_ar,name_en,size',
            'features:id,name,name_en',
            'currency:id,code,symbol',
        ]);

        if ($unit->community !== null) {
            $unit->community->loadCount(['buildings', 'units']);
        }

        return response()->json([
            'code' => 200,
            'message' => 'messages.unit_show',
            'data' => $this->rfUnitDetailsPayload($unit),
            'meta' => [],
        ]);
    }

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Unit::class);

        $units = Unit::query()
            ->with(['community', 'building', 'category', 'type', 'status', 'owner', 'tenant'])
            ->latest()
            ->paginate(15);

        return Inertia::render('properties/units/Index', [
            'units' => $units,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Unit::class);

        return Inertia::render('properties/units/Create', [
            'communities' => Community::select('id', 'name')->get(),
            'buildings' => Building::select('id', 'name', 'rf_community_id')->orderBy('name')->get(),
            'categories' => UnitCategory::with('types')->get(),
            'statuses' => Status::where('type', 'unit')->select('id', 'name', 'name_en')->get(),
            'owners' => Owner::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
            'residents' => Resident::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
            'cities' => City::select('id', 'name', 'name_en', 'country_id')->orderBy('name')->get(),
            'districts' => District::select('id', 'name', 'name_en', 'city_id')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Unit::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'rf_community_id' => ['required', 'integer', 'exists:rf_communities,id'],
            'rf_building_id' => ['nullable', 'integer', 'exists:rf_buildings,id'],
            'category_id' => ['required', 'integer', 'exists:rf_unit_categories,id'],
            'type_id' => ['required', 'integer', 'exists:rf_unit_types,id'],
            'status_id' => ['required', 'integer', 'exists:rf_statuses,id'],
            'owner_id' => ['nullable', 'integer', 'exists:rf_owners,id'],
            'tenant_id' => ['nullable', 'integer', 'exists:rf_tenants,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'net_area' => ['nullable', 'numeric', 'gt:0'],
            'floor_no' => ['nullable', 'integer'],
            'year_build' => ['nullable', 'digits:4'],
            'about' => ['nullable', 'string'],
            'is_market_place' => ['sometimes', 'boolean'],
            'is_buy' => ['sometimes', 'boolean'],
            'is_off_plan_sale' => ['sometimes', 'boolean'],
        ]);

        $unit = Unit::create($validated);

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $unit->load([
                'community:id,name,city_id,district_id,is_market_place,is_buy,community_marketplace_type,is_off_plan_sale,sales_commission_rate,rental_commission_rate,total_income,count_selected_property,is_selected_property',
                'community.city:id,name',
                'community.district:id,name',
                'building:id,name',
                'category:id,name,name_ar,name_en,icon',
                'type:id,name,name_ar,name_en,icon',
                'status:id,name,name_ar,name_en,priority,created_at',
                'owner:id,first_name,last_name',
                'tenant:id,first_name,last_name',
                'city:id,name',
                'district:id,name',
                'photos:id,mediable_id,mediable_type,name',
                'floorPlans:id,mediable_id,mediable_type,name',
                'documents:id,mediable_id,mediable_type,name',
                'specifications:id,unit_id,key,value,name_ar,name_en',
                'rooms:id,unit_id,name,name_ar,name_en,count',
                'areas:id,unit_id,type,name_ar,name_en,size',
            ]);

            if ($unit->community !== null) {
                $unit->community->loadCount(['buildings', 'units']);
            }

            return response()->json([
                'code' => 200,
                'message' => 'messages.unit_created',
                'data' => $this->rfUnitDetailsPayload($unit),
                'meta' => [],
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Unit created.')]);

        return to_route('units.show', $unit);
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'unit_ids' => ['required', 'array', 'min:1'],
            'unit_ids.*' => ['integer', 'exists:rf_units,id'],
        ]);

        $unitIds = collect($validated['unit_ids'])
            ->map(fn (mixed $unitId): int => (int) $unitId)
            ->unique()
            ->values()
            ->all();

        Unit::query()->whereIn('id', $unitIds)->delete();

        return response()->json([
            'data' => [
                'ids' => $unitIds,
                'deleted_count' => count($unitIds),
            ],
            'message' => __('Units deleted.'),
        ]);
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'unit_ids' => ['required', 'array', 'min:1'],
            'unit_ids.*' => ['integer', 'exists:rf_units,id'],
            'status_id' => ['nullable', 'integer', 'exists:rf_statuses,id'],
            'owner_id' => ['nullable', 'integer', 'exists:rf_owners,id'],
            'tenant_id' => ['nullable', 'integer', 'exists:rf_tenants,id'],
            'rf_community_id' => ['nullable', 'integer', 'exists:rf_communities,id'],
            'rf_building_id' => ['nullable', 'integer', 'exists:rf_buildings,id'],
            'category_id' => ['nullable', 'integer', 'exists:rf_unit_categories,id'],
            'type_id' => ['nullable', 'integer', 'exists:rf_unit_types,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'is_market_place' => ['nullable', 'boolean'],
            'is_buy' => ['nullable', 'boolean'],
            'is_off_plan_sale' => ['nullable', 'boolean'],
        ]);

        $unitIds = collect($validated['unit_ids'])
            ->map(fn (mixed $unitId): int => (int) $unitId)
            ->unique()
            ->values()
            ->all();

        $updates = collect($validated)
            ->except(['unit_ids', 'status'])
            ->filter(fn (mixed $value): bool => $value !== null)
            ->all();

        if (array_key_exists('status', $validated) && $validated['status'] !== null) {
            $targetStatus = UnitStatus::tryFrom($validated['status']);
            if ($targetStatus === null) {
                throw ValidationException::withMessages([
                    'status' => sprintf(
                        'Invalid status. Allowed values: %s.',
                        implode(', ', array_column(UnitStatus::cases(), 'value'))
                    ),
                ]);
            }

            $stateMachine = app(UnitStateMachine::class);
            $units = Unit::query()->whereIn('id', $unitIds)->get();

            foreach ($units as $unit) {
                $stateMachine->transition($unit, $targetStatus, $request->user(), 'Bulk status change');
            }

            // Remove status from updates to avoid double-update
            unset($validated['status']);
        }

        $updates = collect($validated)
            ->except(['unit_ids'])
            ->filter(fn (mixed $value): bool => $value !== null)
            ->all();

        if ($updates === []) {
            // If only status was set (handled above), return success
            if (! empty($unitIds)) {
                return response()->json([
                    'data' => [
                        'ids' => $unitIds,
                        'updated_count' => count($unitIds),
                    ],
                    'message' => __('Units updated.'),
                ]);
            }

            throw ValidationException::withMessages([
                'updates' => __('At least one field must be provided for bulk update.'),
            ]);
        }

        Unit::query()
            ->whereIn('id', $unitIds)
            ->update($updates);

        return response()->json([
            'data' => [
                'ids' => $unitIds,
                'updated_count' => count($unitIds),
            ],
            'message' => __('Units updated.'),
        ]);
    }

    public function show(Unit $unit): Response
    {
        $this->authorize('view', $unit);

        $unit->load([
            'community',
            'building',
            'category',
            'type',
            'status',
            'owner',
            'tenant',
            'specifications',
            'rooms',
            'features',
            'currency',
        ]);

        return Inertia::render('properties/units/Show', [
            'unit' => $unit,
        ]);
    }

    public function edit(Unit $unit): Response
    {
        $this->authorize('update', $unit);

        $unit->load(['owner', 'tenant', 'specifications', 'rooms', 'features', 'currency']);

        return Inertia::render('properties/units/Edit', [
            'unit' => $unit,
            'communities' => Community::select('id', 'name')->get(),
            'buildings' => Building::select('id', 'name', 'rf_community_id')->orderBy('name')->get(),
            'categories' => UnitCategory::with('types')->get(),
            'statuses' => Status::where('type', 'unit')->select('id', 'name', 'name_en')->get(),
            'owners' => Owner::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
            'residents' => Resident::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
            'cities' => City::select('id', 'name', 'name_en', 'country_id')->orderBy('name')->get(),
            'districts' => District::select('id', 'name', 'name_en', 'city_id')->orderBy('name')->get(),
            'amenityOptions' => Feature::amenities()->select('id', 'name', 'name_en', 'name_ar')->orderBy('name')->get(),
            'currencies' => Currency::select('id', 'name', 'code', 'symbol')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Unit $unit): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $unit);

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'rf_community_id' => ['required', 'integer', 'exists:rf_communities,id'],
                'rf_building_id' => ['nullable', 'integer', 'exists:rf_buildings,id'],
                'category_id' => ['required', 'integer', 'exists:rf_unit_categories,id'],
                'type_id' => ['required', 'integer', 'exists:rf_unit_types,id'],
                'status_id' => ['nullable', 'integer', 'exists:rf_statuses,id'],
                'owner_id' => ['nullable', 'integer', 'exists:rf_owners,id'],
                'tenant_id' => ['nullable', 'integer', 'exists:rf_tenants,id'],
                'city_id' => ['nullable', 'integer', 'exists:cities,id'],
                'district_id' => ['nullable', 'integer', 'exists:districts,id'],
                'net_area' => ['nullable', 'numeric', 'gt:0'],
                'floor_no' => ['nullable', 'integer'],
                'year_build' => ['nullable', 'digits:4'],
                'about' => ['nullable', 'string'],
                'is_market_place' => ['sometimes', 'boolean'],
                'is_buy' => ['sometimes', 'boolean'],
                'is_off_plan_sale' => ['sometimes', 'boolean'],
            ]);

            $unit->update($validated);
            $unit->load([
                'community:id,name,city_id,district_id,is_market_place,is_buy,community_marketplace_type,is_off_plan_sale,sales_commission_rate,rental_commission_rate,total_income,count_selected_property,is_selected_property',
                'community.city:id,name',
                'community.district:id,name',
                'building:id,name',
                'category:id,name,name_ar,name_en,icon',
                'type:id,name,name_ar,name_en,icon',
                'status:id,name,name_ar,name_en,priority,created_at',
                'owner:id,first_name,last_name',
                'tenant:id,first_name,last_name',
                'city:id,name',
                'district:id,name',
                'photos:id,mediable_id,mediable_type,name',
                'floorPlans:id,mediable_id,mediable_type,name',
                'documents:id,mediable_id,mediable_type,name',
                'specifications:id,unit_id,key,value,name_ar,name_en',
                'rooms:id,unit_id,name,name_ar,name_en,count',
                'areas:id,unit_id,type,name_ar,name_en,size',
            ]);

            if ($unit->community !== null) {
                $unit->community->loadCount(['buildings', 'units']);
            }

            return response()->json([
                'code' => 200,
                'message' => 'messages.unit_updated',
                'data' => $this->rfUnitDetailsPayload($unit),
                'meta' => [],
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'rf_community_id' => ['required', 'integer', 'exists:rf_communities,id'],
            'rf_building_id' => ['nullable', 'integer', 'exists:rf_buildings,id'],
            'category_id' => ['required', 'integer', 'exists:rf_unit_categories,id'],
            'type_id' => ['required', 'integer', 'exists:rf_unit_types,id'],
            'status_id' => ['required', 'integer', 'exists:rf_statuses,id'],
            'owner_id' => ['nullable', 'integer', 'exists:rf_owners,id'],
            'tenant_id' => ['nullable', 'integer', 'exists:rf_tenants,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'net_area' => ['nullable', 'numeric', 'gt:0'],
            'floor_no' => ['nullable', 'integer'],
            'year_build' => ['nullable', 'digits:4'],
            'about' => ['nullable', 'string'],
            'is_market_place' => ['sometimes', 'boolean'],
            'is_buy' => ['sometimes', 'boolean'],
            'is_off_plan_sale' => ['sometimes', 'boolean'],
            // Pricing
            'currency_id' => ['nullable', 'integer', 'exists:currencies,id'],
            'asking_rent_amount' => ['nullable', 'numeric', 'min:0'],
            'rent_period' => ['nullable', 'in:month,year'],
            // Rooms
            'rooms' => ['sometimes', 'array'],
            'rooms.*.name' => ['required_with:rooms', 'string', 'max:100'],
            'rooms.*.count' => ['required_with:rooms', 'integer', 'min:0', 'max:99'],
            // Specifications (furnished, parking_bays, view)
            'specifications' => ['sometimes', 'array'],
            'specifications.*.key' => ['required_with:specifications', 'string', 'max:100'],
            'specifications.*.value' => ['required_with:specifications', 'string', 'max:255'],
            // Amenities
            'amenity_ids' => ['sometimes', 'present', 'array'],
            'amenity_ids.*' => ['integer', 'exists:rf_features,id'],
        ]);

        $unit->update([
            'name' => $validated['name'],
            'rf_community_id' => $validated['rf_community_id'],
            'rf_building_id' => $validated['rf_building_id'] ?? null,
            'category_id' => $validated['category_id'],
            'type_id' => $validated['type_id'],
            'status_id' => $validated['status_id'],
            'owner_id' => $validated['owner_id'] ?? null,
            'tenant_id' => $validated['tenant_id'] ?? null,
            'city_id' => $validated['city_id'] ?? null,
            'district_id' => $validated['district_id'] ?? null,
            'net_area' => $validated['net_area'] ?? null,
            'floor_no' => $validated['floor_no'] ?? null,
            'year_build' => $validated['year_build'] ?? null,
            'about' => $validated['about'] ?? null,
            'is_market_place' => $validated['is_market_place'] ?? $unit->is_market_place,
            'is_buy' => $validated['is_buy'] ?? $unit->is_buy,
            'is_off_plan_sale' => $validated['is_off_plan_sale'] ?? $unit->is_off_plan_sale,
            'currency_id' => $validated['currency_id'] ?? null,
            'asking_rent_amount' => $validated['asking_rent_amount'] ?? null,
            'rent_period' => $validated['rent_period'] ?? null,
        ]);

        // Sync rooms
        if (array_key_exists('rooms', $validated)) {
            $unit->rooms()->delete();
            foreach ($validated['rooms'] as $room) {
                $unit->rooms()->create([
                    'name' => $room['name'],
                    'name_en' => $room['name'],
                    'count' => $room['count'],
                ]);
            }
        }

        // Sync specifications (furnished, parking_bays, view)
        if (array_key_exists('specifications', $validated)) {
            $incomingKeys = array_column($validated['specifications'], 'key');

            // Delete specs not in incoming
            $unit->specifications()->whereNotIn('key', $incomingKeys)->delete();

            foreach ($validated['specifications'] as $spec) {
                $unit->specifications()->updateOrCreate(
                    ['key' => $spec['key']],
                    ['value' => $spec['value']]
                );
            }
        }

        // Sync amenities
        if (array_key_exists('amenity_ids', $validated)) {
            $unit->features()->sync($validated['amenity_ids']);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Unit updated.')]);

        return to_route('units.show', $unit);
    }

    public function rfExport(Request $request): BinaryFileResponse
    {
        $this->authorize('viewAny', Unit::class);

        $filters = $request->only(['status', 'community_id', 'building_id', 'category_id', 'search']);

        return Excel::download(
            new UnitsExport($filters),
            'units-'.now()->format('Y-m-d-His').'.xlsx'
        );
    }

    public function updateStatus(Request $request, Unit $unit): JsonResponse
    {
        $this->authorize('update', $unit);

        $validated = $request->validate([
            'status' => ['required', 'string'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $targetStatus = UnitStatus::tryFrom($validated['status']);

        if ($targetStatus === null) {
            throw ValidationException::withMessages([
                'status' => sprintf(
                    'Invalid status. Allowed values: %s.',
                    implode(', ', array_column(UnitStatus::cases(), 'value'))
                ),
            ]);
        }

        $stateMachine = app(UnitStateMachine::class);
        $history = $stateMachine->transition(
            $unit,
            $targetStatus,
            $request->user(),
            $validated['reason'] ?? null
        );

        return response()->json([
            'data' => [
                'unit_id' => $unit->id,
                'status' => $unit->fresh()->status,
                'history_id' => $history->id,
            ],
            'message' => sprintf(
                'Unit status changed to "%s".',
                $targetStatus->label()
            ),
        ]);
    }

    public function statusHistory(Unit $unit): JsonResponse
    {
        $this->authorize('view', $unit);

        $history = $unit->statusHistory()
            ->with('changedBy:id,name')
            ->latest()
            ->get()
            ->map(fn ($record): array => [
                'id' => $record->id,
                'from_status' => $record->from_status,
                'to_status' => $record->to_status,
                'changed_by' => $record->changedBy?->name,
                'reason' => $record->reason,
                'created_at' => $record->created_at->toJSON(),
            ]);

        return response()->json(['data' => $history]);
    }

    public function uploadPhoto(Request $request, Unit $unit): JsonResponse
    {
        $this->authorize('update', $unit);

        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $path = $request->file('file')->store('unit-photos');

        $maxOrder = $unit->photos()->max('sort_order') ?? 0;
        $isPrimary = $unit->photos()->count() === 0;

        $media = $unit->photos()->create([
            'name' => $request->file('file')->getClientOriginalName(),
            'url' => $path,
            'collection' => 'photos',
            'sort_order' => $maxOrder + 1,
            'is_primary' => $isPrimary,
        ]);

        return response()->json([
            'data' => [
                'id' => $media->id,
                'name' => $media->name,
                'url' => $media->url,
                'sort_order' => $media->sort_order,
                'is_primary' => $media->is_primary,
            ],
            'message' => __('Photo uploaded.'),
        ]);
    }

    public function reorderPhotos(Request $request, Unit $unit): JsonResponse
    {
        $this->authorize('update', $unit);

        $validated = $request->validate([
            'order' => ['required', 'array', 'min:1'],
            'order.*.id' => ['required', 'integer'],
            'order.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($validated['order'] as $item) {
            $unit->photos()->whereKey($item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['message' => __('Photo order updated.')]);
    }

    public function setPrimaryPhoto(Request $request, Unit $unit): JsonResponse
    {
        $this->authorize('update', $unit);

        $validated = $request->validate([
            'photo_id' => ['required', 'integer'],
        ]);

        $unit->photos()->update(['is_primary' => false]);
        $unit->photos()->whereKey($validated['photo_id'])->update(['is_primary' => true]);

        return response()->json(['message' => __('Primary photo set.')]);
    }

    public function deletePhoto(Request $request, Unit $unit): JsonResponse
    {
        $this->authorize('update', $unit);

        $validated = $request->validate([
            'photo_id' => ['required', 'integer'],
        ]);

        $photo = $unit->photos()->whereKey($validated['photo_id'])->firstOrFail();

        if ($photo->is_primary && $unit->photos()->count() > 1) {
            $nextPhoto = $unit->photos()->whereKeyNot($photo->id)->orderBy('sort_order')->first();
            $nextPhoto?->update(['is_primary' => true]);
        }

        $photo->delete();

        return response()->json(['message' => __('Photo deleted.')]);
    }

    public function destroy(Request $request, Unit $unit): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $unit);

        $unitId = $unit->id;
        $unit->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $unitId,
                ],
                'message' => __('Unit deleted.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Unit deleted.')]);

        return to_route('units.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function rfUnitListPayload(Unit $unit): array
    {
        return [
            'id' => $unit->id,
            'name' => $unit->name,
            'category' => [
                'id' => $unit->category?->id,
                'name' => $unit->category?->name_en ?? $unit->category?->name,
                'icon' => $unit->category?->icon,
            ],
            'type' => [
                'id' => $unit->type?->id,
                'name' => $unit->type?->name_en ?? $unit->type?->name,
                'icon' => $unit->type?->icon,
            ],
            'rf_community' => [
                'id' => $unit->community?->id,
                'name' => $unit->community?->name,
            ],
            'rf_building' => [
                'id' => $unit->building?->id,
                'name' => $unit->building?->name,
            ],
            'owner' => $unit->owner
                ? [
                    'id' => $unit->owner->id,
                    'name' => trim($unit->owner->first_name.' '.$unit->owner->last_name),
                ]
                : null,
            'tenant' => $unit->tenant
                ? [
                    'id' => $unit->tenant->id,
                    'name' => trim($unit->tenant->first_name.' '.$unit->tenant->last_name),
                ]
                : null,
            'status' => [
                'id' => $unit->status?->id,
                'name' => $unit->status?->name_en ?? $unit->status?->name,
            ],
            'photos' => [],
            'is_market_place' => $unit->is_market_place ? '1' : '0',
            'city' => [
                'id' => $unit->city?->id,
                'name' => $unit->city?->name,
            ],
            'district' => [
                'id' => $unit->district?->id,
                'name' => $unit->district?->name,
            ],
            'market_rent' => null,
            'net_area' => $unit->net_area,
            'floor_no' => $unit->floor_no,
            'map' => $unit->map,
            'is_off_plan_sale' => $unit->is_off_plan_sale ? '1' : '0',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function rfUnitDetailsPayload(Unit $unit): array
    {
        return [
            ...$this->rfUnitListPayload($unit),
            'status' => [
                'id' => $unit->status?->id,
                'name' => $unit->status?->name_en ?? $unit->status?->name,
                'created_at' => $unit->status?->created_at?->toJSON(),
                'priority' => $unit->status?->priority !== null ? (string) $unit->status->priority : null,
            ],
            'rf_community' => [
                'id' => $unit->community?->id,
                'name' => $unit->community?->name,
                'city' => [
                    'id' => $unit->community?->city?->id,
                    'name' => $unit->community?->city?->name,
                ],
                'district' => [
                    'id' => $unit->community?->district?->id,
                    'name' => $unit->community?->district?->name,
                ],
                'sales_commission_rate' => $unit->community?->sales_commission_rate,
                'rental_commission_rate' => $unit->community?->rental_commission_rate,
                'buildings_count' => (int) ($unit->community?->buildings_count ?? 0),
                'units_count' => (int) ($unit->community?->units_count ?? 0),
                'map' => $unit->community?->map,
                'images' => [],
                'is_selected_property' => (bool) ($unit->community?->is_selected_property ?? false),
                'count_selected_property' => (int) ($unit->community?->count_selected_property ?? 0),
                'requests_count' => null,
                'total_income' => (float) ($unit->community?->total_income ?? 0),
                'is_market_place' => ($unit->community?->is_market_place ?? false) ? '1' : '0',
                'is_buy' => ($unit->community?->is_buy ?? false) ? 1 : 0,
                'community_marketplace_type' => $unit->community?->community_marketplace_type?->value
                    ?? $unit->community?->community_marketplace_type
                    ?? 'rent',
                'is_off_plan_sale' => ($unit->community?->is_off_plan_sale ?? false) ? '1' : '0',
            ],
            'year_build' => (string) $unit->year_build,
            'photos' => $unit->photos->map(fn ($photo): array => [
                'id' => $photo->id,
                'name' => $photo->name,
            ])->values()->all(),
            'floor_plans' => $unit->floorPlans->map(fn ($plan): array => [
                'id' => $plan->id,
                'name' => $plan->name,
            ])->values()->all(),
            'documents' => $unit->documents->map(fn ($document): array => [
                'id' => $document->id,
                'name' => $document->name,
            ])->values()->all(),
            'specifications' => $unit->specifications->map(fn ($specification): array => [
                'id' => $specification->id,
                'name' => $specification->name_en ?? $specification->name_ar ?? $specification->key,
                'value' => $specification->value,
            ])->values()->all(),
            'marketplace' => [
                'sale' => null,
                'rent' => null,
            ],
            'rooms' => $unit->rooms->map(fn ($room): array => [
                'id' => $room->id,
                'name' => $room->name,
                'value' => $room->count,
            ])->values()->all(),
            'areas' => $unit->areas->map(fn ($area): array => [
                'id' => $area->id,
                'name' => $area->name_en ?? $area->name_ar ?? $area->type,
                'value' => $area->size,
            ])->values()->all(),
            'amenities' => ($unit->relationLoaded('features') ? $unit->features : collect())->map(fn ($feature): array => [
                'id' => $feature->id,
                'name' => $feature->name_en ?? $feature->name,
            ])->values()->all(),
            'pricing' => [
                'currency' => $unit->currency ? [
                    'id' => $unit->currency->id,
                    'code' => $unit->currency->code,
                    'symbol' => $unit->currency->symbol,
                ] : null,
                'amount' => $unit->asking_rent_amount,
                'period' => $unit->rent_period,
            ],
            'merge_document' => [],
        ];
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
