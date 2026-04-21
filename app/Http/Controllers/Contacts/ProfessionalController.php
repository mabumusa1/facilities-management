<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Models\RequestSubcategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfessionalController extends Controller
{
    public function index(Request $request): Response
    {
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

    public function store(Request $request): RedirectResponse
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

    public function destroy(Professional $professional): RedirectResponse
    {
        $professional->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Professional deleted.')]);

        return to_route('professionals.index');
    }
}
