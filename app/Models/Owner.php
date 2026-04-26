<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use App\Concerns\HasBilingualName;
use App\Concerns\HasContactInfo;
use App\Concerns\HasManagerScope;
use App\Support\ManagerScopeHelper;
use Database\Factories\OwnerFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Owner extends Model
{
    /** @use HasFactory<OwnerFactory> */
    use BelongsToAccountTenant, HasFactory, HasManagerScope, SoftDeletes;

    use HasBilingualName, HasContactInfo {
        HasContactInfo::name insteadof HasBilingualName;
    }

    protected $table = 'rf_owners';

    /**
     * Owners: filter via rf_units.owner_id → community/building FK.
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
                ->select('owner_id')
                ->from('rf_units')
                ->whereNotNull('owner_id')
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
        'relation',
        'relation_key',
        'account_tenant_id',
    ];

    protected $attributes = [
        'active' => true,
    ];

    /** @return HasMany<Unit, $this> */
    public function units(): HasMany
    {
        return $this->hasMany(Unit::class, 'owner_id');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->where('collection', 'documents');
    }

    public function dependents(): MorphMany
    {
        return $this->morphMany(Dependent::class, 'dependable');
    }

    /** @return HasMany<UnitOwnership, $this> */
    public function unitOwnerships(): HasMany
    {
        return $this->hasMany(UnitOwnership::class);
    }

    public function ownedUnits(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'rf_unit_ownerships')
            ->withPivot(['ownership_type', 'ownership_percentage', 'start_date', 'end_date'])
            ->withTimestamps();
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(ContactActivity::class, 'contact');
    }
}
