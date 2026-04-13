<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $citiesByCountry = [
            'AE' => [
                ['name' => 'Dubai', 'name_ar' => 'دبي'],
                ['name' => 'Abu Dhabi', 'name_ar' => 'أبوظبي'],
                ['name' => 'Sharjah', 'name_ar' => 'الشارقة'],
                ['name' => 'Ajman', 'name_ar' => 'عجمان'],
                ['name' => 'Ras Al Khaimah', 'name_ar' => 'رأس الخيمة'],
                ['name' => 'Fujairah', 'name_ar' => 'الفجيرة'],
                ['name' => 'Umm Al Quwain', 'name_ar' => 'أم القيوين'],
                ['name' => 'Al Ain', 'name_ar' => 'العين'],
            ],
            'SA' => [
                ['name' => 'Riyadh', 'name_ar' => 'الرياض'],
                ['name' => 'Jeddah', 'name_ar' => 'جدة'],
                ['name' => 'Mecca', 'name_ar' => 'مكة المكرمة'],
                ['name' => 'Medina', 'name_ar' => 'المدينة المنورة'],
                ['name' => 'Dammam', 'name_ar' => 'الدمام'],
                ['name' => 'Khobar', 'name_ar' => 'الخبر'],
            ],
            'QA' => [
                ['name' => 'Doha', 'name_ar' => 'الدوحة'],
                ['name' => 'Al Wakrah', 'name_ar' => 'الوكرة'],
                ['name' => 'Al Rayyan', 'name_ar' => 'الريان'],
                ['name' => 'Lusail', 'name_ar' => 'لوسيل'],
            ],
            'KW' => [
                ['name' => 'Kuwait City', 'name_ar' => 'مدينة الكويت'],
                ['name' => 'Hawalli', 'name_ar' => 'حولي'],
                ['name' => 'Salmiya', 'name_ar' => 'السالمية'],
            ],
            'BH' => [
                ['name' => 'Manama', 'name_ar' => 'المنامة'],
                ['name' => 'Muharraq', 'name_ar' => 'المحرق'],
                ['name' => 'Riffa', 'name_ar' => 'الرفاع'],
            ],
            'OM' => [
                ['name' => 'Muscat', 'name_ar' => 'مسقط'],
                ['name' => 'Salalah', 'name_ar' => 'صلالة'],
                ['name' => 'Sohar', 'name_ar' => 'صحار'],
            ],
        ];

        foreach ($citiesByCountry as $countryCode => $cities) {
            $country = Country::where('iso2', $countryCode)->first();
            if (! $country) {
                continue;
            }

            foreach ($cities as $city) {
                City::updateOrCreate(
                    ['country_id' => $country->id, 'name' => $city['name']],
                    $city
                );
            }
        }
    }
}
