<?php

declare(strict_types=1);

namespace App\Multitenancy;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * Trait for users with scoped access to communities and buildings.
 *
 * This trait provides:
 * - Relationships to communities and buildings
 * - Methods to check and manage scoped access
 * - Methods to filter queries based on user's access scope
 */
trait HasScopedAccess
{
    /**
     * Get the communities this user has access to.
     */
    public function accessibleCommunities(): BelongsToMany
    {
        return $this->belongsToMany(
            config('multitenancy.community_model', 'App\Models\Community'),
            'user_communities',
            'user_id',
            'community_id'
        )->withTimestamps();
    }

    /**
     * Get the buildings this user has access to.
     */
    public function accessibleBuildings(): BelongsToMany
    {
        return $this->belongsToMany(
            config('multitenancy.building_model', 'App\Models\Building'),
            'user_buildings',
            'user_id',
            'building_id'
        )->withTimestamps();
    }

    /**
     * Get all community IDs this user has access to.
     *
     * Returns all community IDs if is_all_communities is true,
     * otherwise returns the specific assigned community IDs.
     *
     * @return Collection<int, int>
     */
    public function getAccessibleCommunityIds(): Collection
    {
        if ($this->hasAllCommunitiesAccess()) {
            return collect(); // Empty means all
        }

        return $this->accessibleCommunities()->pluck('community_id');
    }

    /**
     * Get all building IDs this user has access to.
     *
     * Returns all building IDs if is_all_buildings is true,
     * otherwise returns the specific assigned building IDs.
     *
     * @return Collection<int, int>
     */
    public function getAccessibleBuildingIds(): Collection
    {
        if ($this->hasAllBuildingsAccess()) {
            return collect(); // Empty means all
        }

        return $this->accessibleBuildings()->pluck('building_id');
    }

    /**
     * Check if the user has access to a specific community.
     */
    public function hasAccessToCommunity(int $communityId): bool
    {
        if ($this->hasAllCommunitiesAccess()) {
            return true;
        }

        return $this->accessibleCommunities()
            ->where('community_id', $communityId)
            ->exists();
    }

    /**
     * Check if the user has access to a specific building.
     */
    public function hasAccessToBuilding(int $buildingId): bool
    {
        if ($this->hasAllBuildingsAccess()) {
            return true;
        }

        return $this->accessibleBuildings()
            ->where('building_id', $buildingId)
            ->exists();
    }

    /**
     * Grant the user access to specific communities.
     *
     * @param  array<int>|int  $communityIds
     */
    public function grantCommunityAccess(array|int $communityIds): static
    {
        $communityIds = is_array($communityIds) ? $communityIds : [$communityIds];
        $this->accessibleCommunities()->syncWithoutDetaching($communityIds);

        return $this;
    }

    /**
     * Grant the user access to specific buildings.
     *
     * @param  array<int>|int  $buildingIds
     */
    public function grantBuildingAccess(array|int $buildingIds): static
    {
        $buildingIds = is_array($buildingIds) ? $buildingIds : [$buildingIds];
        $this->accessibleBuildings()->syncWithoutDetaching($buildingIds);

        return $this;
    }

    /**
     * Revoke the user's access to specific communities.
     *
     * @param  array<int>|int  $communityIds
     */
    public function revokeCommunityAccess(array|int $communityIds): static
    {
        $communityIds = is_array($communityIds) ? $communityIds : [$communityIds];
        $this->accessibleCommunities()->detach($communityIds);

        return $this;
    }

    /**
     * Revoke the user's access to specific buildings.
     *
     * @param  array<int>|int  $buildingIds
     */
    public function revokeBuildingAccess(array|int $buildingIds): static
    {
        $buildingIds = is_array($buildingIds) ? $buildingIds : [$buildingIds];
        $this->accessibleBuildings()->detach($buildingIds);

        return $this;
    }

    /**
     * Sync the user's community access.
     *
     * @param  array<int>  $communityIds
     */
    public function syncCommunityAccess(array $communityIds): static
    {
        $this->accessibleCommunities()->sync($communityIds);

        return $this;
    }

    /**
     * Sync the user's building access.
     *
     * @param  array<int>  $buildingIds
     */
    public function syncBuildingAccess(array $buildingIds): static
    {
        $this->accessibleBuildings()->sync($buildingIds);

        return $this;
    }

    /**
     * Grant the user unrestricted access (all communities and buildings).
     */
    public function grantUnrestrictedAccess(): static
    {
        $this->is_all_communities = true;
        $this->is_all_buildings = true;
        $this->save();

        // Clear specific assignments when granting full access
        $this->accessibleCommunities()->detach();
        $this->accessibleBuildings()->detach();

        return $this;
    }

    /**
     * Revoke unrestricted access and optionally assign specific scopes.
     *
     * @param  array<int>  $communityIds
     * @param  array<int>  $buildingIds
     */
    public function setRestrictedAccess(array $communityIds = [], array $buildingIds = []): static
    {
        $this->is_all_communities = false;
        $this->is_all_buildings = false;
        $this->save();

        if (! empty($communityIds)) {
            $this->syncCommunityAccess($communityIds);
        }

        if (! empty($buildingIds)) {
            $this->syncBuildingAccess($buildingIds);
        }

        return $this;
    }
}
