<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lease extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'status_id',
        'created_by_id',
        'deal_owner_id',
        'community_id',
        'building_id',
        'lease_unit_type_id',
        'rental_contract_type_id',
        'payment_schedule_id',
        'parent_lease_id',
        'contract_number',
        'tenant_type',
        'rental_type',
        'rental_total_amount',
        'security_deposit_amount',
        'security_deposit_due_date',
        'legal_representative',
        'fit_out_status',
        'start_date',
        'end_date',
        'handover_date',
        'actual_end_at',
        'free_period',
        'number_of_years',
        'number_of_months',
        'number_of_days',
        'lease_escalations_type',
        'lease_escalations',
        'additional_fees_lease',
        'terms_conditions',
        'is_terms',
        'is_sub_lease',
        'is_renew',
        'is_move_out',
        'is_old',
        'pdf_url',
    ];

    protected $casts = [
        'rental_total_amount' => 'decimal:2',
        'security_deposit_amount' => 'decimal:2',
        'security_deposit_due_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'handover_date' => 'date',
        'actual_end_at' => 'date',
        'free_period' => 'integer',
        'number_of_years' => 'integer',
        'number_of_months' => 'integer',
        'number_of_days' => 'integer',
        'lease_escalations' => 'array',
        'additional_fees_lease' => 'array',
        'is_terms' => 'boolean',
        'is_sub_lease' => 'boolean',
        'is_renew' => 'boolean',
        'is_move_out' => 'boolean',
        'is_old' => 'boolean',
    ];

    // Relationships
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'tenant_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'created_by_id');
    }

    public function dealOwner(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'deal_owner_id');
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'lease_units')
            ->withPivot(['rental_annual_type', 'annual_rental_amount', 'net_area', 'meter_cost'])
            ->withTimestamps();
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function subleases(): HasMany
    {
        return $this->hasMany(Lease::class, 'parent_lease_id');
    }

    public function parentLease(): BelongsTo
    {
        return $this->belongsTo(Lease::class, 'parent_lease_id');
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->where('is_move_out', false);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereDate('end_date', '<', now()->startOfDay());
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate('start_date', '>', now()->startOfDay());
    }

    public function scopeExpiringWithinDays(Builder $query, int $days): Builder
    {
        return $query->whereDate('end_date', '>=', now()->startOfDay())
            ->whereDate('end_date', '<=', now()->addDays($days)->endOfDay())
            ->where('is_move_out', false);
    }

    public function scopeByTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByUnit(Builder $query, int $unitId): Builder
    {
        return $query->whereHas('units', fn ($q) => $q->where('units.id', $unitId));
    }

    public function scopeMainLeases(Builder $query): Builder
    {
        return $query->where('is_sub_lease', false);
    }

    public function scopeSubleases(Builder $query): Builder
    {
        return $query->where('is_sub_lease', true);
    }

    public function scopeRenewedLeases(Builder $query): Builder
    {
        return $query->where('is_renew', true);
    }

    public function scopeResidential(Builder $query): Builder
    {
        return $query->where('lease_unit_type_id', 2); // Based on schema
    }

    public function scopeCommercial(Builder $query): Builder
    {
        return $query->where('lease_unit_type_id', '!=', 2);
    }

    // Helper Methods
    public function isActive(): bool
    {
        return $this->start_date <= now() && $this->end_date >= now() && ! $this->is_move_out;
    }

    public function isExpired(): bool
    {
        return $this->end_date < now()->startOfDay();
    }

    public function getDaysRemaining(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return (int) now()->startOfDay()->diffInDays($this->end_date, false);
    }

    public function getDuration(): string
    {
        return "{$this->number_of_years} years {$this->number_of_months} months {$this->number_of_days} days";
    }

    public function getTotalUnpaidAmount(): float
    {
        return (float) $this->transactions()
            ->where('is_paid', false)
            ->sum('left');
    }

    public function getUnpaidTransactionsCount(): int
    {
        return $this->transactions()
            ->where('is_paid', false)
            ->count();
    }

    public function markAsTerminated(): void
    {
        $this->update([
            'is_move_out' => true,
            'actual_end_at' => now(),
        ]);
    }

    public function markAsRenewed(Lease $newLease): void
    {
        $this->update(['is_renew' => true]);
        $newLease->update(['parent_lease_id' => $this->id]);
    }
}
