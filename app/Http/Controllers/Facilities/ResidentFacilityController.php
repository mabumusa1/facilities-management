<?php

namespace App\Http\Controllers\Facilities;

use App\Exceptions\SlotUnavailableException;
use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\FacilityAvailabilityRule;
use App\Models\FacilityBooking;
use App\Models\Status;
use App\Models\User;
use App\Support\FacilityBookingStatus;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ResidentFacilityController extends Controller
{
    /**
     * Resident-facing facility discovery page.
     */
    public function index(): Response
    {
        $this->authorize('viewAnyAsResident', Facility::class);

        $facilities = Facility::query()
            ->active()
            ->with(['category'])
            ->latest()
            ->paginate(15);

        return Inertia::render('facilities/ResidentIndex', [
            'facilities' => $facilities,
        ]);
    }

    /**
     * Show the slot picker page for a facility.
     */
    public function slotPicker(Facility $facility): Response
    {
        $this->authorize('viewAsResident', $facility);

        $facility->load(['category']);

        return Inertia::render('facilities/SlotPicker', [
            'facility' => $facility,
            'bookingHorizonDays' => $facility->booking_horizon_days ?? 14,
        ]);
    }

    /**
     * Return available time slots for a facility on a given date.
     * Used as an AJAX endpoint by the Slot Picker UI.
     */
    public function slots(Facility $facility, Request $request): JsonResponse
    {
        $this->authorize('viewAsResident', $facility);

        $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $date = Carbon::parse($request->query('date'));
        $dayOfWeek = (int) $date->dayOfWeek; // 0=Sunday … 6=Saturday

        /** @var FacilityAvailabilityRule|null $rule */
        $rule = FacilityAvailabilityRule::query()
            ->where('facility_id', $facility->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (! $rule) {
            return response()->json([
                'slots' => [],
                'closed' => true,
                'message' => __('facilities.resident.closedOnDay'),
            ]);
        }

        $activeStatusIds = FacilityBookingStatus::activeIds();

        // Existing bookings for this facility on this date
        $existingBookings = FacilityBooking::query()
            ->where('facility_id', $facility->id)
            ->where('booking_date', $date->toDateString())
            ->whereIn('status_id', $activeStatusIds)
            ->select('start_time', 'end_time')
            ->get();

        // Build slot grid
        $slots = [];
        $slotDuration = $rule->slot_duration_minutes;
        $openTime = $rule->open_time; // Carbon instance (H:i cast)
        $closeTime = $rule->close_time; // Carbon instance (H:i cast)

        $currentSlot = Carbon::createFromTimeString($openTime->format('H:i'));
        $closingTime = Carbon::createFromTimeString($closeTime->format('H:i'));

        while ($currentSlot->lt($closingTime)) {
            $slotEnd = $currentSlot->copy()->addMinutes($slotDuration);

            if ($slotEnd->gt($closingTime)) {
                break;
            }

            $startStr = $currentSlot->format('H:i');
            $endStr = $slotEnd->format('H:i');

            // Count bookings overlapping this slot
            $overlappingCount = $existingBookings->filter(function ($booking) use ($startStr, $endStr): bool {
                return $booking->start_time < $endStr && $booking->end_time > $startStr;
            })->count();

            $remainingCapacity = $rule->max_concurrent_bookings - $overlappingCount;

            $status = match (true) {
                $remainingCapacity <= 0 => 'full',
                default => 'available',
            };

            $slots[] = [
                'start' => $startStr,
                'end' => $endStr,
                'status' => $status,
                'remaining_capacity' => max(0, $remainingCapacity),
            ];

            $currentSlot = $slotEnd;
        }

        return response()->json([
            'slots' => $slots,
            'closed' => false,
            'date' => $date->toDateString(),
            'facility_id' => $facility->id,
        ]);
    }

    /**
     * Create a booking for the authenticated resident.
     */
    public function book(Facility $facility, Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('bookOwn', $facility);

        $validated = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'string', 'regex:/^\d{2}:\d{2}$/'],
            'end_time' => ['required', 'string', 'regex:/^\d{2}:\d{2}$/', 'after:start_time'],
        ]);

        $date = Carbon::parse($validated['date']);
        $startTime = $validated['start_time'];
        $endTime = $validated['end_time'];
        $dayOfWeek = (int) $date->dayOfWeek;

        /** @var FacilityAvailabilityRule|null $rule */
        $rule = FacilityAvailabilityRule::query()
            ->where('facility_id', $facility->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (! $rule) {
            return response()->json([
                'error' => 'facility_closed',
                'message' => __('facilities.resident.closedOnDay'),
            ], 422);
        }

        $activeStatusIds = FacilityBookingStatus::activeIds();

        try {
            $booking = DB::transaction(function () use (
                $facility,
                $date,
                $startTime,
                $endTime,
                $rule,
                $activeStatusIds,
            ): FacilityBooking {
                // Lock the parent availability rule row FIRST so concurrent
                // bookings for the same facility serialize here — even when
                // zero booking rows exist (first-booking-for-slot case).
                FacilityAvailabilityRule::where('id', $rule->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Now count existing overlapping bookings safely under the lock.
                $existingCount = FacilityBooking::query()
                    ->where('facility_id', $facility->id)
                    ->where('booking_date', $date->toDateString())
                    ->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime)
                    ->whereIn('status_id', $activeStatusIds)
                    ->count();

                if ($existingCount >= $rule->max_concurrent_bookings) {
                    throw new SlotUnavailableException;
                }

                $bookerId = Auth::id();

                // Choose status by integer constant — never by name_en string lookup.
                $statusId = $facility->contract_required
                    ? FacilityBookingStatus::PENDING_APPROVAL
                    : FacilityBookingStatus::BOOKED;

                // Verify the status row actually exists in this environment.
                $confirmedStatus = Status::find($statusId)
                    ?? Status::query()->where('type', 'facility_booking')->firstOrFail();

                return FacilityBooking::create([
                    'facility_id' => $facility->id,
                    'booker_type' => User::class,
                    'booker_id' => $bookerId,
                    'status_id' => $confirmedStatus->id,
                    'booking_date' => $date->toDateString(),
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                ]);
            });
        } catch (SlotUnavailableException) {
            return response()->json([
                'error' => 'slot_unavailable',
                'message' => __('facilities.resident.slotUnavailable'),
            ], 409);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'booking' => $booking->only('id', 'booking_date', 'start_time', 'end_time', 'status_id'),
                'contract_required' => $facility->contract_required,
                'message' => __('facilities.resident.bookingConfirmed'),
            ], 201);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('facilities.resident.bookingConfirmed')]);

        return to_route('facilities.resident.index');
    }
}
