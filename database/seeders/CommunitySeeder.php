<?php

namespace Database\Seeders;

use App\Models\Community;
use Illuminate\Database\Seeder;

class CommunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $targetCount = 3;
        $missingCount = $targetCount - Community::query()->count();

        if ($missingCount <= 0) {
            return;
        }

        Community::factory()->count($missingCount)->create();
    }
}
