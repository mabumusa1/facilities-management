<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UnitFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Represents a rentable/sellable unit within a building.
 *
 * Units are the third level in the property hierarchy:
 * Community → Building → Unit
 *
 * Units can be apartments, offices, retail spaces, or any
 * rentable/sellable space within a property.
 */
class Unit extends Model
{
    /** @use HasFactory<UnitFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'community_id',
        'building_id',
        'unit_category_id',
        'unit_type_id',
        'status_id',
        'city_id',
        'district_id',
        'name',
        'floor_no',
        'net_area',
        'year_built',
        'market_rent',
        'about',
        'map',
        'photos',
        'is_marketplace',
        'is_off_plan_sale',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'floor_no' => 'integer',
            'net_area' => 'decimal:2',
            'year_built' => 'integer',
            'market_rent' => 'decimal:2',
            'map' => 'array',
            'photos' => 'array',
            'is_marketplace' => 'boolean',
            'is_off_plan_sale' => 'boolean',
        ];
    }

    // ==========================================
    // Relationships
    // ==========================================

    /**
     * Get the tenant that owns this unit.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the community this unit belongs to.
     */
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * Get the building this unit is in.
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Get the unit category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(UnitCategory::class, 'unit_category_id');
    }

    /**
     * Get the unit type.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(UnitType::class, 'unit_type_id');
    }

    /**
     * Get the unit status.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Get the city where this unit is located.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the district where this unit is located.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    // ==========================================
    // Scopes
    // ==========================================

    /**
     * Scope to filter units for a specific tenant.
     */
    public function scopeForTenant(Builder $query, Tenant|int $tenant): Builder
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;

        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope to filter units for a specific community.
     */
    public function scopeForCommunity(Builder $query, Community|int $community): Builder
    {
        $communityId = $community instanceof Community ? $community->id : $community;

        return $query->where('community_id', $communityId);
    }

    /**
     * Scope to filter units for a specific building.
     */
    public function scopeForBuilding(Builder $query, Building|int $building): Builder
    {
        $buildingId = $building instanceof Building ? $building->id : $building;

        return $query->where('building_id', $buildingId);
    }

    /**
     * Scope to filter units by category.
     */
    public function scopeForCategory(Builder $query, UnitCategory|int $category): Builder
    {
        $categoryId = $category instanceof UnitCategory ? $category->id : $category;

        return $query->where('unit_category_id', $categoryId);
    }

    /**
     * Scope to filter units by type.
     */
    public function scopeForType(Builder $query, UnitType|int $type): Builder
    {
        $typeId = $type instanceof UnitType ? $type->id : $type;

        return $query->where('unit_type_id', $typeId);
    }

    /**
     * Scope to filter units by status.
     */
    public function scopeWithStatus(Builder $query, Status|int $status): Builder
    {
        $statusId = $status instanceof Status ? $status->id : $status;

        return $query->where('status_id', $statusId);
    }

    /**
     * Scope to filter units in a specific city.
     */
    public function scopeInCity(Builder $query, City|int $city): Builder
    {
        $cityId = $city instanceof City ? $city->id : $city;

        return $query->where('city_id', $cityId);
    }

    /**
     * Scope to filter units in a specific district.
     */
    public function scopeInDistrict(Builder $query, District|int $district): Builder
    {
        $districtId = $district instanceof District ? $district->id : $district;

        return $query->where('district_id', $districtId);
    }

    /**
     * Scope to filter marketplace units.
     */
    public function scopeMarketplace(Builder $query): Builder
    {
        return $query->where('is_marketplace', true);
    }

    /**
     * Scope to filter non-marketplace units.
     */
    public function scopeNotMarketplace(Builder $query): Builder
    {
        return $query->where('is_marketplace', false);
    }

    /**
     * Scope to filter off-plan sale units.
     */
    public function scopeOffPlanSale(Builder $query): Builder
    {
        return $query->where('is_off_plan_sale', true);
    }

    /**
     * Scope to filter units on a specific floor.
     */
    public function scopeOnFloor(Builder $query, int $floor): Builder
    {
        return $query->where('floor_no', $floor);
    }

    /**
     * Scope to filter units by minimum area.
     */
    public function scopeMinArea(Builder $query, float $minArea): Builder
    {
        return $query->where('net_area', '>=', $minArea);
    }

    /**
     * Scope to filter units by maximum area.
     */
    public function scopeMaxArea(Builder $query, float $maxArea): Builder
    {
        return $query->where('net_area', '<=', $maxArea);
    }

    /**
     * Scope to filter units by area range.
     */
    public function scopeAreaBetween(Builder $query, float $minArea, float $maxArea): Builder
    {
        return $query->whereBetween('net_area', [$minArea, $maxArea]);
    }

    /**
     * Scope to filter units by minimum rent.
     */
    public function scopeMinRent(Builder $query, float $minRent): Builder
    {
        return $query->where('market_rent', '>=', $minRent);
    }

    /**
     * Scope to filter units by maximum rent.
     */
    public function scopeMaxRent(Builder $query, float $maxRent): Builder
    {
        return $query->where('market_rent', '<=', $maxRent);
    }

    /**
     * Scope to filter units by rent range.
     */
    public function scopeRentBetween(Builder $query, float $minRent, float $maxRent): Builder
    {
        return $query->whereBetween('market_rent', [$minRent, $maxRent]);
    }

    // ==========================================
    // Helper Methods
    // ==========================================

    /**
     * Check if the unit is listed on marketplace.
     */
    public function isOnMarketplace(): bool
    {
        return $this->is_marketplace === true;
    }

    /**
     * Check if the unit is for off-plan sale.
     */
    public function isOffPlanSale(): bool
    {
        return $this->is_off_plan_sale === true;
    }

    /**
     * List the unit on marketplace.
     */
    public function listOnMarketplace(): bool
    {
        return $this->update(['is_marketplace' => true]);
    }

    /**
     * Remove the unit from marketplace.
     */
    public function removeFromMarketplace(): bool
    {
        return $this->update(['is_marketplace' => false]);
    }

    /**
     * Get the full address including building and community.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = [$this->name];

        if ($this->building) {
            $parts[] = $this->building->name;
        }

        if ($this->community) {
            $parts[] = $this->community->name;
        }

        return implode(', ', $parts);
    }

    /**
     * Check if the unit has photos.
     */
    public function hasPhotos(): bool
    {
        return ! empty($this->photos);
    }

    /**
     * Get the photo count.
     */
    public function getPhotoCountAttribute(): int
    {
        return is_array($this->photos) ? count($this->photos) : 0;
    }

    /**
     * Check if the unit has map coordinates.
     */
    public function hasMapCoordinates(): bool
    {
        return ! empty($this->map) && isset($this->map['latitude']) && isset($this->map['longitude']);
    }
}
