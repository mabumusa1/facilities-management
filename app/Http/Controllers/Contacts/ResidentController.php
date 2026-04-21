<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Resident;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResidentController extends Controller
{
    public function index(Request $request): Response
    {
        $residents = Resident::query()
            ->withCount(['units', 'leases'])
            ->latest()
            ->paginate(15);

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

    public function store(Request $request): RedirectResponse
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

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Tenant created.')]);

        return to_route('residents.show', $resident);
    }

    public function show(Resident $resident): Response
    {
        $resident->loadCount(['units', 'leases'])->load(['units.community', 'units.building', 'leases.status', 'dependents']);

        return Inertia::render('contacts/tenants/Show', [
            'resident' => $resident,
        ]);
    }

    public function edit(Resident $resident): Response
    {
        return Inertia::render('contacts/tenants/Edit', [
            'resident' => $resident,
            'countries' => Country::select('id', 'name', 'name_en')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Resident $resident): RedirectResponse
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

        $resident->update($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Tenant updated.')]);

        return to_route('residents.show', $resident);
    }

    public function destroy(Resident $resident): RedirectResponse
    {
        $resident->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Tenant deleted.')]);

        return to_route('residents.index');
    }
}
