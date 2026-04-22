<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Models\RequestSubcategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

class ProfessionalController extends Controller
{
    public function index(Request $request): JsonResponse|Response
    {
        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $perPage = min(max((int) $request->integer('per_page', 10), 1), 50);

            $professionals = Professional::query()
                ->with('subcategories:id,name,name_ar,name_en')
                ->latest()
                ->paginate($perPage)
                ->withQueryString();

            return response()->json([
                'data' => collect($professionals->items())->map(fn (Professional $professional): array => [
                    'id' => $professional->id,
                    'name' => trim(($professional->first_name ?? '').' '.($professional->last_name ?? '')),
                    'image' => $professional->image,
                    'phone_number' => $this->fullPhoneNumber($professional->phone_country_code, $professional->phone_number),
                    'created_at' => $professional->created_at?->toJSON(),
                    'types' => $professional->subcategories->map(fn (RequestSubcategory $subcategory): array => [
                        'id' => $subcategory->id,
                        'name' => $subcategory->name,
                        'name_ar' => $subcategory->name_ar,
                        'name_en' => $subcategory->name_en,
                    ])->values()->all(),
                ]),
                'meta' => $this->meta($professionals),
            ]);
        }

        $professionals = Professional::query()
            ->withCount('requests')
            ->latest()
            ->paginate(15);

        return Inertia::render('contacts/professionals/Index', [
            'professionals' => $professionals,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('contacts/professionals/Create', [
            'subcategories' => RequestSubcategory::with('category')->select('id', 'name', 'name_en', 'category_id')->get(),
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
            'subcategory_ids' => ['nullable', 'array'],
            'subcategory_ids.*' => ['integer', 'exists:rf_request_subcategories,id'],
        ]);

        $professional = Professional::create(collect($validated)->except('subcategory_ids')->all());

        if (isset($validated['subcategory_ids'])) {
            $professional->subcategories()->sync($validated['subcategory_ids']);
        }

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $professional->load(['subcategories:id,name,name_ar,name_en']);

            return response()->json([
                'data' => $this->professionalDetails($professional),
                'message' => __('Professional created.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Professional created.')]);

        return to_route('professionals.show', $professional);
    }

    public function show(Professional $professional): Response
    {
        $professional->load(['subcategories'])->loadCount('requests');

        return Inertia::render('contacts/professionals/Show', [
            'professional' => $professional,
        ]);
    }

    public function edit(Professional $professional): Response
    {
        $professional->load('subcategories');

        return Inertia::render('contacts/professionals/Edit', [
            'professional' => $professional,
            'subcategories' => RequestSubcategory::with('category')->select('id', 'name', 'name_en', 'category_id')->get(),
        ]);
    }

    public function update(Request $request, Professional $professional): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'phone_country_code' => ['required', 'string', 'max:5'],
            'national_id' => ['nullable', 'string', 'max:50'],
            'subcategory_ids' => ['nullable', 'array'],
            'subcategory_ids.*' => ['integer', 'exists:rf_request_subcategories,id'],
        ]);

        $professional->update(collect($validated)->except('subcategory_ids')->all());

        if (array_key_exists('subcategory_ids', $validated)) {
            $professional->subcategories()->sync($validated['subcategory_ids'] ?? []);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Professional updated.')]);

        return to_route('professionals.show', $professional);
    }

    public function destroy(Request $request, Professional $professional): JsonResponse|RedirectResponse
    {
        $professionalId = $professional->id;
        $professional->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $professionalId,
                ],
                'message' => __('Professional deleted.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Professional deleted.')]);

        return to_route('professionals.index');
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

    /**
     * @return array<string, mixed>
     */
    private function professionalDetails(Professional $professional): array
    {
        return [
            'id' => $professional->id,
            'name' => trim(($professional->first_name ?? '').' '.($professional->last_name ?? '')),
            'first_name' => $professional->first_name,
            'last_name' => $professional->last_name,
            'image' => $professional->image,
            'email' => $professional->email,
            'phone_number' => $this->fullPhoneNumber($professional->phone_country_code, $professional->phone_number),
            'phone_country_code' => $professional->phone_country_code,
            'national_id' => $professional->national_id,
            'active' => $professional->active ? '1' : '0',
            'created_at' => $professional->created_at?->toJSON(),
            'types' => $professional->subcategories->map(fn (RequestSubcategory $subcategory): array => [
                'id' => $subcategory->id,
                'name' => $subcategory->name,
                'name_ar' => $subcategory->name_ar,
                'name_en' => $subcategory->name_en,
            ])->values()->all(),
        ];
    }
}
