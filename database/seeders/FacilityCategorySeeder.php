<?php

namespace Database\Seeders;

use App\Models\FacilityCategory;
use Illuminate\Database\Seeder;

class FacilityCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Sports', 'name_ar' => 'رياضة', 'name_en' => 'Sports'],
            ['name' => 'Swimming Pool', 'name_ar' => 'مسبح', 'name_en' => 'Swimming Pool'],
            ['name' => 'Meeting Room', 'name_ar' => 'غرفة اجتماعات', 'name_en' => 'Meeting Room'],
            ['name' => 'Event Hall', 'name_ar' => 'قاعة مناسبات', 'name_en' => 'Event Hall'],
            ['name' => 'Gym', 'name_ar' => 'صالة رياضية', 'name_en' => 'Gym'],
            ['name' => 'Playground', 'name_ar' => 'ملعب', 'name_en' => 'Playground'],
        ];

        foreach ($categories as $category) {
            FacilityCategory::query()->updateOrCreate(
                ['name_en' => $category['name_en']],
                $category,
            );
        }
    }
}
