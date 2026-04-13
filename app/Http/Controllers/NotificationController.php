<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    /**
     * Display the notifications page.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('notifications/index', [
            'notifications' => $this->notificationService->getUserNotifications($user, 50),
            'statistics' => $this->notificationService->getNotificationStatistics($user),
            'preferences' => $this->notificationService->getNotificationPreferences($user),
        ]);
    }

    /**
     * Get all notifications (API).
     */
    public function list(Request $request): JsonResponse
    {
        $user = $request->user();
        $limit = $request->input('limit', 20);

        return response()->json([
            'notifications' => $this->notificationService->getUserNotifications($user, (int) $limit),
            'unread_count' => $this->notificationService->getUnreadCount($user),
        ]);
    }

    /**
     * Get unread notifications (API).
     */
    public function unread(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'notifications' => $this->notificationService->getUnreadNotifications($user),
            'count' => $this->notificationService->getUnreadCount($user),
        ]);
    }

    /**
     * Get unread notification count (API).
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'count' => $this->notificationService->getUnreadCount($user),
        ]);
    }

    /**
     * Mark a notification as read (API).
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = DatabaseNotification::findOrFail($id);

        // Verify the notification belongs to the current user
        if ($notification->notifiable_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->notificationService->markAsRead($notification);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read (API).
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();

        $this->notificationService->markAllAsRead($user);

        return response()->json(['success' => true]);
    }

    /**
     * Delete a notification (API).
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $notification = DatabaseNotification::findOrFail($id);

        // Verify the notification belongs to the current user
        if ($notification->notifiable_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->notificationService->deleteNotification($notification);

        return response()->json(['success' => true]);
    }

    /**
     * Delete all notifications (API).
     */
    public function destroyAll(Request $request): JsonResponse
    {
        $user = $request->user();

        $this->notificationService->deleteAllNotifications($user);

        return response()->json(['success' => true]);
    }

    /**
     * Get notification preferences (API).
     */
    public function preferences(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'preferences' => $this->notificationService->getNotificationPreferences($user),
        ]);
    }

    /**
     * Update notification preferences (API).
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'email_lease_expiring' => 'sometimes|boolean',
            'email_service_request' => 'sometimes|boolean',
            'email_payment_reminder' => 'sometimes|boolean',
            'push_lease_expiring' => 'sometimes|boolean',
            'push_service_request' => 'sometimes|boolean',
            'push_payment_reminder' => 'sometimes|boolean',
            'inapp_enabled' => 'sometimes|boolean',
        ]);

        $this->notificationService->updateNotificationPreferences($user, $validated);

        return response()->json([
            'success' => true,
            'preferences' => $this->notificationService->getNotificationPreferences($user),
        ]);
    }

    /**
     * Get notification statistics (API).
     */
    public function statistics(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json(
            $this->notificationService->getNotificationStatistics($user)
        );
    }
}
