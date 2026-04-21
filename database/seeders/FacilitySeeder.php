<?php

namespace Database\Seeders;

use App\Models\Community;
use App\Models\Facility;
use App\Models\FacilityCategory;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(CommunitySeeder::class);

        $targetCount = 3;
        $missingCount = $targetCount - Facility::query()->count();

        if ($missingCount <= 0) {
            return;
        }

        $categoryIds = FacilityCategory::query()->pluck('id');
        $communityIds = Community::query()->pluck('id');

        if ($categoryIds->isEmpty() || $communityIds->isEmpty()) {
            return;
        }

        for ($index = 0; $index < $missingCount; $index++) {
            Facility::factory()->create([
                'category_id' => $categoryIds->random(),
                'community_id' => $communityIds->random(),
                'is_active' => random_int(0, 1) === 1,
                'requires_approval' => random_int(0, 1) === 1,
            ]);
        }
    }
}
