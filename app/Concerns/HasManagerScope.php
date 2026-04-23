<?php

namespace App\Concerns;

use App\Models\User;
use App\Support\ManagerScopeHelper;
use Illuminate\Database\Eloquent\Builder;

/**
 * Provides the `forManager(User $user)` local Eloquent scope.
 *
 * Models that use this trait expose a `scopeForManager` method that controllers
 * opt into explicitly:
 *
 *   Model::query()->forManager(auth()->user())->paginate()
 *
 * Each model may override `scopeForManager` to implement a custom join path.
 * The default implementation handles models with direct `community_id` /
 * `building_id` columns. Override as needed.
 *
 * accountAdmins and system-wide roles are short-circuited via
 * ManagerScopeHelper::scopesForUser() returning is_unrestricted=true, in which
 * case this scope adds no WHERE clauses.
 */
trait HasManagerScope
{
    /**
     * Scope the query to records the given manager user is allowed to see.
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

        return $query->where(function (Builder $q) use ($communityIds, $buildingIds): void {
            $hasFilter = false;

            if (! empty($communityIds) && $this->hasCommunityIdColumn()) {
                $q->orWhereIn($this->getTable().'.community_id', $communityIds);
                $hasFilter = true;
            }

            if (! empty($buildingIds) && $this->hasBuildingIdColumn()) {
                $q->orWhereIn($this->getTable().'.building_id', $buildingIds);
                $hasFilter = true;
            }

            // If neither filter applies, return nothing (scope columns set but
            // this model has no matching FK — safety net).
            if (! $hasFilter) {
                $q->whereRaw('1 = 0');
            }
        });
    }

    /**
     * Whether this model's table has a `community_id` column for filtering.
     * Override to return false if not applicable.
     */
    protected function hasCommunityIdColumn(): bool
    {
        return true;
    }

    /**
     * Whether this model's table has a `building_id` column for filtering.
     * Override to return false if not applicable.
     */
    protected function hasBuildingIdColumn(): bool
    {
        return false;
    }
}
