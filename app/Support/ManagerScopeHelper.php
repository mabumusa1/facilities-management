<?php

namespace App\Support;

use App\Enums\RolesEnum;
use App\Models\Announcement;
use App\Models\Building;
use App\Models\Community;
use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\Lease;
use App\Models\MarketplaceOffer;
use App\Models\MarketplaceUnit;
use App\Models\MarketplaceVisit;
use App\Models\Owner;
use App\Models\Payment;
use App\Models\Professional;
use App\Models\Request;
use App\Models\Resident;
use App\Models\Transaction;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ManagerScopeHelper
{
    /**
     * Resolve the scope arrays for a user, memoised per-request via once().
     *
     * @return array{community_ids: int[], building_ids: int[], service_type_ids: int[], is_unrestricted: bool}
     */
    public static function scopesForUser(User $user): array
    {
        /** @var array<int, array{community_ids: int[], building_ids: int[], service_type_ids: int[], is_unrestricted: bool}> $cache */
        static $cache = [];

        return $cache[$user->id] ??= (function () use ($user): array {
            // accountAdmins bypass is already enforced via Gate::before in AppServiceProvider.
            // Additionally, treat accountAdmins as unrestricted here so controllers
            // don't need to call ->forManager() at all.
            if ($user->hasRole(RolesEnum::ACCOUNT_ADMINS->value)) {
                return [
                    'community_ids' => [],
                    'building_ids' => [],
                    'service_type_ids' => [],
                    'is_unrestricted' => true,
                ];
            }

            $rows = \DB::table('model_has_roles')
                ->where('model_type', get_class($user))
                ->where('model_id', $user->id)
                ->select('community_id', 'building_id', 'service_type_id')
                ->get();

            // If any row has all three scope FKs null → system-wide role → unrestricted.
            $hasSystemWideRole = $rows->contains(
                fn ($row): bool => $row->community_id === null
                    && $row->building_id === null
                    && $row->service_type_id === null
            );

            if ($hasSystemWideRole) {
                return [
                    'community_ids' => [],
                    'building_ids' => [],
                    'service_type_ids' => [],
                    'is_unrestricted' => true,
                ];
            }

            return [
                'community_ids' => $rows
                    ->pluck('community_id')
                    ->filter()
                    ->map(fn ($id): int => (int) $id)
                    ->unique()
                    ->values()
                    ->all(),
                'building_ids' => $rows
                    ->pluck('building_id')
                    ->filter()
                    ->map(fn ($id): int => (int) $id)
                    ->unique()
                    ->values()
                    ->all(),
                'service_type_ids' => $rows
                    ->pluck('service_type_id')
                    ->filter()
                    ->map(fn ($id): int => (int) $id)
                    ->unique()
                    ->values()
                    ->all(),
                'is_unrestricted' => false,
            ];
        })();
    }

    /**
     * Check if a user can access a single model instance.
     * Used by policies to validate write operations.
     */
    public static function userCanAccessModel(User $user, Model $model): bool
    {
        $scopes = static::scopesForUser($user);

        if ($scopes['is_unrestricted']) {
            return true;
        }

        $communityIds = $scopes['community_ids'];
        $buildingIds = $scopes['building_ids'];
        $serviceTypeIds = $scopes['service_type_ids'];

        return match (get_class($model)) {
            Community::class => in_array($model->id, $communityIds, true),

            Building::class => in_array($model->id, $buildingIds, true)
                || in_array($model->rf_community_id, $communityIds, true),

            Unit::class => in_array($model->rf_community_id, $communityIds, true)
                || in_array($model->rf_building_id, $buildingIds, true),

            Announcement::class => in_array($model->community_id, $communityIds, true)
                || in_array($model->building_id, $buildingIds, true),

            Request::class => in_array($model->community_id, $communityIds, true)
                || in_array($model->building_id, $buildingIds, true),

            Facility::class => in_array($model->community_id, $communityIds, true),

            FacilityBooking::class => static::facilityInScope($model->facility_id, $communityIds),

            Transaction::class => static::unitInScope($model->unit_id, $communityIds, $buildingIds),

            Payment::class => static::transactionInScope($model->transaction_id, $communityIds, $buildingIds),

            Lease::class => static::leaseInScope($model->id, $communityIds, $buildingIds),

            Resident::class => static::residentInScope($model->id, $communityIds, $buildingIds),

            Owner::class => static::ownerInScope($model->id, $communityIds, $buildingIds),

            Professional::class => true, // no direct path in schema; treat as unrestricted

            MarketplaceUnit::class => static::unitInScope($model->unit_id, $communityIds, $buildingIds),

            MarketplaceOffer::class => static::unitInScope($model->unit_id, $communityIds, $buildingIds),

            MarketplaceVisit::class => static::marketplaceVisitInScope($model->marketplace_unit_id, $communityIds, $buildingIds),

            default => false,
        };
    }

    private static function facilityInScope(int $facilityId, array $communityIds): bool
    {
        if (empty($communityIds)) {
            return false;
        }

        return \DB::table('rf_facilities')
            ->where('id', $facilityId)
            ->whereIn('community_id', $communityIds)
            ->exists();
    }

    private static function unitInScope(int $unitId, array $communityIds, array $buildingIds): bool
    {
        if (empty($communityIds) && empty($buildingIds)) {
            return true;
        }

        return \DB::table('rf_units')
            ->where('id', $unitId)
            ->where(function ($q) use ($communityIds, $buildingIds): void {
                if (! empty($communityIds)) {
                    $q->orWhereIn('rf_community_id', $communityIds);
                }
                if (! empty($buildingIds)) {
                    $q->orWhereIn('rf_building_id', $buildingIds);
                }
            })
            ->exists();
    }

    private static function transactionInScope(int $transactionId, array $communityIds, array $buildingIds): bool
    {
        if (empty($communityIds) && empty($buildingIds)) {
            return true;
        }

        $unitId = \DB::table('rf_transactions')->where('id', $transactionId)->value('unit_id');
        if ($unitId === null) {
            return false;
        }

        return static::unitInScope((int) $unitId, $communityIds, $buildingIds);
    }

    private static function leaseInScope(int $leaseId, array $communityIds, array $buildingIds): bool
    {
        if (empty($communityIds) && empty($buildingIds)) {
            return true;
        }

        return \DB::table('lease_units')
            ->join('rf_units', 'rf_units.id', '=', 'lease_units.unit_id')
            ->where('lease_units.lease_id', $leaseId)
            ->where(function ($q) use ($communityIds, $buildingIds): void {
                if (! empty($communityIds)) {
                    $q->orWhereIn('rf_units.rf_community_id', $communityIds);
                }
                if (! empty($buildingIds)) {
                    $q->orWhereIn('rf_units.rf_building_id', $buildingIds);
                }
            })
            ->exists();
    }

    private static function residentInScope(int $residentId, array $communityIds, array $buildingIds): bool
    {
        if (empty($communityIds) && empty($buildingIds)) {
            return true;
        }

        return \DB::table('rf_leases')
            ->join('lease_units', 'lease_units.lease_id', '=', 'rf_leases.id')
            ->join('rf_units', 'rf_units.id', '=', 'lease_units.unit_id')
            ->where('rf_leases.tenant_id', $residentId)
            ->where(function ($q) use ($communityIds, $buildingIds): void {
                if (! empty($communityIds)) {
                    $q->orWhereIn('rf_units.rf_community_id', $communityIds);
                }
                if (! empty($buildingIds)) {
                    $q->orWhereIn('rf_units.rf_building_id', $buildingIds);
                }
            })
            ->exists();
    }

    private static function ownerInScope(int $ownerId, array $communityIds, array $buildingIds): bool
    {
        if (empty($communityIds) && empty($buildingIds)) {
            return true;
        }

        return \DB::table('rf_units')
            ->where('owner_id', $ownerId)
            ->where(function ($q) use ($communityIds, $buildingIds): void {
                if (! empty($communityIds)) {
                    $q->orWhereIn('rf_community_id', $communityIds);
                }
                if (! empty($buildingIds)) {
                    $q->orWhereIn('rf_building_id', $buildingIds);
                }
            })
            ->exists();
    }

    private static function marketplaceVisitInScope(int $marketplaceUnitId, array $communityIds, array $buildingIds): bool
    {
        if (empty($communityIds) && empty($buildingIds)) {
            return true;
        }

        $unitId = \DB::table('rf_marketplace_units')->where('id', $marketplaceUnitId)->value('unit_id');
        if ($unitId === null) {
            return false;
        }

        return static::unitInScope((int) $unitId, $communityIds, $buildingIds);
    }
}
