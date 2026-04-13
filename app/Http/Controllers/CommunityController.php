<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CommunityController extends Controller
{
    /**
     * Display a listing of communities.
     */
    public function index(Request $request): Response
    {
        $communities = Community::query()
            ->forTenant(auth()->user()->tenant_id)
            ->with(['city', 'district'])
            ->withCount('buildings')
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc')
            ->paginate($request->per_page ?? 15)
            ->withQueryString();

        return Inertia::render('properties/communities/index', [
            'communities' => $communities,
            'filters' => $request->only(['search', 'status', 'sort', 'direction']),
        ]);
    }

    /**
     * Show the form for creating a new community.
     */
    public function create(): Response
    {
        return Inertia::render('properties/communities/create');
    }

    /**
     * Store a newly created community.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'description' => ['nullable', 'string'],
            'map' => ['nullable', 'array'],
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['status'] = Community::STATUS_ACTIVE;

        $community = Community::create($validated);

        return redirect()
            ->route('communities.show', $community)
            ->with('success', 'Community created successfully.');
    }

    /**
     * Display the specified community.
     */
    public function show(Community $community): Response
    {
        $this->authorize('view', $community);

        $community->load(['city', 'district', 'buildings' => fn ($q) => $q->withCount('units')]);

        return Inertia::render('properties/communities/show', [
            'community' => $community,
        ]);
    }

    /**
     * Show the form for editing the specified community.
     */
    public function edit(Community $community): Response
    {
        $this->authorize('update', $community);

        return Inertia::render('properties/communities/edit', [
            'community' => $community,
        ]);
    }

    /**
     * Update the specified community.
     */
    public function update(Request $request, Community $community): RedirectResponse
    {
        $this->authorize('update', $community);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'description' => ['nullable', 'string'],
            'map' => ['nullable', 'array'],
            'status' => ['sometimes', 'in:active,inactive'],
        ]);

        $community->update($validated);

        return redirect()
            ->route('communities.show', $community)
            ->with('success', 'Community updated successfully.');
    }

    /**
     * Remove the specified community (soft delete).
     */
    public function destroy(Community $community): RedirectResponse
    {
        $this->authorize('delete', $community);

        $community->delete();

        return redirect()
            ->route('communities.index')
            ->with('success', 'Community deleted successfully.');
    }
}
