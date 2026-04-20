<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['id' => 2, 'name' => 'Residential', 'name_ar' => 'سكني', 'name_en' => 'Residential', 'icon' => 'residential'],
            ['id' => 3, 'name' => 'Commercial', 'name_ar' => 'تجاري', 'name_en' => 'Commercial', 'icon' => 'commercial'],
        ];

        DB::table('rf_unit_categories')->upsert(
            $categories,
            ['id'],
            ['name', 'name_ar', 'name_en', 'icon'],
        );
    }
}
