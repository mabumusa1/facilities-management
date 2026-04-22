<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Dependent;
use App\Models\Request as ServiceRequest;
use App\Models\Resident;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

class ResidentController extends Controller
{
    public function index(Request $request): JsonResponse|Response
    {
        $search = trim((string) $request->input('search', ''));
        $perPage = min(max((int) $request->integer('per_page', 10), 1), 50);

        $residents = Resident::query()
            ->withCount(['units', 'leases'])
            ->with(['units:id,tenant_id,name'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nestedQuery) use ($search): void {
                    $nestedQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");

                    if (is_numeric($search)) {
                        $nestedQuery->orWhere('id', (int) $search);
                    }
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            return response()->json([
                'data' => collect($residents->items())->map(
                    fn (Resident $resident): array => $this->residentListItem($resident)
                ),
                'meta' => $this->meta($residents),
            ]);
        }

        return Inertia::render('contacts/tenants/Index', [
            'residents' => $residents,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('contacts/tenants/Create', [
            'countries' => Country::select('id', 'name', 'name_en')->orderBy('name')->get(),
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
            'national_id' => ['nullable', 'string', 'max:50'],
            'nationality_id' => ['nullable', 'integer', 'exists:countries,id'],
            'gender' => ['nullable', 'in:male,female'],
            'georgian_birthdate' => ['nullable', 'date'],
            'source_id' => ['nullable', 'integer'],
            'active' => ['sometimes', 'boolean'],
        ]);

        $resident = Resident::create($validated);

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $resident->load([
                'units:id,tenant_id,name',
                'leases:id,tenant_id,contract_number,status_id',
                'documents:id,mediable_id,mediable_type,name,collection',
            ]);

            return response()->json([
                'data' => $this->residentDetails($resident),
                'message' => __('Tenant created.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Tenant created.')]);

        return to_route('residents.show', $resident);
    }

    public function storeFamilyMember(Request $request, Resident $resident): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'phone_country_code' => ['nullable', 'string', 'max:5'],
            'email' => ['nullable', 'email', 'max:255'],
            'national_id' => ['nullable', 'string', 'max:50'],
            'gender' => ['nullable', 'in:male,female'],
            'birthdate' => ['nullable', 'date'],
            'relationship' => ['nullable', 'string', 'max:255'],
        ]);

        $dependent = $resident->dependents()->create($validated);

        return response()->json([
            'data' => [
                'id' => $dependent->id,
                'first_name' => $dependent->first_name,
                'last_name' => $dependent->last_name,
                'phone_number' => $dependent->phone_number,
                'phone_country_code' => $dependent->phone_country_code,
                'email' => $dependent->email,
                'national_id' => $dependent->national_id,
                'gender' => $dependent->gender,
                'birthdate' => $dependent->birthdate?->toDateString(),
                'relationship' => $dependent->relationship,
            ],
            'message' => __('Family member created.'),
        ]);
    }

    public function show(Resident $resident): Response
    {
        $resident->loadCount(['units', 'leases'])->load(['units.community', 'units.building', 'leases.status', 'dependents']);

        return Inertia::render('contacts/tenants/Show', [
            'resident' => $resident,
        ]);
    }

    public function rfShow(Request $request, Resident $resident): JsonResponse
    {
        $resident->load([
            'units:id,tenant_id,name',
            'leases:id,tenant_id,contract_number,status_id',
            'documents:id,mediable_id,mediable_type,name,collection',
        ]);

        return response()->json([
            'data' => $this->residentDetails($resident),
            'message' => __('Tenant retrieved.'),
        ]);
    }

    public function edit(Resident $resident): Response
    {
        return Inertia::render('contacts/tenants/Edit', [
            'resident' => $resident,
            'countries' => Country::select('id', 'name', 'name_en')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Resident $resident): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $validated = $request->validate([
                'first_name' => ['sometimes', 'string', 'max:255'],
                'last_name' => ['sometimes', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255'],
                'phone_number' => ['required', 'string', 'max:20'],
                'phone_country_code' => ['required', 'string', 'max:5'],
                'national_id' => ['nullable', 'string', 'max:50'],
                'nationality_id' => ['nullable', 'integer', 'exists:countries,id'],
                'gender' => ['nullable', 'in:male,female'],
                'georgian_birthdate' => ['nullable', 'date'],
                'source_id' => ['nullable', 'integer'],
                'active' => ['sometimes', 'boolean'],
            ]);

            $resident->update($validated);
            $resident->load([
                'units:id,tenant_id,name',
                'leases:id,tenant_id,contract_number,status_id',
                'documents:id,mediable_id,mediable_type,name,collection',
            ]);

            return response()->json([
                'data' => $this->residentDetails($resident),
                'message' => __('Tenant updated.'),
            ]);
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'phone_country_code' => ['required', 'string', 'max:5'],
            'national_id' => ['nullable', 'string', 'max:50'],
            'nationality_id' => ['nullable', 'integer', 'exists:countries,id'],
            'gender' => ['nullable', 'in:male,female'],
            'georgian_birthdate' => ['nullable', 'date'],
            'source_id' => ['nullable', 'integer'],
            'active' => ['sometimes', 'boolean'],
        ]);

        $resident->update($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Tenant updated.')]);

        return to_route('residents.show', $resident);
    }

    public function destroy(Request $request, Resident $resident): JsonResponse|RedirectResponse
    {
        $residentId = $resident->id;
        $resident->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $residentId,
                ],
                'message' => __('Tenant deleted.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Tenant deleted.')]);

        return to_route('residents.index');
    }

    public function destroyFamilyMember(Request $request, Resident $resident, Dependent $dependent): JsonResponse|RedirectResponse
    {
        if (
            $dependent->dependable_type !== Resident::class
            || (int) $dependent->dependable_id !== $resident->id
        ) {
            abort(404);
        }

        $dependentId = $dependent->id;
        $dependent->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $dependentId,
                ],
                'message' => __('Family member deleted.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Family member deleted.')]);

        return to_route('residents.show', $resident);
    }

    /**
     * @return array<string, mixed>
     */
    private function residentListItem(Resident $resident): array
    {
        return [
            'id' => $resident->id,
            'name' => trim(($resident->first_name ?? '').' '.($resident->last_name ?? '')),
            'image' => $resident->image,
            'phone_number' => $this->fullPhoneNumber($resident),
            'invited' => $resident->accepted_invite ? '1' : '0',
            'created_at' => $resident->created_at?->toDateTimeString(),
            'units' => $resident->units->map(fn ($unit): array => [
                'id' => $unit->id,
                'name' => $unit->name,
            ])->values()->all(),
            'accepted_invite' => $resident->accepted_invite ? 1 : 0,
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

    private function fullPhoneNumber(Resident $resident): ?string
    {
        if ($resident->phone_number === null) {
            return null;
        }

        $phone = ltrim((string) $resident->phone_number, '+');
        $countryCode = strtoupper((string) ($resident->phone_country_code ?? ''));
        $dialCode = $countryCode === 'SA' ? '966' : '';

        if ($dialCode !== '' && str_starts_with($phone, '0')) {
            $phone = ltrim($phone, '0');
        }

        if ($dialCode !== '' && str_starts_with($phone, $dialCode)) {
            return '+'.$phone;
        }

        if ($dialCode !== '') {
            return '+'.$dialCode.$phone;
        }

        return '+'.$phone;
    }

    /**
     * @return array<string, mixed>
     */
    private function residentDetails(Resident $resident): array
    {
        $activeRequests = ServiceRequest::query()
            ->where('requester_type', Resident::class)
            ->where('requester_id', $resident->id)
            ->latest()
            ->limit(10)
            ->get(['id', 'title', 'status_id'])
            ->map(fn (ServiceRequest $serviceRequest): array => [
                'id' => $serviceRequest->id,
                'title' => $serviceRequest->title,
                'status_id' => $serviceRequest->status_id,
            ])
            ->values()
            ->all();

        $transactions = Transaction::query()
            ->where('assignee_type', Resident::class)
            ->where('assignee_id', $resident->id)
            ->latest()
            ->limit(10)
            ->get(['id', 'amount', 'status_id'])
            ->map(fn (Transaction $transaction): array => [
                'id' => $transaction->id,
                'amount' => $transaction->amount,
                'status_id' => $transaction->status_id,
            ])
            ->values()
            ->all();

        return [
            'id' => $resident->id,
            'name' => trim($resident->first_name.' '.$resident->last_name),
            'first_name' => $resident->first_name,
            'last_name' => $resident->last_name,
            'image' => $resident->image,
            'email' => $resident->email,
            'georgian_birthdate' => $resident->georgian_birthdate?->toDateString(),
            'gender' => $resident->gender,
            'national_id' => $resident->national_id,
            'phone_number' => $this->fullPhoneNumber($resident),
            'national_phone_number' => $resident->national_phone_number,
            'phone_country_code' => $resident->phone_country_code,
            'nationality' => null,
            'created_at' => $resident->created_at?->toJSON(),
            'active' => $resident->active ? '1' : '0',
            'account_creation_date' => $resident->created_at?->format('Y-m-d h:i a'),
            'last_active' => $resident->last_active,
            'units' => $resident->units->map(fn ($unit): array => [
                'id' => $unit->id,
                'name' => $unit->name,
            ])->values()->all(),
            'leases' => $resident->leases->map(fn ($lease): array => [
                'id' => $lease->id,
                'contract_number' => $lease->contract_number,
            ])->values()->all(),
            'active_requests' => $activeRequests,
            'transaction' => $transactions,
            'relation' => $resident->relation,
            'relation_key' => $resident->relation_key,
            'documents' => $resident->documents->map(fn ($document): array => [
                'id' => $document->id,
                'name' => $document->name,
            ])->values()->all(),
            'source' => null,
            'accepted_invite' => $resident->accepted_invite ? 1 : 0,
        ];
    }
}
