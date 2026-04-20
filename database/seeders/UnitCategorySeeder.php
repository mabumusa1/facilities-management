<?php

namespace Database\Seeders;

use App\Models\UnitCategory;
use Illuminate\Database\Seeder;

class UnitCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Residential', 'name_ar' => 'سكني', 'name_en' => 'Residential', 'icon' => 'residential'],
            ['name' => 'Commercial', 'name_ar' => 'تجاري', 'name_en' => 'Commercial', 'icon' => 'commercial'],
            ['name' => 'Industrial', 'name_ar' => 'صناعي', 'name_en' => 'Industrial', 'icon' => 'industrial'],
            ['name' => 'Mixed Use', 'name_ar' => 'متعدد الاستخدامات', 'name_en' => 'Mixed Use', 'icon' => 'mixed'],
        ];

        foreach ($categories as $category) {
            UnitCategory::updateOrCreate(['name' => $category['name']], $category);
        }
    }
}
