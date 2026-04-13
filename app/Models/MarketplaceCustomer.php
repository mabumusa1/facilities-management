<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Represents a prospective buyer/renter in the marketplace.
 */
#[Fillable([
    'tenant_id',
    'contact_id',
    'first_name',
    'last_name',
    'email',
    'phone',
    'national_id',
    'customer_type',
    'status',
    'budget_min',
    'budget_max',
    'preferred_payment_method',
    'preferred_unit_types',
    'preferred_locations',
    'preferred_bedrooms_min',
    'preferred_bedrooms_max',
    'preferred_area_min',
    'preferred_area_max',
    'source',
    'campaign',
    'lead_score',
    'notes',
    'assigned_agent',
    'converted_at',
    'converted_unit_id',
])]
class MarketplaceCustomer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'budget_min' => 'decimal:2',
            'budget_max' => 'decimal:2',
            'preferred_area_min' => 'decimal:2',
            'preferred_area_max' => 'decimal:2',
            'preferred_unit_types' => 'array',
            'preferred_locations' => 'array',
            'converted_at' => 'datetime',
        ];
    }

    /**
     * Get the tenant that owns this customer.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the linked contact if any.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the assigned agent.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'assigned_agent');
    }

    /**
     * Get the unit they converted on.
     */
    public function convertedUnit(): BelongsTo
    {
        return $this->belongsTo(MarketplaceUnit::class, 'converted_unit_id');
    }

    /**
     * Get the visits made by this customer.
     */
    public function visits(): HasMany
    {
        return $this->hasMany(MarketplaceVisit::class);
    }

    /**
     * Get the full name of the customer.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Qualify the customer lead.
     */
    public function qualify(): void
    {
        $this->update(['status' => 'qualified']);
    }

    /**
     * Start negotiation with the customer.
     */
    public function startNegotiation(): void
    {
        $this->update(['status' => 'negotiating']);
    }

    /**
     * Mark the customer as converted.
     */
    public function convert(?int $unitId = null): void
    {
        $this->update([
            'status' => 'converted',
            'converted_at' => now(),
            'converted_unit_id' => $unitId,
        ]);
    }

    /**
     * Mark the customer as inactive.
     */
    public function deactivate(): void
    {
        $this->update(['status' => 'inactive']);
    }

    /**
     * Reactivate the customer.
     */
    public function reactivate(): void
    {
        $this->update(['status' => 'active']);
    }

    /**
     * Assign an agent to this customer.
     */
    public function assignAgent(int $agentId): void
    {
        $this->update(['assigned_agent' => $agentId]);
    }

    /**
     * Update the lead score.
     */
    public function updateLeadScore(int $score): void
    {
        $this->update(['lead_score' => $score]);
    }

    /**
     * Increment the lead score.
     */
    public function incrementLeadScore(int $amount = 1): void
    {
        $this->increment('lead_score', $amount);
    }

    /**
     * Check if the customer is a lead.
     */
    public function isLead(): bool
    {
        return $this->status === 'lead';
    }

    /**
     * Check if the customer is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the customer is qualified.
     */
    public function isQualified(): bool
    {
        return $this->status === 'qualified';
    }

    /**
     * Check if the customer is negotiating.
     */
    public function isNegotiating(): bool
    {
        return $this->status === 'negotiating';
    }

    /**
     * Check if the customer is converted.
     */
    public function isConverted(): bool
    {
        return $this->status === 'converted';
    }

    /**
     * Check if the customer is inactive.
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Check if the customer is a buyer.
     */
    public function isBuyer(): bool
    {
        return $this->customer_type === 'buyer';
    }

    /**
     * Check if the customer is a renter.
     */
    public function isRenter(): bool
    {
        return $this->customer_type === 'renter';
    }

    /**
     * Check if the customer is an investor.
     */
    public function isInvestor(): bool
    {
        return $this->customer_type === 'investor';
    }

    /**
     * Check if budget matches a price.
     */
    public function isWithinBudget(float $price): bool
    {
        $minOk = $this->budget_min === null || $price >= $this->budget_min;
        $maxOk = $this->budget_max === null || $price <= $this->budget_max;

        return $minOk && $maxOk;
    }
}
