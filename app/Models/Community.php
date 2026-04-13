<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CommunityFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Represents a property community (residential compound, development, etc.)
 *
 * A community is the top-level grouping in the property hierarchy:
 * Community → Building → Unit
 */
class Community extends Model
{
    /** @use HasFactory<CommunityFactory> */
    use HasFactory, SoftDeletes;

    // Status constants
    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    // Marketplace type constants
    public const MARKETPLACE_TYPE_RENT = 'rent';

    public const MARKETPLACE_TYPE_BUY = 'buy';

    public const MARKETPLACE_TYPE_BOTH = 'both';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'country_id',
        'currency_id',
        'city_id',
        'district_id',
        'sales_commission_rate',
        'rental_commission_rate',
        'map',
        'is_marketplace',
        'is_buy',
        'marketplace_type',
        'is_off_plan_sale',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sales_commission_rate' => 'decimal:2',
            'rental_commission_rate' => 'decimal:2',
            'map' => 'array',
            'is_marketplace' => 'boolean',
            'is_buy' => 'boolean',
            'is_off_plan_sale' => 'boolean',
        ];
    }

    // ==========================================
    // Relationships
    // ==========================================

    /**
     * Get the tenant that owns this community.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the country where this community is located.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the currency used by this community.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the city where this community is located.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the district where this community is located.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get the buildings in this community.
     */
    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class);
    }

    /**
     * Get the amenities available in this community.
     */
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'community_amenities')
            ->withTimestamps();
    }

    // ==========================================
    // Scopes
    // ==========================================

    /**
     * Scope to filter active communities.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope to filter inactive communities.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    /**
     * Scope to filter communities for a specific tenant.
     */
    public function scopeForTenant(Builder $query, Tenant|int $tenant): Builder
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;

        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope to filter marketplace-enabled communities.
     */
    public function scopeMarketplace(Builder $query): Builder
    {
        return $query->where('is_marketplace', true);
    }

    /**
     * Scope to filter communities in a specific city.
     */
    public function scopeInCity(Builder $query, City|int $city): Builder
    {
        $cityId = $city instanceof City ? $city->id : $city;

        return $query->where('city_id', $cityId);
    }

    /**
     * Scope to filter communities in a specific district.
     */
    public function scopeInDistrict(Builder $query, District|int $district): Builder
    {
        $districtId = $district instanceof District ? $district->id : $district;

        return $query->where('district_id', $districtId);
    }

    // ==========================================
    // Helper Methods
    // ==========================================

    /**
     * Check if the community is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if the community is inactive.
     */
    public function isInactive(): bool
    {
        return $this->status === self::STATUS_INACTIVE;
    }

    /**
     * Check if the community is listed on marketplace.
     */
    public function isOnMarketplace(): bool
    {
        return $this->is_marketplace === true;
    }

    /**
     * Activate the community.
     */
    public function activate(): bool
    {
        return $this->update(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Deactivate the community.
     */
    public function deactivate(): bool
    {
        return $this->update(['status' => self::STATUS_INACTIVE]);
    }

    /**
     * Get all available statuses.
     *
     * @return array<string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
        ];
    }

    /**
     * Get all available marketplace types.
     *
     * @return array<string>
     */
    public static function marketplaceTypes(): array
    {
        return [
            self::MARKETPLACE_TYPE_RENT,
            self::MARKETPLACE_TYPE_BUY,
            self::MARKETPLACE_TYPE_BOTH,
        ];
    }

    /**
     * Get the count of buildings in this community.
     */
    public function getBuildingsCountAttribute(): int
    {
        return $this->buildings()->count();
    }
}
