<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use App\Concerns\HasBilingualName;
use App\Concerns\HasManagerScope;
use Database\Factories\FacilityFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facility extends Model
{
    /** @use HasFactory<FacilityFactory> */
    use BelongsToAccountTenant, HasBilingualName, HasFactory, HasManagerScope, SoftDeletes;

    protected $table = 'rf_facilities';

    protected $fillable = [
        'category_id',
        'community_id',
        'name',
        'name_ar',
        'name_en',
        'description',
        'capacity',
        'open_time',
        'close_time',
        'booking_fee',
        'is_active',
        'requires_approval',
        'account_tenant_id',
        'currency',
        'type',
        'pricing_mode',
        'requires_booking',
        'booking_horizon_days',
        'cancellation_hours_before',
        'min_booking_duration_minutes',
        'max_booking_duration_minutes',
        'contract_required',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'booking_fee' => 'decimal:2',
            'is_active' => 'boolean',
            'requires_approval' => 'boolean',
            'requires_booking' => 'boolean',
            'contract_required' => 'boolean',
            'booking_horizon_days' => 'integer',
            'cancellation_hours_before' => 'integer',
            'min_booking_duration_minutes' => 'integer',
            'max_booking_duration_minutes' => 'integer',
        ];
    }

    /** @return BelongsTo<FacilityCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(FacilityCategory::class, 'category_id');
    }

    /** @return BelongsTo<Community, $this> */
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    /** @return HasMany<FacilityBooking, $this> */
    public function bookings(): HasMany
    {
        return $this->hasMany(FacilityBooking::class);
    }

    /** @return HasMany<FacilityAvailabilityRule, $this> */
    public function availabilityRules(): HasMany
    {
        return $this->hasMany(FacilityAvailabilityRule::class);
    }

    /**
     * Scope to only active facilities.
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to facilities belonging to a specific community.
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeForCommunity(Builder $query, int $communityId): Builder
    {
        return $query->where('community_id', $communityId);
    }
}
