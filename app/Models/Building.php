<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use App\Concerns\HasManagerScope;
use App\Support\ManagerScopeHelper;
use Database\Factories\BuildingFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Building extends Model
{
    /** @use HasFactory<BuildingFactory> */
    use BelongsToAccountTenant, HasFactory, HasManagerScope;

    /**
     * Buildings: match by direct building_id OR via community (rf_community_id).
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
            if (! empty($buildingIds)) {
                $q->orWhereIn($this->getTable().'.id', $buildingIds);
            }
            if (! empty($communityIds)) {
                $q->orWhereIn($this->getTable().'.rf_community_id', $communityIds);
            }
        });
    }

    protected $table = 'rf_buildings';

    protected $fillable = [
        'name',
        'rf_community_id',
        'city_id',
        'district_id',
        'account_tenant_id',
        'no_floors',
        'year_build',
        'map',
    ];

    protected $attributes = [
        'no_floors' => 0,
    ];

    protected function casts(): array
    {
        return [
            'map' => 'array',
        ];
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class, 'rf_community_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class, 'rf_building_id');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->where('collection', 'photos');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->where('collection', 'documents');
    }
}
