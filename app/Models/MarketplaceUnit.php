<?php

namespace App\Models;

use App\Concerns\HasManagerScope;
use App\Support\ManagerScopeHelper;
use Database\Factories\MarketplaceUnitFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketplaceUnit extends Model
{
    /** @use HasFactory<MarketplaceUnitFactory> */
    use HasFactory, HasManagerScope;

    protected $table = 'rf_marketplace_units';

    /**
     * MarketplaceUnits: filter via unit_id → rf_units community/building FK.
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
        'unit_id',
        'listing_type',
        'price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /** @return BelongsTo<Unit, $this> */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /** @return HasMany<MarketplaceVisit, $this> */
    public function visits(): HasMany
    {
        return $this->hasMany(MarketplaceVisit::class);
    }
}
