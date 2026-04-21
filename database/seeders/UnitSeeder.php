<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Owner;
use App\Models\Resident;
use App\Models\Status;
use App\Models\Unit;
use App\Models\UnitType;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            BuildingSeeder::class,
            OwnerSeeder::class,
            ResidentSeeder::class,
        ]);

        $targetCount = 15;
        $missingCount = $targetCount - Unit::query()->count();

        if ($missingCount <= 0) {
            return;
        }

        $buildings = Building::query()->get(['id', 'rf_community_id']);
        $unitTypes = UnitType::query()->get(['id', 'category_id']);
        $statusIds = Status::query()->where('type', 'unit')->pluck('id');

        if ($buildings->isEmpty() || $unitTypes->isEmpty() || $statusIds->isEmpty()) {
            return;
        }

        $ownerIds = Owner::query()->pluck('id');
        $residentIds = Resident::query()->pluck('id');

        for ($index = 0; $index < $missingCount; $index++) {
            $building = $buildings->random();
            $unitType = $unitTypes->random();

            Unit::factory()->create([
                'rf_community_id' => $building->rf_community_id,
                'rf_building_id' => $building->id,
                'category_id' => $unitType->category_id,
                'type_id' => $unitType->id,
                'status_id' => $statusIds->random(),
                'owner_id' => $ownerIds->isNotEmpty() && random_int(0, 1) === 1 ? $ownerIds->random() : null,
                'tenant_id' => $residentIds->isNotEmpty() && random_int(0, 2) === 0 ? $residentIds->random() : null,
                'is_market_place' => random_int(0, 1) === 1,
                'is_buy' => random_int(0, 1) === 1,
                'is_off_plan_sale' => random_int(0, 1) === 1,
            ]);
        }

        // Ensure seeded owners are assigned to at least one unit each.
        $ownersWithoutUnits = Owner::query()
            ->doesntHave('units')
            ->pluck('id');

        if ($ownersWithoutUnits->isEmpty()) {
            return;
        }

        $unitsWithoutOwner = Unit::query()
            ->whereNull('owner_id')
            ->orderBy('id')
            ->take($ownersWithoutUnits->count())
            ->get(['id']);

        foreach ($ownersWithoutUnits as $position => $ownerId) {
            $unit = $unitsWithoutOwner->get($position);

            if ($unit === null) {
                break;
            }

            Unit::query()
                ->whereKey($unit->id)
                ->update(['owner_id' => $ownerId]);
        }
    }
}
