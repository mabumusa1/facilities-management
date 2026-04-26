<?php

namespace App\Services\Facilities;

use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Support\FacilityBookingStatus;
use Illuminate\Database\Eloquent\Builder;

/**
 * Checks for overlapping bookings for a given facility and time range.
 *
 * Overlap condition: start_a < end_b AND end_a > start_b
 * Used by both the resident booking flow and the admin calendar store.
 */
class BookingConflictService
{
    /**
     * Return the first conflicting booking, or null if none found.
     *
     * @param  string  $bookingDate  Y-m-d
     * @param  string  $startTime  H:i
     * @param  string  $endTime  H:i
     */
    public function check(
        Facility $facility,
        string $bookingDate,
        string $startTime,
        string $endTime,
        ?int $excludeBookingId = null,
    ): ?FacilityBooking {
        return FacilityBooking::query()
            ->where('facility_id', $facility->id)
            ->where('booking_date', $bookingDate)
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->whereIn('status_id', FacilityBookingStatus::activeIds())
            ->when($excludeBookingId, fn (Builder $q) => $q->where('id', '!=', $excludeBookingId))
            ->with(['facility'])
            ->first();
    }
}
