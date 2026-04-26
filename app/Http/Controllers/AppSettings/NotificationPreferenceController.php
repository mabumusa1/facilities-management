<?php

namespace App\Http\Controllers\AppSettings;

use App\Http\Controllers\Controller;
use App\Models\NotificationPreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotificationPreferenceController extends Controller
{
    protected static array $domains = ['leasing', 'accounting', 'service_requests', 'facilities', 'visitor_access'];

    public function index(Request $request): JsonResponse|Response
    {
        $preferences = NotificationPreference::query()
            ->orderBy('domain')
            ->orderBy('trigger_key')
            ->get();

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            return response()->json(['data' => $preferences]);
        }

        return Inertia::render('app-settings/notifications/Index', [
            'preferences' => $preferences->groupBy('domain'),
            'domains' => self::$domains,
        ]);
    }

    public function update(Request $request, NotificationPreference $preference): JsonResponse
    {
        $validated = $request->validate([
            'email_enabled' => ['sometimes', 'boolean'],
            'sms_enabled' => ['sometimes', 'boolean'],
            'email_template' => ['nullable', 'array'],
            'sms_template' => ['nullable', 'array'],
        ]);

        $preference->update($validated);

        return response()->json([
            'data' => $preference->fresh(),
            'message' => __('Notification preference updated.'),
        ]);
    }
}
