<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use App\Concerns\HasManagerScope;
use App\Enums\LeaseEscalationType;
use App\Enums\RentalType;
use App\Enums\TenantType;
use App\Support\ManagerScopeHelper;
use Database\Factories\LeaseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lease extends Model
{
    /** @use HasFactory<LeaseFactory> */
    use BelongsToAccountTenant, HasFactory, HasManagerScope, SoftDeletes;

    protected $table = 'rf_leases';

    /**
     * Leases: filter via lease_units pivot → rf_units community/building FK.
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeForManager(Builder $query, User $user): Builder
    {
        $scopes = ManagerScopeHelper::scopesForUser($user);

        if ($scopes['is_unrestricted']) {
            return $query;
        }

        $communityIds = $scopes['community_ids'];
        $buildingIds = $scopes['building_ids'];

        if (empty($communityIds) && empty($buildingIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn(
            $this->getTable().'.id',
            fn ($sub) => $sub
                ->select('lease_units.lease_id')
                ->from('lease_units')
                ->join('rf_units', 'rf_units.id', '=', 'lease_units.unit_id')
                ->where(function ($q) use ($communityIds, $buildingIds): void {
                    if (! empty($communityIds)) {
                        $q->orWhereIn('rf_units.rf_community_id', $communityIds);
                    }
                    if (! empty($buildingIds)) {
                        $q->orWhereIn('rf_units.rf_building_id', $buildingIds);
                    }
                })
        );
    }

    protected $fillable = [
        'contract_number',
        'tenant_id',
        'status_id',
        'lease_unit_type_id',
        'rental_contract_type_id',
        'payment_schedule_id',
        'created_by_id',
        'deal_owner_id',
        'account_tenant_id',
        'start_date',
        'end_date',
        'handover_date',
        'actual_end_at',
        'tenant_type',
        'rental_type',
        'rental_total_amount',
        'security_deposit_amount',
        'security_deposit_due_date',
        'lease_escalations_type',
        'terms_conditions',
        'is_terms',
        'is_sub_lease',
        'parent_lease_id',
        'legal_representative',
        'fit_out_status',
        'free_period',
        'number_of_years',
        'number_of_months',
        'number_of_days',
        'is_renew',
        'is_move_out',
        'is_old',
        'pdf_url',
        'quote_id',
        'kyc_complete',
        'kyc_submitted_at',
        'approved_by_id',
        'approved_at',
        'rejected_by_id',
        'rejected_at',
        'rejection_reason',
        'current_amendment_number',
    ];

    protected $attributes = [
        'is_terms' => false,
        'is_sub_lease' => false,
        'free_period' => 0,
        'is_renew' => false,
        'is_move_out' => false,
        'is_old' => false,
    ];

    protected function casts(): array
    {
        return [
            'tenant_type' => TenantType::class,
            'rental_type' => RentalType::class,
            'lease_escalations_type' => LeaseEscalationType::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'handover_date' => 'date',
            'actual_end_at' => 'date',
            'security_deposit_due_date' => 'date',
            'kyc_submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'rental_total_amount' => 'decimal:2',
            'security_deposit_amount' => 'decimal:2',
            'is_terms' => 'boolean',
            'is_sub_lease' => 'boolean',
            'is_renew' => 'boolean',
            'is_move_out' => 'boolean',
            'is_old' => 'boolean',
            'kyc_complete' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Resident::class, 'tenant_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function leaseUnitType(): BelongsTo
    {
        return $this->belongsTo(UnitCategory::class, 'lease_unit_type_id');
    }

    public function rentalContractType(): BelongsTo
    {
        return $this->belongsTo(Setting::class, 'rental_contract_type_id');
    }

    public function paymentSchedule(): BelongsTo
    {
        return $this->belongsTo(Setting::class, 'payment_schedule_id');
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'lease_units')
            ->withPivot('rental_annual_type', 'annual_rental_amount', 'net_area', 'meter_cost')
            ->withTimestamps();
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'lease_id');
    }

    public function additionalFees(): HasMany
    {
        return $this->hasMany(LeaseAdditionalFee::class, 'lease_id');
    }

    public function escalations(): HasMany
    {
        return $this->hasMany(LeaseEscalation::class, 'lease_id');
    }

    public function subleases(): HasMany
    {
        return $this->hasMany(self::class, 'parent_lease_id');
    }

    public function parentLease(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_lease_id');
    }

    public function moveOuts(): HasMany
    {
        return $this->hasMany(MoveOut::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_id');
    }

    public function dealOwner(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'deal_owner_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by_id');
    }

    /** @return BelongsTo<LeaseQuote, $this> */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(LeaseQuote::class, 'quote_id');
    }

    /** @return HasMany<LeaseKycDocument, $this> */
    public function kycDocuments(): HasMany
    {
        return $this->hasMany(LeaseKycDocument::class, 'lease_id');
    }

    /** @return HasMany<LeaseAmendment, $this> */
    public function amendments(): HasMany
    {
        return $this->hasMany(LeaseAmendment::class, 'lease_id')->orderByDesc('amendment_number');
    }

    /** @return HasMany<LeaseNotice, $this> */
    public function notices(): HasMany
    {
        return $this->hasMany(LeaseNotice::class, 'lease_id');
    }

    public function getTotalUnpaidAmountAttribute(): string
    {
        return number_format(
            (float) $this->transactions()->sum('amount') - (float) Payment::whereIn('transaction_id', $this->transactions()->select('id'))->sum('amount'),
            2,
            '.',
            '',
        );
    }

    public function getUnpaidTransactionsCountAttribute(): int
    {
        return $this->transactions()->where('is_paid', false)->count();
    }
}
