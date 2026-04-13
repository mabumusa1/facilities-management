<?php

namespace Database\Seeders;

use App\Models\UnitCategory;
use Illuminate\Database\Seeder;

class UnitCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Residential',
                'name_ar' => 'سكني',
                'description' => 'Residential properties for living purposes',
            ],
            [
                'name' => 'Commercial',
                'name_ar' => 'تجاري',
                'description' => 'Commercial properties for business purposes',
            ],
            [
                'name' => 'Industrial',
                'name_ar' => 'صناعي',
                'description' => 'Industrial properties for manufacturing and warehousing',
            ],
            [
                'name' => 'Mixed Use',
                'name_ar' => 'متعدد الاستخدامات',
                'description' => 'Properties combining residential and commercial use',
            ],
            [
                'name' => 'Land',
                'name_ar' => 'أرض',
                'description' => 'Vacant land parcels',
            ],
        ];

        foreach ($categories as $category) {
            UnitCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
