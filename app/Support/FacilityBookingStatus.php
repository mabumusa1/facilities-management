<?php

namespace App\Support;

/**
 * Integer constants for facility-booking statuses stored in `rf_statuses`.
 *
 * IDs are authoritative and must match `StatusSeeder` (IDs 19–22).
 * Use `Status::find(FacilityBookingStatus::BOOKED)` instead of name-string
 * lookups to avoid fragile `name_en` comparisons.
 */
final class FacilityBookingStatus
{
    /** Booking awaiting admin approval (contract_required = true). */
    public const PENDING_APPROVAL = 19;

    /** Booking confirmed / slot reserved. */
    public const BOOKED = 20;

    /** Booking request was rejected by admin. */
    public const BOOKING_REJECTED = 21;

    /** Booking was cancelled. */
    public const CANCELLED = 22;

    /**
     * IDs that count against capacity (i.e. not cancelled / rejected).
     *
     * @return list<int>
     */
    public static function activeIds(): array
    {
        return [self::PENDING_APPROVAL, self::BOOKED];
    }
}
