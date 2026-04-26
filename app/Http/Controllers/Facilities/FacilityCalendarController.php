<?php

namespace App\Http\Controllers\Facilities;

use App\Http\Controllers\Controller;
use App\Http\Requests\FacilityCalendarBookingRequest;
use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\Resident;
use App\Models\User;
use App\Services\Facilities\BookingConflictService;
use App\Support\FacilityBookingStatus;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class FacilityCalendarController extends Controller
{
    public function __construct(private readonly BookingConflictService $conflictService) {}

    /**
     * Render the admin calendar page.
     *
     * Provides the facility list (for the dropdown) and the current week start
     * date. All booking data is fetched client-side via the `bookings()` AJAX
     * endpoint so navigation does not trigger a full page reload.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', FacilityBooking::class);

        $weekStart = Carbon::now()->startOfWeek(Carbon::SUNDAY)->toDateString();

        /** @var User $user */
        $user = Auth::user();

        $facilities = Facility::query()
            ->forManager($user)
            ->active()
            ->select('id', 'name', 'name_en', 'name_ar')
            ->orderBy('name')
            ->get();

        return Inertia::render('facilities/Calendar', [
            'facilities' => $facilities,
            'currentWeekStart' => $weekStart,
        ]);
    }

    /**
     * Return bookings for a given week, optionally filtered by facility.
     *
     * Query params:
     *   week_start  – Y-m-d (required)
     *   facility_id – integer (optional)
     */
    public function bookings(Request $request): JsonResponse
    {
        $this->authorize('viewAny', FacilityBooking::class);

        $request->validate([
            'week_start' => ['required', 'date'],
            'facility_id' => ['nullable', 'integer', 'exists:rf_facilities,id'],
        ]);

        $weekStart = Carbon::parse($request->query('week_start'))->startOfDay();
        $weekEnd = $weekStart->copy()->addDays(6)->endOfDay();
        $facilityId = $request->query('facility_id') ? (int) $request->query('facility_id') : null;

        /** @var User $user */
        $user = Auth::user();

        $bookings = FacilityBooking::query()
            ->with(['facility', 'booker', 'status'])
            ->forManager($user)
            ->whereBetween('booking_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->when($facilityId, fn ($q) => $q->where('facility_id', $facilityId))
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->get()
            ->map(fn (FacilityBooking $booking) => [
                'id' => $booking->id,
                'facility_id' => $booking->facility_id,
                'facility_name' => $booking->facility?->name ?? '',
                'booker_name' => $this->resolveBookerName($booking),
                'booker_type' => $booking->booker_type,
                'booking_date' => $booking->booking_date?->toDateString(),
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'status_id' => $booking->status_id,
                'status_name' => $booking->status?->name ?? '',
                'notes' => $booking->notes,
            ]);

        return response()->json([
            'bookings' => $bookings,
            'facility_id' => $facilityId,
            'week_start' => $weekStart->toDateString(),
            'week_end' => $weekEnd->toDateString(),
        ]);
    }

    /**
     * Return booking detail JSON for the calendar popover.
     */
    public function show(FacilityBooking $facilityBooking): JsonResponse
    {
        $this->authorize('view', $facilityBooking);

        $facilityBooking->load(['facility', 'booker', 'status']);

        /** @var User $user */
        $user = Auth::user();

        return response()->json([
            'id' => $facilityBooking->id,
            'facility_id' => $facilityBooking->facility_id,
            'facility_name' => $facilityBooking->facility?->name ?? '',
            'booker_name' => $this->resolveBookerName($facilityBooking),
            'booker_type' => $facilityBooking->booker_type,
            'booking_date' => $facilityBooking->booking_date?->toDateString(),
            'start_time' => $facilityBooking->start_time,
            'end_time' => $facilityBooking->end_time,
            'status_id' => $facilityBooking->status_id,
            'status_name' => $facilityBooking->status?->name ?? '',
            'notes' => $facilityBooking->notes,
            'invoice_id' => $facilityBooking->invoice_id,
            'can_checkin' => $user?->can('checkin', $facilityBooking),
            'can_cancel' => $user?->can('cancel', $facilityBooking),
            'can_update' => $user?->can('update', $facilityBooking),
        ]);
    }

    /**
     * Admin creates a booking from the calendar modal.
     */
    public function store(FacilityCalendarBookingRequest $request): JsonResponse
    {
        $this->authorize('create', FacilityBooking::class);

        $validated = $request->validated();

        /** @var Facility $facility */
        $facility = Facility::findOrFail($validated['facility_id']);

        $booking = DB::transaction(function () use ($validated, $facility): FacilityBooking {
            // Lock the facility row to prevent concurrent double-booking races.
            Facility::where('id', $facility->id)->lockForUpdate()->firstOrFail();

            $conflict = $this->conflictService->check(
                $facility,
                $validated['booking_date'],
                $validated['start_time'],
                $validated['end_time'],
            );

            if ($conflict) {
                abort(422, 'overlap_detected:'.$conflict->id);
            }

            $statusId = $facility->contract_required
                ? FacilityBookingStatus::PENDING_APPROVAL
                : FacilityBookingStatus::BOOKED;

            $bookerType = $validated['resident_id']
                ? Resident::class
                : User::class;

            $bookerId = $validated['resident_id'] ?? Auth::id();

            return FacilityBooking::create([
                'facility_id' => $facility->id,
                'account_tenant_id' => $facility->account_tenant_id,
                'status_id' => $statusId,
                'booker_type' => $bookerType,
                'booker_id' => $bookerId,
                'booked_by_type' => User::class,
                'booking_date' => $validated['booking_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        $booking->load(['facility', 'booker', 'status']);

        return response()->json([
            'booking' => [
                'id' => $booking->id,
                'facility_id' => $booking->facility_id,
                'facility_name' => $booking->facility?->name ?? '',
                'booker_name' => $this->resolveBookerName($booking),
                'booker_type' => $booking->booker_type,
                'booking_date' => $booking->booking_date?->toDateString(),
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'status_id' => $booking->status_id,
                'status_name' => $booking->status?->name ?? '',
                'notes' => $booking->notes,
            ],
            'message' => __('facilities.calendar.bookingCreated'),
        ], 201);
    }

    /**
     * Resolve the display name for a booking's booker.
     */
    private function resolveBookerName(FacilityBooking $booking): string
    {
        $booker = $booking->booker;

        if (! $booker) {
            return '';
        }

        if (isset($booker->first_name)) {
            return trim(($booker->first_name ?? '').' '.($booker->last_name ?? ''));
        }

        return $booker->name ?? '';
    }
}
