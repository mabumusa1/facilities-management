<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Represents a sales/booking offer or negotiation on a marketplace unit.
 */
#[Fillable([
    'tenant_id',
    'marketplace_unit_id',
    'marketplace_customer_id',
    'status_id',
    'offer_reference',
    'offer_type',
    'offer_amount',
    'counter_offer_amount',
    'final_amount',
    'currency',
    'payment_method',
    'installment_months',
    'down_payment_percentage',
    'down_payment_amount',
    'booking_deposit',
    'deposit_paid_at',
    'deposit_refunded_at',
    'deposit_payment_reference',
    'conditions',
    'customer_message',
    'agent_response',
    'rejection_reason',
    'valid_until',
    'submitted_at',
    'reviewed_at',
    'approved_at',
    'rejected_at',
    'accepted_at',
    'cancelled_at',
    'expired_at',
    'completed_at',
    'contract_reference',
    'contract_signed_at',
    'contract_signed_by',
    'assigned_agent',
    'reviewed_by',
    'approved_by',
    'negotiation_rounds',
    'is_counter_offer',
    'parent_offer_id',
    'marketplace_visit_id',
])]
class MarketplaceOffer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'offer_amount' => 'decimal:2',
            'counter_offer_amount' => 'decimal:2',
            'final_amount' => 'decimal:2',
            'down_payment_percentage' => 'decimal:2',
            'down_payment_amount' => 'decimal:2',
            'booking_deposit' => 'decimal:2',
            'is_counter_offer' => 'boolean',
            'valid_until' => 'datetime',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'accepted_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'expired_at' => 'datetime',
            'completed_at' => 'datetime',
            'deposit_paid_at' => 'datetime',
            'deposit_refunded_at' => 'datetime',
            'contract_signed_at' => 'datetime',
        ];
    }

    /**
     * Get the tenant that owns this offer.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the marketplace unit this offer is for.
     */
    public function marketplaceUnit(): BelongsTo
    {
        return $this->belongsTo(MarketplaceUnit::class);
    }

    /**
     * Get the customer making the offer.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(MarketplaceCustomer::class, 'marketplace_customer_id');
    }

    /**
     * Get the status of this offer.
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
     * Get the person who reviewed the offer.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'reviewed_by');
    }

    /**
     * Get the person who approved the offer.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'approved_by');
    }

    /**
     * Get the person who signed the contract.
     */
    public function contractSigner(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contract_signed_by');
    }

    /**
     * Get the parent offer (if this is a counter offer).
     */
    public function parentOffer(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOffer::class, 'parent_offer_id');
    }

    /**
     * Get child counter offers.
     */
    public function counterOffers(): HasMany
    {
        return $this->hasMany(MarketplaceOffer::class, 'parent_offer_id');
    }

    /**
     * Get the related visit.
     */
    public function visit(): BelongsTo
    {
        return $this->belongsTo(MarketplaceVisit::class, 'marketplace_visit_id');
    }

    /**
     * Submit the offer for consideration.
     */
    public function submit(): void
    {
        $status = Status::where('domain', 'marketplace_offer')
            ->where('slug', 'marketplace_offer_submitted')
            ->first();

        $this->update([
            'status_id' => $status?->id ?? $this->status_id,
            'submitted_at' => now(),
        ]);
    }

    /**
     * Start negotiation on the offer.
     */
    public function startNegotiation(?int $agentId = null): void
    {
        $status = Status::where('domain', 'marketplace_offer')
            ->where('slug', 'marketplace_offer_negotiating')
            ->first();

        $this->update([
            'status_id' => $status?->id ?? $this->status_id,
            'assigned_agent' => $agentId ?? $this->assigned_agent,
        ]);
    }

    /**
     * Create a counter offer.
     */
    public function createCounterOffer(float $amount, ?string $response = null): self
    {
        $this->increment('negotiation_rounds');
        $this->update([
            'counter_offer_amount' => $amount,
            'agent_response' => $response,
        ]);

        return static::create([
            'tenant_id' => $this->tenant_id,
            'marketplace_unit_id' => $this->marketplace_unit_id,
            'marketplace_customer_id' => $this->marketplace_customer_id,
            'status_id' => $this->status_id,
            'offer_type' => $this->offer_type,
            'offer_amount' => $amount,
            'currency' => $this->currency,
            'payment_method' => $this->payment_method,
            'installment_months' => $this->installment_months,
            'conditions' => $this->conditions,
            'assigned_agent' => $this->assigned_agent,
            'is_counter_offer' => true,
            'parent_offer_id' => $this->id,
            'negotiation_rounds' => $this->negotiation_rounds,
            'valid_until' => now()->addDays(7),
            'marketplace_visit_id' => $this->marketplace_visit_id,
        ]);
    }

    /**
     * Submit for review.
     */
    public function submitForReview(): void
    {
        $status = Status::where('domain', 'marketplace_offer')
            ->where('slug', 'marketplace_offer_review')
            ->first();

        $this->update([
            'status_id' => $status?->id ?? $this->status_id,
        ]);
    }

    /**
     * Approve the offer.
     */
    public function approve(?int $approvedBy = null): void
    {
        $status = Status::where('domain', 'marketplace_offer')
            ->where('slug', 'marketplace_offer_approved')
            ->first();

        $this->update([
            'status_id' => $status?->id ?? $this->status_id,
            'approved_at' => now(),
            'approved_by' => $approvedBy,
            'reviewed_at' => $this->reviewed_at ?? now(),
            'reviewed_by' => $this->reviewed_by ?? $approvedBy,
        ]);
    }

    /**
     * Reject the offer.
     */
    public function reject(?string $reason = null, ?int $rejectedBy = null): void
    {
        $status = Status::where('domain', 'marketplace_offer')
            ->where('slug', 'marketplace_offer_rejected')
            ->first();

        $this->update([
            'status_id' => $status?->id ?? $this->status_id,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
            'reviewed_at' => now(),
            'reviewed_by' => $rejectedBy,
        ]);
    }

    /**
     * Accept the offer (by customer).
     */
    public function accept(): void
    {
        $status = Status::where('domain', 'marketplace_offer')
            ->where('slug', 'marketplace_offer_accepted')
            ->first();

        $this->update([
            'status_id' => $status?->id ?? $this->status_id,
            'accepted_at' => now(),
            'final_amount' => $this->counter_offer_amount ?? $this->offer_amount,
        ]);
    }

    /**
     * Cancel the offer.
     */
    public function cancel(?string $reason = null): void
    {
        $status = Status::where('domain', 'marketplace_offer')
            ->where('slug', 'marketplace_offer_cancelled')
            ->first();

        $this->update([
            'status_id' => $status?->id ?? $this->status_id,
            'cancelled_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Mark the offer as expired.
     */
    public function markExpired(): void
    {
        $status = Status::where('domain', 'marketplace_offer')
            ->where('slug', 'marketplace_offer_expired')
            ->first();

        $this->update([
            'status_id' => $status?->id ?? $this->status_id,
            'expired_at' => now(),
        ]);
    }

    /**
     * Record booking deposit payment.
     */
    public function recordDeposit(float $amount, ?string $paymentReference = null): void
    {
        $this->update([
            'booking_deposit' => $amount,
            'deposit_paid_at' => now(),
            'deposit_payment_reference' => $paymentReference,
        ]);
    }

    /**
     * Refund the booking deposit.
     */
    public function refundDeposit(): void
    {
        $this->update([
            'deposit_refunded_at' => now(),
        ]);
    }

    /**
     * Sign the contract.
     */
    public function signContract(?string $contractReference = null, ?int $signedBy = null): void
    {
        $status = Status::where('domain', 'marketplace_offer')
            ->where('slug', 'marketplace_offer_contracted')
            ->first();

        $this->update([
            'status_id' => $status?->id ?? $this->status_id,
            'contract_reference' => $contractReference,
            'contract_signed_at' => now(),
            'contract_signed_by' => $signedBy,
        ]);
    }

    /**
     * Complete the offer/sale.
     */
    public function complete(): void
    {
        $status = Status::where('domain', 'marketplace_offer')
            ->where('slug', 'marketplace_offer_completed')
            ->first();

        $this->update([
            'status_id' => $status?->id ?? $this->status_id,
            'completed_at' => now(),
        ]);
    }

    /**
     * Assign an agent to the offer.
     */
    public function assignAgent(int $agentId): void
    {
        $this->update(['assigned_agent' => $agentId]);
    }

    /**
     * Generate a unique offer reference.
     */
    public function generateReference(): void
    {
        $this->update([
            'offer_reference' => 'OFF-'.strtoupper(uniqid()),
        ]);
    }

    /**
     * Check if the offer is a draft.
     */
    public function isDraft(): bool
    {
        return $this->status?->slug === 'marketplace_offer_draft' || $this->submitted_at === null;
    }

    /**
     * Check if the offer is submitted.
     */
    public function isSubmitted(): bool
    {
        return $this->status?->slug === 'marketplace_offer_submitted' || $this->submitted_at !== null;
    }

    /**
     * Check if the offer is in negotiation.
     */
    public function isNegotiating(): bool
    {
        return $this->status?->slug === 'marketplace_offer_negotiating';
    }

    /**
     * Check if the offer is under review.
     */
    public function isUnderReview(): bool
    {
        return $this->status?->slug === 'marketplace_offer_review';
    }

    /**
     * Check if the offer is approved.
     */
    public function isApproved(): bool
    {
        return $this->status?->slug === 'marketplace_offer_approved' || $this->approved_at !== null;
    }

    /**
     * Check if the offer is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status?->slug === 'marketplace_offer_rejected' || $this->rejected_at !== null;
    }

    /**
     * Check if the offer is accepted.
     */
    public function isAccepted(): bool
    {
        return $this->status?->slug === 'marketplace_offer_accepted' || $this->accepted_at !== null;
    }

    /**
     * Check if the offer is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status?->slug === 'marketplace_offer_cancelled' || $this->cancelled_at !== null;
    }

    /**
     * Check if the offer is expired.
     */
    public function isExpired(): bool
    {
        if ($this->status?->slug === 'marketplace_offer_expired' || $this->expired_at !== null) {
            return true;
        }

        return $this->valid_until !== null && $this->valid_until->isPast();
    }

    /**
     * Check if the offer has a signed contract.
     */
    public function hasContract(): bool
    {
        return $this->contract_signed_at !== null;
    }

    /**
     * Check if the offer is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status?->slug === 'marketplace_offer_completed' || $this->completed_at !== null;
    }

    /**
     * Check if the deposit is paid.
     */
    public function isDepositPaid(): bool
    {
        return $this->deposit_paid_at !== null && $this->deposit_refunded_at === null;
    }

    /**
     * Check if the deposit is refunded.
     */
    public function isDepositRefunded(): bool
    {
        return $this->deposit_refunded_at !== null;
    }

    /**
     * Check if this is a counter offer.
     */
    public function isCounterOffer(): bool
    {
        return $this->is_counter_offer;
    }

    /**
     * Check if the offer is active (not cancelled, rejected, expired, or completed).
     */
    public function isActive(): bool
    {
        return ! $this->isCancelled() && ! $this->isRejected() && ! $this->isExpired() && ! $this->isCompleted();
    }
}
