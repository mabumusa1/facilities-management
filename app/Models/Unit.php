<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use App\Concerns\HasManagerScope;
use App\Support\ManagerScopeHelper;
use Database\Factories\UnitFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Unit extends Model
{
    /** @use HasFactory<UnitFactory> */
    use BelongsToAccountTenant, HasFactory, HasManagerScope;

    /**
     * Units: filter by rf_community_id or rf_building_id.
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

        return $query->where(function (Builder $q) use ($communityIds, $buildingIds): void {
            if (! empty($communityIds)) {
                $q->orWhereIn($this->getTable().'.rf_community_id', $communityIds);
            }
            if (! empty($buildingIds)) {
                $q->orWhereIn($this->getTable().'.rf_building_id', $buildingIds);
            }
        });
    }

    protected $table = 'rf_units';

    protected $fillable = [
        'name',
        'rf_community_id',
        'rf_building_id',
        'category_id',
        'type_id',
        'status_id',
        'city_id',
        'district_id',
        'owner_id',
        'tenant_id',
        'account_tenant_id',
        'year_build',
        'net_area',
        'floor_no',
        'about',
        'map',
        'is_market_place',
        'is_buy',
        'is_off_plan_sale',
        'renewal_status',
        'marketplace_booking_unit_id',
    ];

    protected $attributes = [
        'is_market_place' => false,
        'is_buy' => false,
        'is_off_plan_sale' => false,
        'renewal_status' => false,
    ];

    protected function casts(): array
    {
        return [
            'map' => 'array',
            'is_market_place' => 'boolean',
            'is_buy' => 'boolean',
            'is_off_plan_sale' => 'boolean',
            'renewal_status' => 'boolean',
            'net_area' => 'decimal:2',
        ];
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class, 'rf_community_id');
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'rf_building_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(UnitCategory::class, 'category_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(UnitType::class, 'type_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function photos(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->where('collection', 'photos');
    }

    public function floorPlans(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->where('collection', 'floor_plans');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->where('collection', 'documents');
    }

    /** @return HasMany<UnitSpecification, $this> */
    public function specifications(): HasMany
    {
        return $this->hasMany(UnitSpecification::class);
    }

    /** @return HasMany<UnitRoom, $this> */
    public function rooms(): HasMany
    {
        return $this->hasMany(UnitRoom::class);
    }

    /** @return HasMany<UnitArea, $this> */
    public function areas(): HasMany
    {
        return $this->hasMany(UnitArea::class);
    }

    /** @return BelongsToMany<Feature, $this> */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'feature_unit');
    }

    /** @return HasMany<MarketplaceUnit, $this> */
    public function marketplaceListings(): HasMany
    {
        return $this->hasMany(MarketplaceUnit::class);
    }

    /** @return HasMany<MarketplaceOffer, $this> */
    public function marketplaceOffers(): HasMany
    {
        return $this->hasMany(MarketplaceOffer::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Resident::class, 'tenant_id');
    }
}
