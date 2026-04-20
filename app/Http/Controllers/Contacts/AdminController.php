<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\Controller;
use App\Models\Admin;
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
        return Inertia::render('contacts/admins/Create');
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
            'gender' => ['nullable', 'in:male,female'],
        ]);

        $admin = Admin::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Admin created.')]);

        return to_route('admins.show', $admin);
    }

    public function show(Admin $admin): Response
    {
        return Inertia::render('contacts/admins/Show', [
            'admin' => $admin,
        ]);
    }

    public function edit(Admin $admin): Response
    {
        return Inertia::render('contacts/admins/Edit', [
            'admin' => $admin,
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
            'gender' => ['nullable', 'in:male,female'],
        ]);

        $admin->update($validated);

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
