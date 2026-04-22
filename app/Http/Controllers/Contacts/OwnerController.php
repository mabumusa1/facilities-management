<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Owner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

class OwnerController extends Controller
{
    public function index(Request $request): JsonResponse|Response
    {
        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $search = trim((string) $request->input('search', ''));
            $perPage = min(max((int) $request->integer('per_page', 10), 1), 50);

            $owners = Owner::query()
                ->with('units:id,owner_id,name')
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

            return response()->json([
                'data' => collect($owners->items())->map(fn (Owner $owner): array => $this->ownerListItem($owner)),
                'meta' => $this->meta($owners),
            ]);
        }

        $owners = Owner::query()
            ->withCount('units')
            ->latest()
            ->paginate(15);

        return Inertia::render('contacts/owners/Index', [
            'owners' => $owners,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('contacts/owners/Create', [
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
            'active' => ['sometimes', 'boolean'],
        ]);

        $owner = Owner::create($validated);

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $owner->load('units:id,owner_id,name');

            return response()->json([
                'data' => $this->ownerDetails($owner),
                'message' => __('Owner created.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Owner created.')]);

        return to_route('owners.show', $owner);
    }

    public function show(Request $request, Owner $owner): JsonResponse|Response
    {
        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $owner->load('units:id,owner_id,name');

            return response()->json([
                'data' => $this->ownerDetails($owner),
                'message' => __('Owner retrieved.'),
            ]);
        }

        $owner->loadCount('units')->load(['units.community', 'units.building']);

        return Inertia::render('contacts/owners/Show', [
            'owner' => $owner,
        ]);
    }

    public function edit(Owner $owner): Response
    {
        return Inertia::render('contacts/owners/Edit', [
            'owner' => $owner,
            'countries' => Country::select('id', 'name', 'name_en')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Owner $owner): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $validated = $request->validate([
                'first_name' => ['sometimes', 'string', 'max:255'],
                'last_name' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255'],
                'phone_number' => ['required', 'string', 'max:20'],
                'phone_country_code' => ['required', 'string', 'max:5'],
                'national_id' => ['nullable', 'string', 'max:50'],
                'nationality_id' => ['nullable', 'integer', 'exists:countries,id'],
                'gender' => ['nullable', 'in:male,female'],
                'georgian_birthdate' => ['nullable', 'date'],
                'active' => ['sometimes', 'boolean'],
            ]);

            $owner->update($validated);
            $owner->load('units:id,owner_id,name');

            return response()->json([
                'data' => $this->ownerDetails($owner),
                'message' => __('Owner updated.'),
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
            'active' => ['sometimes', 'boolean'],
        ]);

        $owner->update($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Owner updated.')]);

        return to_route('owners.show', $owner);
    }

    public function destroy(Request $request, Owner $owner): JsonResponse|RedirectResponse
    {
        $ownerId = $owner->id;
        $owner->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $ownerId,
                ],
                'message' => __('Owner deleted.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Owner deleted.')]);

        return to_route('owners.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function ownerListItem(Owner $owner): array
    {
        return [
            'id' => $owner->id,
            'name' => $owner->name,
            'image' => $owner->image,
            'phone_number' => $this->fullPhoneNumber($owner->phone_country_code, $owner->phone_number),
            'created_at' => $owner->created_at?->toJSON(),
            'units' => $owner->units->map(fn ($unit): array => [
                'id' => $unit->id,
                'name' => $unit->name,
            ])->values()->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function ownerDetails(Owner $owner): array
    {
        $nationality = null;

        if ($owner->nationality_id !== null) {
            $nationality = Country::query()
                ->whereKey($owner->nationality_id)
                ->value('name');
        }

        return [
            'id' => $owner->id,
            'name' => $owner->name,
            'first_name' => $owner->first_name,
            'last_name' => $owner->last_name,
            'image' => $owner->image,
            'email' => $owner->email,
            'georgian_birthdate' => $owner->georgian_birthdate?->toDateString(),
            'gender' => $owner->gender?->value ?? $owner->gender,
            'national_id' => $owner->national_id,
            'phone_number' => $this->fullPhoneNumber($owner->phone_country_code, $owner->phone_number),
            'national_phone_number' => $owner->national_phone_number ?? $owner->phone_number,
            'phone_country_code' => $owner->phone_country_code,
            'nationality' => $nationality,
            'created_at' => $owner->created_at?->toJSON(),
            'active' => $owner->active ? '1' : '0',
            'account_creation_date' => $owner->created_at?->format('Y-m-d h:i a'),
            'last_active' => $owner->last_active?->toJSON(),
            'units' => $owner->units->map(fn ($unit): array => [
                'id' => $unit->id,
                'name' => $unit->name,
            ])->values()->all(),
            'active_requests' => [],
            'transaction' => [],
            'relation' => $owner->relation,
            'relation_key' => $owner->relation_key,
        ];
    }

    private function fullPhoneNumber(?string $phoneCountryCode, ?string $phoneNumber): ?string
    {
        if ($phoneNumber === null || $phoneNumber === '') {
            return null;
        }

        if (str_starts_with($phoneNumber, '+')) {
            return $phoneNumber;
        }

        $countryCode = strtoupper(trim((string) $phoneCountryCode));

        $dialingCode = match ($countryCode) {
            'SA' => '+966',
            'AE' => '+971',
            'KW' => '+965',
            'BH' => '+973',
            'QA' => '+974',
            'OM' => '+968',
            default => trim((string) $phoneCountryCode),
        };

        if ($dialingCode === '') {
            return $phoneNumber;
        }

        $normalizedPhone = ltrim($phoneNumber, '0');

        if (str_starts_with($dialingCode, '+')) {
            return $dialingCode.$normalizedPhone;
        }

        if (ctype_digit($dialingCode)) {
            return '+'.$dialingCode.$normalizedPhone;
        }

        return $phoneNumber;
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
