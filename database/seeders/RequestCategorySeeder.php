<?php

namespace Database\Seeders;

use App\Models\RequestCategory;
use Illuminate\Database\Seeder;

class RequestCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Maintenance', 'name_ar' => 'صيانة', 'name_en' => 'Maintenance', 'has_sub_categories' => true],
            ['name' => 'Cleaning', 'name_ar' => 'تنظيف', 'name_en' => 'Cleaning', 'has_sub_categories' => true],
            ['name' => 'Security', 'name_ar' => 'أمن', 'name_en' => 'Security', 'has_sub_categories' => false],
            ['name' => 'Parking', 'name_ar' => 'مواقف', 'name_en' => 'Parking', 'has_sub_categories' => false],
            ['name' => 'General', 'name_ar' => 'عام', 'name_en' => 'General', 'has_sub_categories' => false],
            ['name' => 'Pest Control', 'name_ar' => 'مكافحة حشرات', 'name_en' => 'Pest Control', 'has_sub_categories' => false],
            ['name' => 'Landscaping', 'name_ar' => 'تنسيق حدائق', 'name_en' => 'Landscaping', 'has_sub_categories' => false],
            ['name' => 'Move In/Out', 'name_ar' => 'نقل', 'name_en' => 'Move In/Out', 'has_sub_categories' => false],
        ];

        foreach ($categories as $category) {
            RequestCategory::query()->updateOrCreate(
                ['name_en' => $category['name_en']],
                $category,
            );
        }
    }
}
