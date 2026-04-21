<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Community;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(CommunitySeeder::class);

        $targetCount = 5;
        $missingCount = $targetCount - Building::query()->count();

        if ($missingCount <= 0) {
            return;
        }

        $communities = Community::query()->get(['id', 'city_id', 'district_id']);

        if ($communities->isEmpty()) {
            return;
        }

        for ($index = 0; $index < $missingCount; $index++) {
            $community = $communities->random();

            Building::factory()->create([
                'rf_community_id' => $community->id,
                'city_id' => $community->city_id,
                'district_id' => $community->district_id,
            ]);
        }
    }
}
