<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Owner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OwnerController extends Controller
{
    public function index(Request $request): Response
    {
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
            'active' => ['sometimes', 'boolean'],
        ]);

        $owner = Owner::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Owner created.')]);

        return to_route('owners.show', $owner);
    }

    public function show(Owner $owner): Response
    {
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

    public function update(Request $request, Owner $owner): RedirectResponse
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

        $owner->update($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Owner updated.')]);

        return to_route('owners.show', $owner);
    }

    public function destroy(Owner $owner): RedirectResponse
    {
        $owner->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Owner deleted.')]);

        return to_route('owners.index');
    }
}
