<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            ['name' => 'Swimming Pool', 'name_ar' => 'مسبح', 'name_en' => 'Swimming Pool'],
            ['name' => 'Gym', 'name_ar' => 'صالة رياضية', 'name_en' => 'Gym'],
            ['name' => 'Playground', 'name_ar' => 'ملعب', 'name_en' => 'Playground'],
            ['name' => 'Security', 'name_ar' => 'أمن', 'name_en' => 'Security'],
            ['name' => 'Parking', 'name_ar' => 'مواقف', 'name_en' => 'Parking'],
            ['name' => 'Garden', 'name_ar' => 'حديقة', 'name_en' => 'Garden'],
            ['name' => 'Concierge', 'name_ar' => 'خدمة استقبال', 'name_en' => 'Concierge'],
            ['name' => 'BBQ Area', 'name_ar' => 'منطقة شواء', 'name_en' => 'BBQ Area'],
        ];

        foreach ($amenities as $amenity) {
            Amenity::query()->updateOrCreate(
                ['name_en' => $amenity['name_en']],
                $amenity,
            );
        }
    }
}
