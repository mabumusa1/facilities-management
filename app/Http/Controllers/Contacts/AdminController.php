<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Building;
use App\Models\Community;
use App\Models\Country;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function index(Request $request): Response
    {
        $admins = Admin::query()
            ->latest()
            ->paginate(15);

        return Inertia::render('contacts/admins/Index', [
            'admins' => $admins,
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

    public function store(Request $request): RedirectResponse
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

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Admin created.')]);

        return to_route('admins.show', $admin);
    }

    public function show(Admin $admin): Response
    {
        $admin->load(['communities', 'buildings']);

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

    public function update(Request $request, Admin $admin): RedirectResponse
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

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Admin updated.')]);

        return to_route('admins.show', $admin);
    }

    public function destroy(Admin $admin): RedirectResponse
    {
        $admin->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Admin deleted.')]);

        return to_route('admins.index');
    }
}
