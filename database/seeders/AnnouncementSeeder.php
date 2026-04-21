<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Building;
use App\Models\Community;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            CommunitySeeder::class,
            BuildingSeeder::class,
        ]);

        $targetCount = 3;
        $missingCount = $targetCount - Announcement::query()->count();

        if ($missingCount <= 0) {
            return;
        }

        $communities = Community::query()->get(['id']);
        $buildings = Building::query()->get(['id', 'rf_community_id']);

        if ($communities->isEmpty()) {
            return;
        }

        for ($index = 0; $index < $missingCount; $index++) {
            $community = $communities->random();
            $communityBuildings = $buildings->where('rf_community_id', $community->id);
            $buildingId = $communityBuildings->isNotEmpty() ? $communityBuildings->random()->id : null;
            $isPublished = random_int(0, 1) === 1;

            Announcement::factory()->create([
                'community_id' => $community->id,
                'building_id' => $buildingId,
                'status' => $isPublished,
                'published_at' => $isPublished ? now()->subDays(random_int(1, 14)) : null,
            ]);
        }
    }
}
