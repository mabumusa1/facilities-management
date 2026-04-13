<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Represents a scheduled property visit in the marketplace.
 */
#[Fillable([
    'tenant_id',
    'marketplace_unit_id',
    'marketplace_customer_id',
    'status_id',
    'visit_date',
    'visit_time',
    'visit_end_time',
    'duration_minutes',
    'is_all_day',
    'assigned_agent',
    'customer_notes',
    'agent_notes',
    'feedback',
    'interest_level',
    'outcome',
    'confirmed_at',
    'confirmed_by',
    'completed_at',
    'canceled_at',
    'cancellation_reason',
    'rescheduled_from',
    'rescheduled_at',
])]
class MarketplaceVisit extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'is_all_day' => 'boolean',
            'confirmed_at' => 'datetime',
            'completed_at' => 'datetime',
            'canceled_at' => 'datetime',
            'rescheduled_at' => 'datetime',
        ];
    }

    /**
     * Get the tenant that owns this visit.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the marketplace unit being visited.
     */
    public function marketplaceUnit(): BelongsTo
    {
        return $this->belongsTo(MarketplaceUnit::class);
    }

    /**
     * Get the customer making the visit.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(MarketplaceCustomer::class, 'marketplace_customer_id');
    }

    /**
     * Get the status of this visit.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Get the assigned agent.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'assigned_agent');
    }

    /**
     * Get the contact who confirmed the visit.
     */
    public function confirmedByContact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'confirmed_by');
    }

    /**
     * Get the visit this was rescheduled from.
     */
    public function rescheduledFromVisit(): BelongsTo
    {
        return $this->belongsTo(MarketplaceVisit::class, 'rescheduled_from');
    }

    /**
     * Confirm the visit.
     */
    public function confirm(?int $confirmedBy = null): void
    {
        $status = Status::where('domain', 'marketplace_visit')
            ->where('slug', 'marketplace_visit_confirmed')
            ->first();

        $this->update([
            'status_id' => $status?->id ?? $this->status_id,
            'confirmed_at' => now(),
            'confirmed_by' => $confirmedBy,
        ]);
    }

    /**
     * Complete the visit.
     */
    public function complete(?string $outcome = null, ?int $interestLevel = null): void
    {
        $status = Status::where('domain', 'marketplace_visit')
            ->where('slug', 'marketplace_visit_completed')
            ->first();

        $this->update([
            'status_id' => $status?->id ?? $this->status_id,
            'completed_at' => now(),
            'outcome' => $outcome,
            'interest_level' => $interestLevel,
        ]);
    }

    /**
     * Cancel the visit.
     */
    public function cancel(?string $reason = null): void
    {
        $status = Status::where('domain', 'marketplace_visit')
            ->where('slug', 'marketplace_visit_canceled')
            ->first();

        $this->update([
            'status_id' => $status?->id ?? $this->status_id,
            'canceled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }

    /**
     * Mark as no show.
     */
    public function markAsNoShow(): void
    {
        $status = Status::where('domain', 'marketplace_visit')
            ->where('slug', 'marketplace_visit_no_show')
            ->first();

        $this->update([
            'status_id' => $status?->id ?? $this->status_id,
        ]);
    }

    /**
     * Reschedule the visit.
     */
    public function reschedule(string $newDate, ?string $newTime = null): self
    {
        // Cancel the current visit
        $this->cancel('Rescheduled');

        // Create a new visit with the same details
        return static::create([
            'tenant_id' => $this->tenant_id,
            'marketplace_unit_id' => $this->marketplace_unit_id,
            'marketplace_customer_id' => $this->marketplace_customer_id,
            'status_id' => $this->status_id,
            'visit_date' => $newDate,
            'visit_time' => $newTime ?? $this->visit_time,
            'visit_end_time' => $this->visit_end_time,
            'duration_minutes' => $this->duration_minutes,
            'is_all_day' => $this->is_all_day,
            'assigned_agent' => $this->assigned_agent,
            'customer_notes' => $this->customer_notes,
            'rescheduled_from' => $this->id,
            'rescheduled_at' => now(),
        ]);
    }

    /**
     * Assign an agent to this visit.
     */
    public function assignAgent(int $agentId): void
    {
        $this->update(['assigned_agent' => $agentId]);
    }

    /**
     * Add feedback to the visit.
     */
    public function addFeedback(string $feedback, ?int $interestLevel = null): void
    {
        $this->update([
            'feedback' => $feedback,
            'interest_level' => $interestLevel,
        ]);
    }

    /**
     * Check if the visit is pending.
     */
    public function isPending(): bool
    {
        return $this->status?->slug === 'marketplace_visit_pending';
    }

    /**
     * Check if the visit is confirmed.
     */
    public function isConfirmed(): bool
    {
        return $this->status?->slug === 'marketplace_visit_confirmed' || $this->confirmed_at !== null;
    }

    /**
     * Check if the visit is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status?->slug === 'marketplace_visit_completed' || $this->completed_at !== null;
    }

    /**
     * Check if the visit is canceled.
     */
    public function isCanceled(): bool
    {
        return $this->status?->slug === 'marketplace_visit_canceled' || $this->canceled_at !== null;
    }

    /**
     * Check if the visit is a no show.
     */
    public function isNoShow(): bool
    {
        return $this->status?->slug === 'marketplace_visit_no_show';
    }

    /**
     * Check if the visit is rescheduled.
     */
    public function isRescheduled(): bool
    {
        return $this->rescheduled_from !== null;
    }

    /**
     * Check if the visit is upcoming (scheduled for the future).
     */
    public function isUpcoming(): bool
    {
        return $this->visit_date->isFuture() && ! $this->isCanceled() && ! $this->isCompleted();
    }

    /**
     * Check if the visit is today.
     */
    public function isToday(): bool
    {
        return $this->visit_date->isToday();
    }
}
