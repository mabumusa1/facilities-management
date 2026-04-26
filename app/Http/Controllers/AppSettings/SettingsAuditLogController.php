<?php

namespace App\Http\Controllers\AppSettings;

use App\Http\Controllers\Controller;
use App\Models\SettingsAuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsAuditLogController extends Controller
{
    public function index(Request $request): JsonResponse|Response
    {
        $logs = SettingsAuditLog::query()
            ->with('user:id,name')
            ->latest()
            ->paginate(30);

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            return response()->json([
                'data' => $logs->map(fn ($log): array => [
                    'id' => $log->id,
                    'user' => $log->user?->name,
                    'setting_group' => $log->setting_group,
                    'setting_key' => $log->setting_key,
                    'old_value' => $log->old_value,
                    'new_value' => $log->new_value,
                    'created_at' => $log->created_at->toJSON(),
                ]),
                'meta' => [
                    'current_page' => $logs->currentPage(),
                    'last_page' => $logs->lastPage(),
                    'total' => $logs->total(),
                ],
            ]);
        }

        return Inertia::render('app-settings/audit-log/Index', [
            'logs' => $logs,
        ]);
    }
}
