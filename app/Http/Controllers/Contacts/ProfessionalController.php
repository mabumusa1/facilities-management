<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfessionalController extends Controller
{
    public function index(Request $request): Response
    {
        $professionals = Professional::query()
            ->latest()
            ->paginate(15);

        return Inertia::render('contacts/professionals/Index', [
            'professionals' => $professionals,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('contacts/professionals/Create');
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
        ]);

        $professional = Professional::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Professional created.')]);

        return to_route('professionals.show', $professional);
    }

    public function show(Professional $professional): Response
    {
        return Inertia::render('contacts/professionals/Show', [
            'professional' => $professional,
        ]);
    }

    public function edit(Professional $professional): Response
    {
        return Inertia::render('contacts/professionals/Edit', [
            'professional' => $professional,
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
        ]);

        $professional->update($validated);

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
