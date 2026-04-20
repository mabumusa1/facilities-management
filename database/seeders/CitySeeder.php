<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $sa = Country::where('iso2', 'SA')->first();

        if (! $sa) {
            return;
        }

        $cities = [
            ['name' => 'Riyadh', 'name_ar' => 'الرياض', 'name_en' => 'Riyadh'],
            ['name' => 'Jeddah', 'name_ar' => 'جدة', 'name_en' => 'Jeddah'],
            ['name' => 'Mecca', 'name_ar' => 'مكة المكرمة', 'name_en' => 'Mecca'],
            ['name' => 'Medina', 'name_ar' => 'المدينة المنورة', 'name_en' => 'Medina'],
            ['name' => 'Dammam', 'name_ar' => 'الدمام', 'name_en' => 'Dammam'],
            ['name' => 'Khobar', 'name_ar' => 'الخبر', 'name_en' => 'Khobar'],
            ['name' => 'Tabuk', 'name_ar' => 'تبوك', 'name_en' => 'Tabuk'],
            ['name' => 'Abha', 'name_ar' => 'أبها', 'name_en' => 'Abha'],
        ];

        foreach ($cities as $city) {
            City::updateOrCreate(
                ['name' => $city['name'], 'country_id' => $sa->id],
                $city + ['country_id' => $sa->id],
            );
        }
    }
}
