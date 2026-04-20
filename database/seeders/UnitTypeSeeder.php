<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            // Residential types (category_id: 2)
            ['id' => 17, 'name' => 'Apartment', 'name_ar' => 'شقة', 'name_en' => 'Apartment', 'icon' => 'unit_residential', 'category_id' => 2],
            ['id' => 18, 'name' => 'Penthouse', 'name_ar' => 'بنتهاوس', 'name_en' => 'Penthouse', 'icon' => 'unit_residential', 'category_id' => 2],
            ['id' => 19, 'name' => 'Duplex Apartment', 'name_ar' => 'شقة دوبلكس', 'name_en' => 'Duplex Apartment', 'icon' => 'unit_residential', 'category_id' => 2],
            ['id' => 20, 'name' => 'Duplex Villa', 'name_ar' => 'فيلا دوبلكس', 'name_en' => 'Duplex Villa', 'icon' => 'unit_residential', 'category_id' => 2],
            ['id' => 21, 'name' => 'Floor Apartment', 'name_ar' => 'دور', 'name_en' => 'Floor Apartment', 'icon' => 'unit_residential', 'category_id' => 2],
            ['id' => 22, 'name' => 'Villa', 'name_ar' => 'فيلا', 'name_en' => 'Villa', 'icon' => 'unit_residential', 'category_id' => 2],
            ['id' => 24, 'name' => 'Townhouse', 'name_ar' => 'تاون هاوس', 'name_en' => 'Townhouse', 'icon' => 'unit_residential', 'category_id' => 2],
            ['id' => 25, 'name' => 'Land', 'name_ar' => 'أرض', 'name_en' => 'Land', 'icon' => 'unit_residential', 'category_id' => 2],

            // Commercial types (category_id: 3)
            ['id' => 26, 'name' => 'Store', 'name_ar' => 'محل', 'name_en' => 'Store', 'icon' => 'unit_commercial', 'category_id' => 3],
            ['id' => 27, 'name' => 'F&B Outlet', 'name_ar' => 'مطعم /مقهى', 'name_en' => 'F&B Outlet', 'icon' => 'unit_commercial', 'category_id' => 3],
            ['id' => 28, 'name' => 'Warehouse', 'name_ar' => 'مستودع', 'name_en' => 'Warehouse', 'icon' => 'unit_commercial', 'category_id' => 3],
            ['id' => 29, 'name' => 'Storage', 'name_ar' => 'مخزن', 'name_en' => 'Storage', 'icon' => 'unit_commercial', 'category_id' => 3],
            ['id' => 30, 'name' => 'Office', 'name_ar' => 'مكتب', 'name_en' => 'Office', 'icon' => 'unit_commercial', 'category_id' => 3],
            ['id' => 31, 'name' => 'Land', 'name_ar' => 'أرض', 'name_en' => 'Land', 'icon' => 'unit_commercial', 'category_id' => 3],
            ['id' => 135, 'name' => 'Showroom', 'name_ar' => 'معرض', 'name_en' => 'Showroom', 'icon' => 'unit_commercial', 'category_id' => 3],
            ['id' => 136, 'name' => 'Kiosk', 'name_ar' => 'كشك', 'name_en' => 'Kiosk', 'icon' => 'unit_commercial', 'category_id' => 3],
            ['id' => 137, 'name' => 'Executive Office', 'name_ar' => 'مكتب تنفيذي', 'name_en' => 'Executive Office', 'icon' => 'unit_commercial', 'category_id' => 3],
            ['id' => 138, 'name' => 'Shared Office', 'name_ar' => 'مكتب مشترك', 'name_en' => 'Shared Office', 'icon' => 'unit_commercial', 'category_id' => 3],
            ['id' => 139, 'name' => 'Building', 'name_ar' => 'مبنى', 'name_en' => 'Building', 'icon' => 'unit_commercial', 'category_id' => 3],
            ['id' => 140, 'name' => 'Tower', 'name_ar' => 'برج', 'name_en' => 'Tower', 'icon' => 'unit_commercial', 'category_id' => 3],
        ];

        DB::table('rf_unit_types')->upsert(
            $types,
            ['id'],
            ['name', 'name_ar', 'name_en', 'icon', 'category_id'],
        );
    }
}
