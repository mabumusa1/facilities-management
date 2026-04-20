<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            ['name' => 'Central AC', 'name_ar' => 'تكييف مركزي', 'name_en' => 'Central AC'],
            ['name' => 'Built-in Wardrobes', 'name_ar' => 'خزائن مدمجة', 'name_en' => 'Built-in Wardrobes'],
            ['name' => 'Balcony', 'name_ar' => 'شرفة', 'name_en' => 'Balcony'],
            ['name' => 'Maid Room', 'name_ar' => 'غرفة خادمة', 'name_en' => 'Maid Room'],
            ['name' => 'Kitchen Appliances', 'name_ar' => 'أجهزة مطبخ', 'name_en' => 'Kitchen Appliances'],
            ['name' => 'Parking', 'name_ar' => 'مواقف', 'name_en' => 'Parking'],
            ['name' => 'Storage', 'name_ar' => 'مستودع', 'name_en' => 'Storage'],
            ['name' => 'Smart Home', 'name_ar' => 'منزل ذكي', 'name_en' => 'Smart Home'],
        ];

        foreach ($features as $feature) {
            Feature::query()->updateOrCreate(
                ['name_en' => $feature['name_en']],
                $feature,
            );
        }
    }
}
