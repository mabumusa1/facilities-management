<?php

namespace App\Services;

use App\Models\Lease;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Notifications\LeaseExpiringNotification;
use App\Notifications\ServiceRequestCreatedNotification;
use App\Notifications\ServiceRequestStatusChangedNotification;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Service for managing notifications.
 */
class NotificationService
{
    /**
     * Get notifications for a user.
     *
     * @return Collection<int, DatabaseNotification>
     */
    public function getUserNotifications(User $user, int $limit = 20): Collection
    {
        return $user->notifications()->latest()->take($limit)->get();
    }

    /**
     * Get unread notifications for a user.
     *
     * @return Collection<int, DatabaseNotification>
     */
    public function getUnreadNotifications(User $user): Collection
    {
        return $user->unreadNotifications;
    }

    /**
     * Get unread notification count for a user.
     */
    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(DatabaseNotification $notification): void
    {
        $notification->markAsRead();
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(User $user): void
    {
        $user->unreadNotifications->markAsRead();
    }

    /**
     * Delete a notification.
     */
    public function deleteNotification(DatabaseNotification $notification): void
    {
        $notification->delete();
    }

    /**
     * Delete all notifications for a user.
     */
    public function deleteAllNotifications(User $user): void
    {
        $user->notifications()->delete();
    }

    /**
     * Send lease expiring notification.
     */
    public function sendLeaseExpiringNotification(Lease $lease, User $user): void
    {
        $user->notify(new LeaseExpiringNotification($lease));
    }

    /**
     * Send service request created notification.
     */
    public function sendServiceRequestCreatedNotification(ServiceRequest $serviceRequest, User $user): void
    {
        $user->notify(new ServiceRequestCreatedNotification($serviceRequest));
    }

    /**
     * Send service request status changed notification.
     */
    public function sendServiceRequestStatusChangedNotification(
        ServiceRequest $serviceRequest,
        User $user,
        string $oldStatus,
        string $newStatus
    ): void {
        $user->notify(new ServiceRequestStatusChangedNotification(
            $serviceRequest,
            $oldStatus,
            $newStatus
        ));
    }

    /**
     * Get notification preferences for a user.
     *
     * @return array<string, bool>
     */
    public function getNotificationPreferences(User $user): array
    {
        $preferences = $user->notification_preferences ?? [];

        return [
            'email_lease_expiring' => $preferences['email_lease_expiring'] ?? true,
            'email_service_request' => $preferences['email_service_request'] ?? true,
            'email_payment_reminder' => $preferences['email_payment_reminder'] ?? true,
            'push_lease_expiring' => $preferences['push_lease_expiring'] ?? true,
            'push_service_request' => $preferences['push_service_request'] ?? true,
            'push_payment_reminder' => $preferences['push_payment_reminder'] ?? true,
            'inapp_enabled' => $preferences['inapp_enabled'] ?? true,
        ];
    }

    /**
     * Update notification preferences for a user.
     *
     * @param  array<string, bool>  $preferences
     */
    public function updateNotificationPreferences(User $user, array $preferences): void
    {
        $user->update([
            'notification_preferences' => array_merge(
                $this->getNotificationPreferences($user),
                $preferences
            ),
        ]);
    }

    /**
     * Get notifications grouped by date.
     *
     * @return array<string, Collection<int, DatabaseNotification>>
     */
    public function getNotificationsGroupedByDate(User $user, int $limit = 50): array
    {
        $notifications = $user->notifications()->latest()->take($limit)->get();

        return $notifications->groupBy(function ($notification) {
            $createdAt = Carbon::parse($notification->created_at);

            if ($createdAt->isToday()) {
                return 'Today';
            } elseif ($createdAt->isYesterday()) {
                return 'Yesterday';
            } elseif ($createdAt->isCurrentWeek()) {
                return 'This Week';
            } elseif ($createdAt->isCurrentMonth()) {
                return 'This Month';
            }

            return 'Older';
        })->toArray();
    }

    /**
     * Get notification statistics for a user.
     *
     * @return array<string, int>
     */
    public function getNotificationStatistics(User $user): array
    {
        $notifications = $user->notifications();

        return [
            'total' => (clone $notifications)->count(),
            'unread' => $user->unreadNotifications()->count(),
            'read' => (clone $notifications)->whereNotNull('read_at')->count(),
            'today' => (clone $notifications)->whereDate('created_at', Carbon::today())->count(),
            'this_week' => (clone $notifications)->where('created_at', '>=', Carbon::now()->startOfWeek())->count(),
        ];
    }
}
