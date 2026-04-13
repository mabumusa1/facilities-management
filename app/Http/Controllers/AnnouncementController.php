<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnnouncementRequest;
use App\Http\Requests\UpdateAnnouncementRequest;
use App\Models\Announcement;
use App\Services\AnnouncementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    public function __construct(
        protected AnnouncementService $announcementService
    ) {}

    /**
     * Display a listing of announcements.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $status = $request->query('status');

        return Inertia::render('announcements/index', [
            'announcements' => $this->announcementService->getAnnouncementsForTenant(
                $user->tenant_id,
                15,
                $status
            ),
            'statistics' => $this->announcementService->getAnnouncementStatistics($user->tenant_id),
            'filters' => [
                'status' => $status,
            ],
        ]);
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create(): Response
    {
        return Inertia::render('announcements/create');
    }

    /**
     * Store a newly created announcement.
     */
    public function store(StoreAnnouncementRequest $request): RedirectResponse
    {
        $announcement = $this->announcementService->createAnnouncement(
            $request->validated(),
            $request->user()
        );

        return redirect()
            ->route('announcements.show', $announcement)
            ->with('success', 'Announcement created successfully.');
    }

    /**
     * Display the specified announcement.
     */
    public function show(Announcement $announcement): Response
    {
        $announcement->load('creator');

        return Inertia::render('announcements/show', [
            'announcement' => $announcement,
        ]);
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit(Announcement $announcement): Response
    {
        return Inertia::render('announcements/edit', [
            'announcement' => $announcement,
        ]);
    }

    /**
     * Update the specified announcement.
     */
    public function update(UpdateAnnouncementRequest $request, Announcement $announcement): RedirectResponse
    {
        $this->announcementService->updateAnnouncement($announcement, $request->validated());

        return redirect()
            ->route('announcements.show', $announcement)
            ->with('success', 'Announcement updated successfully.');
    }

    /**
     * Remove the specified announcement.
     */
    public function destroy(Announcement $announcement): RedirectResponse
    {
        $this->announcementService->deleteAnnouncement($announcement);

        return redirect()
            ->route('announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }

    /**
     * Publish an announcement.
     */
    public function publish(Announcement $announcement): RedirectResponse
    {
        $this->announcementService->publishAnnouncement($announcement);

        return redirect()
            ->back()
            ->with('success', 'Announcement published successfully.');
    }

    /**
     * Cancel an announcement.
     */
    public function cancel(Announcement $announcement): RedirectResponse
    {
        $this->announcementService->cancelAnnouncement($announcement);

        return redirect()
            ->back()
            ->with('success', 'Announcement cancelled successfully.');
    }

    // API Methods

    /**
     * Get announcements list for API.
     */
    public function list(Request $request): JsonResponse
    {
        $user = $request->user();
        $status = $request->query('status');

        return response()->json([
            'announcements' => $this->announcementService->getAnnouncementsForTenant(
                $user->tenant_id,
                $request->query('per_page', 15),
                $status
            ),
        ]);
    }

    /**
     * Get active announcements for current user.
     */
    public function active(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'announcements' => $this->announcementService->getActiveAnnouncementsForUser($user),
        ]);
    }

    /**
     * Get announcement statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json(
            $this->announcementService->getAnnouncementStatistics($user->tenant_id)
        );
    }

    /**
     * Display the directory page.
     */
    public function directory(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('directory/index', [
            'announcements' => $this->announcementService->getAnnouncementsForDirectory($user->tenant_id),
        ]);
    }
}
