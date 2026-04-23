<?php

namespace App\Models;

use App\Concerns\HasManagerScope;
use App\Support\ManagerScopeHelper;
use Database\Factories\MarketplaceVisitFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceVisit extends Model
{
    /** @use HasFactory<MarketplaceVisitFactory> */
    use HasFactory, HasManagerScope;

    protected $table = 'rf_marketplace_visits';

    /**
     * MarketplaceVisits: filter via marketplace_unit_id → rf_marketplace_units.unit_id → rf_units.
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
            $this->getTable().'.marketplace_unit_id',
            fn ($sub) => $sub
                ->select('rf_marketplace_units.id')
                ->from('rf_marketplace_units')
                ->join('rf_units', 'rf_units.id', '=', 'rf_marketplace_units.unit_id')
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
        'marketplace_unit_id',
        'status_id',
        'visitor_name',
        'visitor_phone',
        'scheduled_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<MarketplaceUnit, $this> */
    public function marketplaceUnit(): BelongsTo
    {
        return $this->belongsTo(MarketplaceUnit::class);
    }

    /** @return BelongsTo<Status, $this> */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }
}
