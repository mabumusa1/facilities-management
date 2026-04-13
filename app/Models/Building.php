<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\BuildingFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Represents a physical building within a community.
 *
 * Buildings are the second level in the property hierarchy:
 * Community → Building → Unit
 */
class Building extends Model
{
    /** @use HasFactory<BuildingFactory> */
    use HasFactory, SoftDeletes;

    // Status constants
    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'community_id',
        'name',
        'city_id',
        'district_id',
        'no_floors',
        'year_built',
        'map',
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
            'no_floors' => 'integer',
            'year_built' => 'integer',
            'map' => 'array',
        ];
    }

    // ==========================================
    // Relationships
    // ==========================================

    /**
     * Get the tenant that owns this building.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the community this building belongs to.
     */
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * Get the city where this building is located.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the district where this building is located.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get the units in this building.
     */
    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    // ==========================================
    // Scopes
    // ==========================================

    /**
     * Scope to filter active buildings.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope to filter inactive buildings.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    /**
     * Scope to filter buildings for a specific tenant.
     */
    public function scopeForTenant(Builder $query, Tenant|int $tenant): Builder
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;

        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope to filter buildings for a specific community.
     */
    public function scopeForCommunity(Builder $query, Community|int $community): Builder
    {
        $communityId = $community instanceof Community ? $community->id : $community;

        return $query->where('community_id', $communityId);
    }

    /**
     * Scope to filter buildings in a specific city.
     */
    public function scopeInCity(Builder $query, City|int $city): Builder
    {
        $cityId = $city instanceof City ? $city->id : $city;

        return $query->where('city_id', $cityId);
    }

    /**
     * Scope to filter buildings in a specific district.
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
     * Check if the building is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if the building is inactive.
     */
    public function isInactive(): bool
    {
        return $this->status === self::STATUS_INACTIVE;
    }

    /**
     * Activate the building.
     */
    public function activate(): bool
    {
        return $this->update(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Deactivate the building.
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
     * Get the count of units in this building.
     */
    public function getUnitsCountAttribute(): int
    {
        return $this->units()->count();
    }
}
