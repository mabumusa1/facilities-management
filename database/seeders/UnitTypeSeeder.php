<?php

namespace Database\Seeders;

use App\Models\UnitCategory;
use App\Models\UnitType;
use Illuminate\Database\Seeder;

class UnitTypeSeeder extends Seeder
{
    public function run(): void
    {
        $residential = UnitCategory::where('name', 'Residential')->first();
        $commercial = UnitCategory::where('name', 'Commercial')->first();
        $industrial = UnitCategory::where('name', 'Industrial')->first();

        if (! $residential || ! $commercial || ! $industrial) {
            return;
        }

        $types = [
            // Residential
            ['name' => 'Apartment', 'name_ar' => 'شقة', 'name_en' => 'Apartment', 'icon' => 'apartment', 'category_id' => $residential->id],
            ['name' => 'Villa', 'name_ar' => 'فيلا', 'name_en' => 'Villa', 'icon' => 'villa', 'category_id' => $residential->id],
            ['name' => 'Duplex', 'name_ar' => 'دوبلكس', 'name_en' => 'Duplex', 'icon' => 'duplex', 'category_id' => $residential->id],
            ['name' => 'Studio', 'name_ar' => 'استوديو', 'name_en' => 'Studio', 'icon' => 'studio', 'category_id' => $residential->id],
            ['name' => 'Penthouse', 'name_ar' => 'بنتهاوس', 'name_en' => 'Penthouse', 'icon' => 'penthouse', 'category_id' => $residential->id],
            ['name' => 'Townhouse', 'name_ar' => 'تاون هاوس', 'name_en' => 'Townhouse', 'icon' => 'townhouse', 'category_id' => $residential->id],

            // Commercial
            ['name' => 'Office', 'name_ar' => 'مكتب', 'name_en' => 'Office', 'icon' => 'office', 'category_id' => $commercial->id],
            ['name' => 'Shop', 'name_ar' => 'محل', 'name_en' => 'Shop', 'icon' => 'shop', 'category_id' => $commercial->id],
            ['name' => 'Showroom', 'name_ar' => 'معرض', 'name_en' => 'Showroom', 'icon' => 'showroom', 'category_id' => $commercial->id],

            // Industrial
            ['name' => 'Warehouse', 'name_ar' => 'مستودع', 'name_en' => 'Warehouse', 'icon' => 'warehouse', 'category_id' => $industrial->id],
            ['name' => 'Workshop', 'name_ar' => 'ورشة', 'name_en' => 'Workshop', 'icon' => 'workshop', 'category_id' => $industrial->id],
        ];

        foreach ($types as $type) {
            UnitType::updateOrCreate(
                ['name' => $type['name'], 'category_id' => $type['category_id']],
                $type,
            );
        }
    }
}
