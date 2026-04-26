<?php

namespace Database\Seeders;

use App\Models\Community;
use App\Models\Facility;
use App\Models\FacilityAvailabilityRule;
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

        // Ensure a sample Gym with Mon–Sat availability rules is seeded first
        $gymExists = Facility::query()->where('type', 'gym')->exists();

        if (! $gymExists) {
            $gym = Facility::factory()->create([
                'category_id' => $categoryIds->random(),
                'community_id' => $communityIds->random(),
                'name' => 'Gym',
                'name_en' => 'Gym',
                'name_ar' => 'صالة رياضية',
                'type' => 'gym',
                'is_active' => true,
                'requires_booking' => true,
                'pricing_mode' => 'free',
            ]);

            // Mon–Sat (1–6), 06:00–22:00
            for ($day = 1; $day <= 6; $day++) {
                FacilityAvailabilityRule::factory()->create([
                    'facility_id' => $gym->id,
                    'day_of_week' => $day,
                    'open_time' => '06:00',
                    'close_time' => '22:00',
                    'slot_duration_minutes' => 60,
                    'max_concurrent_bookings' => 1,
                    'is_active' => true,
                ]);
            }

            $missingCount--;
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
