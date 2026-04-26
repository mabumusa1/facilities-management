<?php

namespace App\Http\Controllers\Facilities;

use App\Http\Controllers\Controller;
use App\Models\FacilityBooking;
use App\Models\FacilityWaitlist;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BookingManagementController extends Controller
{
    public function calendar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
            'facility_id' => ['nullable', 'integer', 'exists:rf_facilities,id'],
            'community_id' => ['nullable', 'integer', 'exists:rf_communities,id'],
        ]);

        $bookings = FacilityBooking::query()
            ->with(['facility:id,name,community_id', 'facility.community:id,name'])
            ->whereBetween('booking_date', [$validated['from'], $validated['to']])
            ->when($validated['facility_id'] ?? null, fn ($q) => $q->where('facility_id', (int) $validated['facility_id']))
            ->when($validated['community_id'] ?? null, fn ($q) => $q->whereHas('facility', fn ($sq) => $sq->where('community_id', (int) $validated['community_id'])))
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'data' => $bookings->map(fn ($b): array => [
                'id' => $b->id,
                'facility' => $b->facility?->name,
                'community' => $b->facility?->community?->name,
                'date' => $b->booking_date,
                'start_time' => $b->start_time,
                'end_time' => $b->end_time,
                'status_id' => $b->status_id,
                'purpose' => $b->purpose,
            ]),
        ]);
    }

    public function checkIn(Request $request, FacilityBooking $booking): JsonResponse
    {
        if ($booking->checked_in_at !== null) {
            throw ValidationException::withMessages(['booking' => 'Already checked in.']);
        }

        $booking->update([
            'checked_in_at' => now(),
            'checked_in_by' => $request->user()?->id,
        ]);

        return response()->json([
            'message' => 'Booking checked in.',
        ]);
    }

    public function checkOut(Request $request, FacilityBooking $booking): JsonResponse
    {
        if ($booking->checked_in_at === null) {
            throw ValidationException::withMessages(['booking' => 'Cannot check out — not yet checked in.']);
        }

        if ($booking->checked_out_at !== null) {
            throw ValidationException::withMessages(['booking' => 'Already checked out.']);
        }

        $booking->update(['checked_out_at' => now()]);

        // Notify next on waitlist
        $next = FacilityWaitlist::query()
            ->where('facility_id', $booking->facility_id)
            ->whereNull('notified_at')
            ->oldest()
            ->first();

        if ($next !== null) {
            $next->update(['notified_at' => now()]);
        }

        return response()->json([
            'message' => 'Booking checked out.',
        ]);
    }

    public function waitlistJoin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'facility_id' => ['required', 'integer', 'exists:rf_facilities,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $startAt = "{$validated['date']} {$validated['start_time']}:00";
        $endAt = "{$validated['date']} {$validated['end_time']}:00";

        FacilityWaitlist::create([
            'account_tenant_id' => Tenant::current()?->id,
            'facility_id' => $validated['facility_id'],
            'resident_id' => $request->user()?->id,
            'requested_start_at' => $startAt,
            'requested_end_at' => $endAt,
        ]);

        return response()->json(['message' => 'Joined waitlist.']);
    }

    public function waitlistLeave(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'waitlist_id' => ['required', 'integer', 'exists:rf_facility_waitlist,id'],
        ]);

        FacilityWaitlist::whereKey($validated['waitlist_id'])
            ->where('resident_id', $request->user()?->id)
            ->delete();

        return response()->json(['message' => 'Removed from waitlist.']);
    }

    public function operationalReport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
            'facility_id' => ['nullable', 'integer'],
            'community_id' => ['nullable', 'integer'],
        ]);

        $bookings = FacilityBooking::query()
            ->with('facility:id,name,community_id')
            ->whereBetween('booking_date', [$validated['from'], $validated['to']])
            ->when($validated['facility_id'] ?? null, fn ($q) => $q->where('facility_id', (int) $validated['facility_id']))
            ->when($validated['community_id'] ?? null, fn ($q) => $q->whereHas('facility', fn ($sq) => $sq->where('community_id', (int) $validated['community_id'])))
            ->get();

        $total = $bookings->count();
        $checkedIn = $bookings->whereNotNull('checked_in_at')->count();
        $cancelled = $bookings->where('status_id', 0)->count();
        $noShow = $bookings->filter(fn ($b) => $b->booking_date < now()->toDateString() && $b->checked_in_at === null && $b->status_id !== 0)->count();

        $hourDistribution = $bookings
            ->filter(fn ($b) => $b->start_time !== null)
            ->groupBy(fn ($b) => substr($b->start_time, 0, 2))
            ->map(fn ($g) => $g->count())
            ->sortDesc()
            ->take(5);

        return response()->json([
            'data' => [
                'total_bookings' => $total,
                'checked_in' => $checkedIn,
                'cancelled' => $cancelled,
                'no_show' => $noShow,
                'utilisation_rate_pct' => $total > 0 ? round(($checkedIn / $total) * 100, 1) : 0,
                'no_show_rate_pct' => $total > 0 ? round(($noShow / $total) * 100, 1) : 0,
                'peak_hours' => $hourDistribution->map(fn ($c, $h) => ['hour' => sprintf('%s:00', $h), 'bookings' => $c])->values(),
            ],
        ]);
    }
}
