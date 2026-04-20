<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    public function index(Request $request): Response
    {
        $announcements = Announcement::query()
            ->with(['community', 'building'])
            ->latest()
            ->paginate(15);

        return Inertia::render('communication/announcements/Index', [
            'announcements' => $announcements,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('communication/announcements/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'community_id' => ['nullable', 'integer', 'exists:rf_communities,id'],
            'building_id' => ['nullable', 'integer', 'exists:rf_buildings,id'],
        ]);

        $announcement = Announcement::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Announcement created.')]);

        return to_route('announcements.show', $announcement);
    }

    public function show(Announcement $announcement): Response
    {
        $announcement->load(['community', 'building']);

        return Inertia::render('communication/announcements/Show', [
            'announcement' => $announcement,
        ]);
    }

    public function edit(Announcement $announcement): Response
    {
        return Inertia::render('communication/announcements/Edit', [
            'announcement' => $announcement,
        ]);
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'community_id' => ['nullable', 'integer', 'exists:rf_communities,id'],
            'building_id' => ['nullable', 'integer', 'exists:rf_buildings,id'],
        ]);

        $announcement->update($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Announcement updated.')]);

        return to_route('announcements.show', $announcement);
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Announcement deleted.')]);

        return to_route('announcements.index');
    }
}
