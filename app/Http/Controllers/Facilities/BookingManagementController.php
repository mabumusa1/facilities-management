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
    /**
     * Admin calendar — bookings in a date range for double-booking prevention.
     */
    public function calendar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
            'facility_id' => ['nullable', 'integer', 'exists:rf_facilities,id'],
            'community_id' => ['nullable', 'integer', 'exists:rf_communities,id'],
        ]);

        $from = $validated['from'];
        $to = $validated['to'];

        $bookings = FacilityBooking::query()
            ->with(['facility:id,name,community_id', 'facility.community:id,name', 'user:id,name'])
            ->whereBetween('date', [$from, $to])
            ->when($validated['facility_id'] ?? null, fn ($q) => $q->where('facility_id', (int) $validated['facility_id']))
            ->when($validated['community_id'] ?? null, fn ($q) => $q->whereHas('facility', fn ($sq) => $sq->where('community_id', (int) $validated['community_id'])))
            ->whereNotIn('status', ['cancelled'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'data' => $bookings->map(fn ($b): array => [
                'id' => $b->id,
                'facility' => $b->facility?->name,
                'community' => $b->facility?->community?->name,
                'user' => $b->user?->name,
                'date' => $b->date,
                'start_time' => $b->start_time,
                'end_time' => $b->end_time,
                'status' => $b->status,
                'purpose' => $b->purpose,
            ]),
        ]);
    }

    /**
     * Waitlist — join queue for a full slot.
     */
    public function waitlistJoin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'facility_id' => ['required', 'integer', 'exists:rf_facilities,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $exists = FacilityWaitlist::query()
            ->where('facility_id', $validated['facility_id'])
            ->where('date', $validated['date'])
            ->where('start_time', $validated['start_time'])
            ->where('user_id', $request->user()?->id)
            ->whereNull('notified_at')
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'facility_id' => 'You are already on the waitlist for this slot.',
            ]);
        }

        FacilityWaitlist::create([
            'account_tenant_id' => Tenant::current()?->id,
            'facility_id' => $validated['facility_id'],
            'user_id' => $request->user()?->id,
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);

        return response()->json([
            'message' => 'Joined waitlist. You will be notified if a slot opens.',
        ]);
    }

    /**
     * Waitlist — leave the queue.
     */
    public function waitlistLeave(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'waitlist_id' => ['required', 'integer', 'exists:rf_facility_waitlist,id'],
        ]);

        FacilityWaitlist::whereKey($validated['waitlist_id'])
            ->where('user_id', $request->user()?->id)
            ->delete();

        return response()->json(['message' => 'Removed from waitlist.']);
    }

    /**
     * Booking check-in — mark booking as arrived.
     */
    public function checkIn(Request $request, FacilityBooking $booking): JsonResponse
    {
        if ($booking->status === 'cancelled') {
            throw ValidationException::withMessages([
                'booking' => 'Cannot check in a cancelled booking.',
            ]);
        }

        if ($booking->checked_in_at !== null) {
            throw ValidationException::withMessages([
                'booking' => 'Already checked in.',
            ]);
        }

        $booking->update([
            'status' => 'active',
            'checked_in_at' => now(),
            'checked_in_by' => $request->user()?->id,
        ]);

        return response()->json([
            'data' => [
                'id' => $booking->id,
                'checked_in_at' => $booking->checked_in_at->toJSON(),
            ],
            'message' => 'Booking checked in.',
        ]);
    }

    /**
     * Booking check-out — mark booking as departed.
     */
    public function checkOut(Request $request, FacilityBooking $booking): JsonResponse
    {
        if ($booking->checked_in_at === null) {
            throw ValidationException::withMessages([
                'booking' => 'Cannot check out — not yet checked in.',
            ]);
        }

        if ($booking->checked_out_at !== null) {
            throw ValidationException::withMessages([
                'booking' => 'Already checked out.',
            ]);
        }

        $booking->update([
            'status' => 'completed',
            'checked_out_at' => now(),
        ]);

        // Notify next person on waitlist
        $next = FacilityWaitlist::query()
            ->where('facility_id', $booking->facility_id)
            ->where('date', $booking->date)
            ->where('start_time', $booking->start_time)
            ->whereNull('notified_at')
            ->oldest()
            ->first();

        if ($next !== null) {
            $next->update(['notified_at' => now()]);
        }

        return response()->json([
            'data' => [
                'id' => $booking->id,
                'checked_in_at' => $booking->checked_in_at?->toJSON(),
                'checked_out_at' => $booking->checked_out_at->toJSON(),
                'waitlist_spot_opened' => $next !== null,
            ],
            'message' => 'Booking checked out.',
        ]);
    }

    /**
     * Operational reporting — utilisation rate, peak hours, no-show rate.
     */
    public function operationalReport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
            'facility_id' => ['nullable', 'integer', 'exists:rf_facilities,id'],
            'community_id' => ['nullable', 'integer', 'exists:rf_communities,id'],
        ]);

        $from = $validated['from'];
        $to = $validated['to'];

        $bookings = FacilityBooking::query()
            ->with('facility:id,name,community_id')
            ->whereBetween('date', [$from, $to])
            ->when($validated['facility_id'] ?? null, fn ($q) => $q->where('facility_id', (int) $validated['facility_id']))
            ->when($validated['community_id'] ?? null, fn ($q) => $q->whereHas('facility', fn ($sq) => $sq->where('community_id', (int) $validated['community_id'])))
            ->get();

        $total = $bookings->count();
        $checkedIn = $bookings->whereNotNull('checked_in_at')->count();
        $cancelled = $bookings->where('status', 'cancelled')->count();
        $noShow = $bookings->where('status', 'confirmed')->whereNull('checked_in_at')
            ->filter(fn ($b) => $b->date.' '.($b->start_time ?? '00:00') < now()->toDateTimeString())->count();

        // Peak hours
        $hourDistribution = $bookings
            ->filter(fn ($b) => $b->start_time !== null)
            ->groupBy(fn ($b) => substr($b->start_time, 0, 2))
            ->map(fn ($group) => $group->count())
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
                'peak_hours' => $hourDistribution->map(fn ($count, $hour) => [
                    'hour' => sprintf('%s:00', $hour),
                    'bookings' => $count,
                ])->values(),
            ],
        ]);
    }
}
