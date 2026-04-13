<?php

namespace Database\Seeders;

use App\Models\UnitCategory;
use App\Models\UnitType;
use Illuminate\Database\Seeder;

class UnitTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $typesByCategory = [
            'Residential' => [
                ['name' => 'Studio', 'name_ar' => 'استوديو', 'description' => 'Single room apartment'],
                ['name' => 'Apartment', 'name_ar' => 'شقة', 'description' => 'Multi-room apartment'],
                ['name' => 'Villa', 'name_ar' => 'فيلا', 'description' => 'Standalone house'],
                ['name' => 'Townhouse', 'name_ar' => 'تاون هاوس', 'description' => 'Row house with shared walls'],
                ['name' => 'Penthouse', 'name_ar' => 'بنتهاوس', 'description' => 'Top floor luxury apartment'],
                ['name' => 'Duplex', 'name_ar' => 'دوبلكس', 'description' => 'Two-story apartment'],
                ['name' => 'Loft', 'name_ar' => 'لوفت', 'description' => 'Open-plan living space'],
            ],
            'Commercial' => [
                ['name' => 'Office', 'name_ar' => 'مكتب', 'description' => 'Office space'],
                ['name' => 'Retail Shop', 'name_ar' => 'محل تجاري', 'description' => 'Retail storefront'],
                ['name' => 'Showroom', 'name_ar' => 'صالة عرض', 'description' => 'Display space for products'],
                ['name' => 'Restaurant', 'name_ar' => 'مطعم', 'description' => 'Food service establishment'],
                ['name' => 'Clinic', 'name_ar' => 'عيادة', 'description' => 'Medical clinic space'],
            ],
            'Industrial' => [
                ['name' => 'Warehouse', 'name_ar' => 'مستودع', 'description' => 'Storage facility'],
                ['name' => 'Factory', 'name_ar' => 'مصنع', 'description' => 'Manufacturing facility'],
                ['name' => 'Workshop', 'name_ar' => 'ورشة', 'description' => 'Light industrial workshop'],
                ['name' => 'Labor Camp', 'name_ar' => 'سكن عمال', 'description' => 'Worker accommodation'],
            ],
            'Land' => [
                ['name' => 'Residential Plot', 'name_ar' => 'قطعة أرض سكنية', 'description' => 'Land zoned for residential'],
                ['name' => 'Commercial Plot', 'name_ar' => 'قطعة أرض تجارية', 'description' => 'Land zoned for commercial'],
                ['name' => 'Industrial Plot', 'name_ar' => 'قطعة أرض صناعية', 'description' => 'Land zoned for industrial'],
            ],
        ];

        foreach ($typesByCategory as $categoryName => $types) {
            $category = UnitCategory::where('name', $categoryName)->first();
            if (! $category) {
                continue;
            }

            foreach ($types as $type) {
                UnitType::updateOrCreate(
                    ['unit_category_id' => $category->id, 'name' => $type['name']],
                    $type
                );
            }
        }
    }
}
