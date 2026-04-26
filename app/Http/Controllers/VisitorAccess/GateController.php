<?php

namespace App\Http\Controllers\VisitorAccess;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\VisitorAccessSetting;
use App\Models\VisitorInvitation;
use App\Models\VisitorLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GateController extends Controller
{
    /**
     * Gate Officer — today's expected visitors with search.
     */
    public function gateView(Request $request): JsonResponse
    {
        $search = trim((string) $request->input('search', ''));
        $communityId = $request->input('community_id');
        $perPage = min(max((int) $request->integer('per_page', 25), 10), 100);

        $today = now()->startOfDay();

        $invitations = VisitorInvitation::query()
            ->with(['resident:id,name', 'community:id,name'])
            ->whereBetween('expected_at', [$today, $today->copy()->endOfDay()])
            ->when($communityId, fn ($q) => $q->where('community_id', (int) $communityId))
            ->when($search !== '', function ($q) use ($search): void {
                $q->where(function ($sq) use ($search): void {
                    $sq->where('visitor_name', 'like', "%{$search}%")
                        ->orWhere('visitor_phone', 'like', "%{$search}%")
                        ->orWhere('qr_code_token', 'like', "%{$search}%");
                });
            })
            ->orderBy('expected_at')
            ->paginate($perPage);

        return response()->json([
            'data' => $invitations->map(fn ($inv): array => [
                'id' => $inv->id,
                'visitor_name' => $inv->visitor_name,
                'visitor_phone' => $inv->visitor_phone,
                'visitor_purpose' => $inv->visitor_purpose,
                'expected_at' => $inv->expected_at?->toJSON(),
                'status' => $inv->status,
                'qr_code_token' => $inv->qr_code_token,
                'resident' => $inv->resident?->name,
                'community' => $inv->community?->name,
                'checked_in' => $inv->logs()->whereNotNull('entry_at')->exists(),
                'checked_out' => $inv->logs()->whereNotNull('exit_at')->exists(),
            ]),
            'meta' => $this->meta($invitations),
        ]);
    }

    /**
     * Gate check-in — scan QR code or enter code manually.
     */
    public function checkIn(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'qr_code_token' => ['required', 'string'],
            'visitor_name' => ['nullable', 'string', 'max:255'],
            'visitor_phone' => ['nullable', 'string', 'max:20'],
            'id_verified' => ['sometimes', 'boolean'],
            'community_id' => ['nullable', 'integer'],
        ]);

        $invitation = VisitorInvitation::query()
            ->where('qr_code_token', $validated['qr_code_token'])
            ->first();

        // Walk-in: no invitation found, create manual log entry
        if ($invitation === null) {
            $walkInAllowed = VisitorAccessSetting::query()
                ->where('community_id', $validated['community_id'] ?? null)
                ->value('allow_walk_in') ?? false;

            if (! $walkInAllowed) {
                throw ValidationException::withMessages([
                    'qr_code_token' => 'Invitation not found. Walk-in admissions are not allowed for this community.',
                ]);
            }

            if (empty($validated['visitor_name'])) {
                throw ValidationException::withMessages([
                    'visitor_name' => 'Visitor name is required for walk-in admissions.',
                ]);
            }

            $log = VisitorLog::create([
                'account_tenant_id' => Tenant::current()?->id,
                'community_id' => $validated['community_id'] ?? null,
                'visitor_name' => $validated['visitor_name'],
                'visitor_phone' => $validated['visitor_phone'] ?? null,
                'purpose' => 'walk_in',
                'gate_officer_id' => $request->user()?->id,
                'entry_at' => now(),
                'id_verified' => $validated['id_verified'] ?? false,
            ]);

            return response()->json([
                'data' => [
                    'log_id' => $log->id,
                    'type' => 'walk_in',
                    'visitor_name' => $log->visitor_name,
                    'entry_at' => $log->entry_at?->toJSON(),
                ],
                'message' => 'Walk-in visitor admitted.',
            ]);
        }

        // Check invitation validity
        if ($invitation->status === 'cancelled') {
            throw ValidationException::withMessages([
                'qr_code_token' => 'This invitation has been cancelled.',
            ]);
        }

        if ($invitation->status === 'expired' || ($invitation->valid_until && now()->gt($invitation->valid_until))) {
            $invitation->update(['status' => 'expired']);

            throw ValidationException::withMessages([
                'qr_code_token' => 'This invitation has expired.',
            ]);
        }

        $this->enforceMaxScans($invitation);

        // Already checked in but not checked out — record additional entry? Or reject
        $activeLog = $invitation->logs()
            ->whereNotNull('entry_at')
            ->whereNull('exit_at')
            ->first();

        if ($activeLog !== null) {
            throw ValidationException::withMessages([
                'qr_code_token' => 'This visitor is already checked in and has not checked out.',
            ]);
        }

        $log = VisitorLog::create([
            'account_tenant_id' => $invitation->account_tenant_id,
            'invitation_id' => $invitation->id,
            'community_id' => $invitation->community_id,
            'visitor_name' => $invitation->visitor_name,
            'visitor_phone' => $invitation->visitor_phone,
            'purpose' => $invitation->visitor_purpose,
            'gate_officer_id' => $request->user()?->id,
            'entry_at' => now(),
            'id_verified' => $validated['id_verified'] ?? false,
        ]);

        $invitation->update(['status' => 'used']);

        return response()->json([
            'data' => [
                'log_id' => $log->id,
                'type' => 'check_in',
                'visitor_name' => $log->visitor_name,
                'entry_at' => $log->entry_at?->toJSON(),
            ],
            'message' => sprintf('Visitor %s checked in.', $log->visitor_name),
        ]);
    }

    /**
     * Gate check-out — mark visitor as departed.
     */
    public function checkOut(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'log_id' => ['required', 'integer', 'exists:rf_visitor_logs,id'],
        ]);

        $log = VisitorLog::findOrFail($validated['log_id']);

        if ($log->exit_at !== null) {
            throw ValidationException::withMessages([
                'log_id' => 'This visitor has already checked out.',
            ]);
        }

        $log->update(['exit_at' => now()]);

        return response()->json([
            'data' => [
                'log_id' => $log->id,
                'visitor_name' => $log->visitor_name,
                'entry_at' => $log->entry_at?->toJSON(),
                'exit_at' => $log->exit_at->toJSON(),
                'duration_minutes' => (int) $log->entry_at?->diffInMinutes($log->exit_at),
            ],
            'message' => sprintf('Visitor %s checked out.', $log->visitor_name),
        ]);
    }

    /**
     * Visitor log reporting — audit trail with filters.
     */
    public function logReport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'community_id' => ['nullable', 'integer'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $perPage = min(max((int) $request->integer('per_page', 25), 10), 100);

        $logs = VisitorLog::query()
            ->with(['invitation:id,visitor_name,visitor_phone,qr_code_token', 'community:id,name', 'gateOfficer:id,name'])
            ->when($validated['from'] ?? null, fn ($q) => $q->whereDate('entry_at', '>=', $validated['from']))
            ->when($validated['to'] ?? null, fn ($q) => $q->whereDate('entry_at', '<=', $validated['to']))
            ->when($validated['community_id'] ?? null, fn ($q) => $q->where('community_id', (int) $validated['community_id']))
            ->when($validated['search'] ?? null, fn ($q) => $q->where('visitor_name', 'like', "%{$validated['search']}%"))
            ->latest('entry_at')
            ->paginate($perPage);

        return response()->json([
            'data' => $logs->map(fn ($log): array => [
                'id' => $log->id,
                'visitor_name' => $log->visitor_name,
                'visitor_phone' => $log->visitor_phone,
                'purpose' => $log->purpose,
                'community' => $log->community?->name,
                'gate_officer' => $log->gateOfficer?->name,
                'entry_at' => $log->entry_at?->toJSON(),
                'exit_at' => $log->exit_at?->toJSON(),
                'id_verified' => $log->id_verified,
                'duration_minutes' => $log->exit_at && $log->entry_at
                    ? (int) $log->entry_at->diffInMinutes($log->exit_at)
                    : null,
                'overstay' => $log->exit_at === null && $log->entry_at?->diffInHours(now()) > 24,
            ]),
            'meta' => $this->meta($logs),
        ]);
    }

    /**
     * Overstay detection — flag visitors still checked in beyond allowed duration.
     */
    public function overstayReport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'community_id' => ['nullable', 'integer'],
            'max_hours' => ['nullable', 'integer', 'min:1', 'max:72'],
        ]);

        $hours = $validated['max_hours'] ?? 24;

        $overstays = VisitorLog::query()
            ->with(['community:id,name', 'gateOfficer:id,name'])
            ->whereNotNull('entry_at')
            ->whereNull('exit_at')
            ->where('entry_at', '<', now()->subHours($hours))
            ->when($validated['community_id'] ?? null, fn ($q) => $q->where('community_id', (int) $validated['community_id']))
            ->latest('entry_at')
            ->get();

        return response()->json([
            'data' => $overstays->map(fn ($log): array => [
                'log_id' => $log->id,
                'visitor_name' => $log->visitor_name,
                'visitor_phone' => $log->visitor_phone,
                'community' => $log->community?->name,
                'gate_officer' => $log->gateOfficer?->name,
                'entry_at' => $log->entry_at?->toJSON(),
                'hours_over' => (int) $log->entry_at?->diffInHours(now()),
            ]),
            'meta' => [
                'total_overstays' => $overstays->count(),
                'threshold_hours' => $hours,
            ],
        ]);
    }

    private function enforceMaxScans(VisitorInvitation $invitation): void
    {
        $setting = VisitorAccessSetting::query()
            ->where('community_id', $invitation->community_id)
            ->first();

        $maxScans = $setting?->max_uses_per_invitation ?? 0;

        if ($maxScans <= 0) {
            return;
        }

        $scanCount = $invitation->logs()->whereNotNull('entry_at')->count();

        if ($scanCount >= $maxScans) {
            throw ValidationException::withMessages([
                'qr_code_token' => sprintf(
                    'Maximum of %d scan(s) per invitation reached. This QR code is no longer valid.',
                    $maxScans
                ),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function meta($paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'from' => $paginator->firstItem(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'to' => $paginator->lastItem(),
            'total' => $paginator->total(),
        ];
    }
}
