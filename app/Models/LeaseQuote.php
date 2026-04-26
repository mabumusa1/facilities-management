<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use App\Concerns\HasManagerScope;
use App\Support\ManagerScopeHelper;
use Database\Factories\LeaseQuoteFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LeaseQuote extends Model
{
    /** @use HasFactory<LeaseQuoteFactory> */
    use BelongsToAccountTenant, HasFactory, HasManagerScope, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $quote): void {
            if (empty($quote->public_token)) {
                $quote->public_token = (string) Str::uuid();
            }
        });
    }

    /**
     * LeaseQuotes: filter via unit_id → rf_units community/building FK.
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
            $this->getTable().'.unit_id',
            fn ($sub) => $sub
                ->select('id')
                ->from('rf_units')
                ->where(function ($q) use ($communityIds, $buildingIds): void {
                    if (! empty($communityIds)) {
                        $q->orWhereIn('rf_community_id', $communityIds);
                    }
                    if (! empty($buildingIds)) {
                        $q->orWhereIn('rf_building_id', $buildingIds);
                    }
                })
        );
    }

    protected $fillable = [
        'account_tenant_id',
        'quote_number',
        'public_token',
        'unit_id',
        'contact_id',
        'contract_type_id',
        'status_id',
        'duration_months',
        'start_date',
        'rent_amount',
        'payment_frequency_id',
        'security_deposit',
        'additional_charges',
        'special_conditions',
        'valid_until',
        'version',
        'parent_quote_id',
        'revision_note',
        'email_subject_prefix',
        'rejection_reason',
        'marketplace_unit_id',
        'created_by_id',
    ];

    protected $attributes = [
        'version' => 1,
        'security_deposit' => 0,
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'valid_until' => 'datetime',
            'rent_amount' => 'decimal:2',
            'security_deposit' => 'decimal:2',
            'additional_charges' => 'array',
            'special_conditions' => 'array',
            'version' => 'integer',
            'duration_months' => 'integer',
        ];
    }

    /** @return BelongsTo<Unit, $this> */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /** @return BelongsTo<Resident, $this> */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Resident::class, 'contact_id');
    }

    /** @return BelongsTo<ContractType, $this> */
    public function contractType(): BelongsTo
    {
        return $this->belongsTo(ContractType::class);
    }

    /** @return BelongsTo<Status, $this> */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /** @return BelongsTo<Setting, $this> */
    public function paymentFrequency(): BelongsTo
    {
        return $this->belongsTo(Setting::class, 'payment_frequency_id');
    }

    /** @return BelongsTo<MarketplaceUnit, $this> */
    public function marketplaceUnit(): BelongsTo
    {
        return $this->belongsTo(MarketplaceUnit::class);
    }

    /** @return BelongsTo<Admin, $this> */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_id');
    }

    /**
     * Revisions of this quote — child quotes that have this quote as their parent.
     *
     * @return HasMany<self, $this>
     */
    public function revisions(): HasMany
    {
        return $this->hasMany(self::class, 'parent_quote_id');
    }

    /** @return BelongsTo<self, $this> */
    public function parentQuote(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_quote_id');
    }

    /**
     * The lease created when this quote is converted (story #172).
     * The lease_id FK column will be added to rf_leases in story #172.
     *
     * @return HasOne<Lease, $this>
     */
    public function lease(): HasOne
    {
        return $this->hasOne(Lease::class, 'quote_id');
    }
}
