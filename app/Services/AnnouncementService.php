<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AnnouncementService
{
    /**
     * Get paginated announcements for a tenant.
     */
    public function getAnnouncementsForTenant(?int $tenantId, int $perPage = 15, ?string $status = null): LengthAwarePaginator
    {
        $query = Announcement::query()
            ->when($tenantId !== null, fn ($q) => $q->where('tenant_id', $tenantId))
            ->with('creator')
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get active announcements for a user.
     */
    public function getActiveAnnouncementsForUser(User $user): Collection
    {
        return Announcement::query()
            ->when($user->tenant_id !== null, fn ($q) => $q->where('tenant_id', $user->tenant_id))
            ->active()
            ->orderBy('priority', 'desc')
            ->orderBy('start_date', 'desc')
            ->get();
    }

    /**
     * Get announcement statistics for a tenant.
     *
     * @return array<string, int>
     */
    public function getAnnouncementStatistics(?int $tenantId): array
    {
        $base = Announcement::query()
            ->when($tenantId !== null, fn ($q) => $q->where('tenant_id', $tenantId));

        return [
            'total' => (clone $base)->count(),
            'active' => (clone $base)->where('status', 'active')->count(),
            'draft' => (clone $base)->where('status', 'draft')->count(),
            'scheduled' => (clone $base)->where('status', 'scheduled')->count(),
            'expired' => (clone $base)->where('status', 'expired')->count(),
            'cancelled' => (clone $base)->where('status', 'cancelled')->count(),
        ];
    }

    /**
     * Create a new announcement.
     *
     * @param  array<string, mixed>  $data
     */
    public function createAnnouncement(array $data, User $creator): Announcement
    {
        $data['created_by'] = $creator->id;
        $data['tenant_id'] = $creator->tenant_id;

        // Auto-set status based on dates
        if (! isset($data['status']) || $data['status'] === 'draft') {
            $data['status'] = $this->determineStatus($data);
        }

        return Announcement::create($data);
    }

    /**
     * Update an announcement.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateAnnouncement(Announcement $announcement, array $data): Announcement
    {
        // Auto-update status based on dates if not explicitly set
        if (! isset($data['status'])) {
            $data['status'] = $this->determineStatus(array_merge($announcement->toArray(), $data));
        }

        $announcement->update($data);

        return $announcement->fresh();
    }

    /**
     * Delete an announcement.
     */
    public function deleteAnnouncement(Announcement $announcement): bool
    {
        return $announcement->delete();
    }

    /**
     * Publish an announcement (set status to active or scheduled).
     */
    public function publishAnnouncement(Announcement $announcement): Announcement
    {
        $status = $this->determineStatus($announcement->toArray());
        $announcement->update(['status' => $status]);

        return $announcement->fresh();
    }

    /**
     * Cancel an announcement.
     */
    public function cancelAnnouncement(Announcement $announcement): Announcement
    {
        $announcement->update(['status' => 'cancelled']);

        return $announcement->fresh();
    }

    /**
     * Determine the status based on dates.
     *
     * @param  array<string, mixed>  $data
     */
    protected function determineStatus(array $data): string
    {
        $startDate = $data['start_date'] ?? null;
        $endDate = $data['end_date'] ?? null;

        if (! $startDate || ! $endDate) {
            return 'draft';
        }

        $now = now();

        if ($endDate < $now) {
            return 'expired';
        }

        if ($startDate > $now) {
            return 'scheduled';
        }

        return 'active';
    }

    /**
     * Get announcements for directory view (active and visible only).
     */
    public function getAnnouncementsForDirectory(?int $tenantId): Collection
    {
        return Announcement::query()
            ->when($tenantId !== null, fn ($q) => $q->where('tenant_id', $tenantId))
            ->active()
            ->visible()
            ->with('creator')
            ->orderBy('priority', 'desc')
            ->orderBy('start_date', 'desc')
            ->get();
    }
}
