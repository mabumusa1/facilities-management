<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function index(Request $request): Response|JsonResponse
    {
        $user = $request->user();

        abort_unless($user !== null, 401);

        $perPage = min(max((int) $request->integer('per_page', 10), 1), 50);

        $notifications = $user->notifications()
            ->latest()
            ->paginate($perPage)
            ->through(fn (DatabaseNotification $notification): array => [
                'id' => $notification->id,
                'text' => (string) data_get($notification->data, 'text', data_get($notification->data, 'title', 'Notification')),
                'data' => $notification->data,
                'type' => class_basename($notification->type),
                'read' => $notification->read_at?->toISOString(),
                'created_at' => $notification->created_at?->format('Y-m-d H:i:s'),
            ]);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $notifications->items(),
                'meta' => [
                    'current_page' => $notifications->currentPage(),
                    'from' => $notifications->firstItem(),
                    'last_page' => $notifications->lastPage(),
                    'path' => $notifications->path(),
                    'per_page' => $notifications->perPage(),
                    'to' => $notifications->lastItem(),
                    'total' => $notifications->total(),
                ],
            ]);
        }

        return Inertia::render('notifications/Index', [
            'notifications' => $notifications,
            'unreadCount' => $user->unreadNotifications()->count(),
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $user = $request->user();

        abort_unless($user !== null, 401);

        return response()->json([
            'data' => [
                'count' => $user->unreadNotifications()->count(),
            ],
        ]);
    }

    public function markAsRead(Request $request, string $notification): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        abort_unless($user !== null, 401);

        $record = $user->notifications()->where('id', $notification)->first();

        abort_unless($record instanceof DatabaseNotification, 404);

        if (! $record->read_at) {
            $record->markAsRead();
        }

        if (! $request->expectsJson()) {
            return back();
        }

        return response()->json([
            'data' => [
                'id' => $record->id,
                'read' => $record->read_at?->toISOString(),
            ],
            'message' => __('Notification marked as read.'),
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        abort_unless($user !== null, 401);

        $count = $user->unreadNotifications()->count();
        $user->unreadNotifications->markAsRead();

        if (! $request->expectsJson()) {
            return back();
        }

        return response()->json([
            'data' => [
                'count' => $count,
            ],
            'message' => __('All notifications marked as read.'),
        ]);
    }
}
