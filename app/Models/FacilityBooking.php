<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Represents a facility booking with state machine workflow.
 *
 * Workflow States (from BUSINESS-WORKFLOWS.md):
 * - Pending Approval (19) → Booked (20) or Rejected (21/38)
 * - Booked (20) → Scheduled (35)
 * - Scheduled (35) → Completed (36)
 * - Any state → Canceled (22/37)
 */
#[Fillable([
    'tenant_id',
    'facility_id',
    'contact_id',
    'unit_id',
    'status_id',
    'booking_date',
    'start_time',
    'end_time',
    'duration_minutes',
    'total_price',
    'notes',
    'special_requests',
    'approved_by',
    'approved_at',
    'canceled_at',
    'cancellation_reason',
    'checked_in_at',
    'checked_in_by',
    'checked_out_at',
    'checked_out_by',
])]
class FacilityBooking extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'approved_at' => 'datetime',
            'canceled_at' => 'datetime',
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
            'total_price' => 'decimal:2',
        ];
    }

    /**
     * Get the tenant that owns this booking.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the facility being booked.
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    /**
     * Get the contact who made the booking.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the unit associated with this booking.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the status of this booking.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Get the contact who approved the booking.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'approved_by');
    }

    /**
     * Get the contact who checked in.
     */
    public function checkedInByContact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'checked_in_by');
    }

    /**
     * Get the contact who checked out.
     */
    public function checkedOutByContact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'checked_out_by');
    }

    /**
     * Approve the booking (Pending Approval → Booked).
     */
    public function approve(?int $approvedBy = null): void
    {
        $status = Status::where('domain', 'facility_booking')
            ->where('slug', 'facility_booking_booked')
            ->first();

        $this->update([
            'status_id' => $status?->id ?? $this->status_id,
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }

    /**
     * Reject the booking (Pending Approval → Rejected).
     */
    public function reject(?string $reason = null): void
    {
        $status = Status::where('domain', 'facility_booking')
            ->where('slug', 'facility_booking_rejected')
            ->first();

        $this->update([
            'status_id' => $status?->id ?? $this->status_id,
            'cancellation_reason' => $reason,
        ]);
    }

    /**
     * Schedule the booking (Booked → Scheduled).
     */
    public function schedule(): void
    {
        $status = Status::where('domain', 'facility_booking')
            ->where('slug', 'facility_booking_scheduled')
            ->first();

        $this->update([
            'status_id' => $status?->id ?? $this->status_id,
        ]);
    }

    /**
     * Complete the booking (Scheduled → Completed).
     */
    public function complete(): void
    {
        $status = Status::where('domain', 'facility_booking')
            ->where('slug', 'facility_booking_completed')
            ->first();

        $this->update([
            'status_id' => $status?->id ?? $this->status_id,
        ]);
    }

    /**
     * Cancel the booking (Any state → Canceled).
     */
    public function cancel(?string $reason = null): void
    {
        $status = Status::where('domain', 'facility_booking')
            ->where('slug', 'facility_booking_canceled')
            ->first();

        $this->update([
            'status_id' => $status?->id ?? $this->status_id,
            'canceled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }

    /**
     * Check in for the booking.
     */
    public function checkIn(?int $checkedInBy = null): void
    {
        $this->update([
            'checked_in_at' => now(),
            'checked_in_by' => $checkedInBy,
        ]);
    }

    /**
     * Check out from the booking.
     */
    public function checkOut(?int $checkedOutBy = null): void
    {
        $this->update([
            'checked_out_at' => now(),
            'checked_out_by' => $checkedOutBy,
        ]);

        // Auto-complete if scheduled
        if ($this->isScheduled()) {
            $this->complete();
        }
    }

    /**
     * Check if booking is pending approval.
     */
    public function isPending(): bool
    {
        return $this->status?->slug === 'facility_booking_pending';
    }

    /**
     * Check if booking is booked/approved.
     */
    public function isBooked(): bool
    {
        return $this->status?->slug === 'facility_booking_booked';
    }

    /**
     * Check if booking is scheduled.
     */
    public function isScheduled(): bool
    {
        return $this->status?->slug === 'facility_booking_scheduled';
    }

    /**
     * Check if booking is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status?->slug === 'facility_booking_completed';
    }

    /**
     * Check if booking is rejected.
     */
    public function isRejected(): bool
    {
        return in_array($this->status?->slug, ['facility_booking_rejected']);
    }

    /**
     * Check if booking is canceled.
     */
    public function isCanceled(): bool
    {
        return in_array($this->status?->slug, ['facility_booking_canceled']) || $this->canceled_at !== null;
    }

    /**
     * Check if user has checked in.
     */
    public function isCheckedIn(): bool
    {
        return $this->checked_in_at !== null && $this->checked_out_at === null;
    }

    /**
     * Check if user has checked out.
     */
    public function isCheckedOut(): bool
    {
        return $this->checked_in_at !== null && $this->checked_out_at !== null;
    }
}
