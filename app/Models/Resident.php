<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use App\Concerns\HasBilingualName;
use App\Concerns\HasContactInfo;
use App\Concerns\HasManagerScope;
use App\Support\ManagerScopeHelper;
use Database\Factories\ResidentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resident extends Model
{
    /** @use HasFactory<ResidentFactory> */
    use BelongsToAccountTenant, HasFactory, HasManagerScope, SoftDeletes;

    use HasBilingualName, HasContactInfo {
        HasContactInfo::name insteadof HasBilingualName;
    }

    protected $table = 'rf_tenants';

    /**
     * Residents: filter via rf_leases.tenant_id → lease_units → rf_units.
     * Residents with no active lease will be hidden when scoped (documented as intended).
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
                ->select('rf_leases.tenant_id')
                ->from('rf_leases')
                ->join('lease_units', 'lease_units.lease_id', '=', 'rf_leases.id')
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
        'source_id',
        'accepted_invite',
        'relation',
        'relation_key',
        'account_tenant_id',
    ];

    protected $attributes = [
        'active' => true,
        'accepted_invite' => false,
    ];

    protected function casts(): array
    {
        return [
            'accepted_invite' => 'boolean',
        ];
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->where('collection', 'documents');
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class, 'tenant_id');
    }

    public function dependents(): MorphMany
    {
        return $this->morphMany(Dependent::class, 'dependable');
    }

    /** @return HasMany<Unit, $this> */
    public function units(): HasMany
    {
        return $this->hasMany(Unit::class, 'tenant_id');
    }
}
