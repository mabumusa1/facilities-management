<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\District;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $districtsByCity = [
            'Dubai' => [
                ['name' => 'Downtown Dubai', 'name_ar' => 'وسط مدينة دبي'],
                ['name' => 'Dubai Marina', 'name_ar' => 'دبي مارينا'],
                ['name' => 'Palm Jumeirah', 'name_ar' => 'نخلة جميرا'],
                ['name' => 'Jumeirah Beach Residence', 'name_ar' => 'جميرا بيتش ريزيدنس'],
                ['name' => 'Business Bay', 'name_ar' => 'الخليج التجاري'],
                ['name' => 'DIFC', 'name_ar' => 'مركز دبي المالي العالمي'],
                ['name' => 'Jumeirah Village Circle', 'name_ar' => 'قرية جميرا سيركل'],
                ['name' => 'Dubai Hills Estate', 'name_ar' => 'دبي هيلز استيت'],
                ['name' => 'Arabian Ranches', 'name_ar' => 'المرابع العربية'],
                ['name' => 'Emirates Hills', 'name_ar' => 'تلال الإمارات'],
                ['name' => 'Al Barsha', 'name_ar' => 'البرشاء'],
                ['name' => 'Deira', 'name_ar' => 'ديرة'],
                ['name' => 'Bur Dubai', 'name_ar' => 'بر دبي'],
                ['name' => 'International City', 'name_ar' => 'المدينة العالمية'],
                ['name' => 'Discovery Gardens', 'name_ar' => 'ديسكفري جاردنز'],
            ],
            'Abu Dhabi' => [
                ['name' => 'Al Reem Island', 'name_ar' => 'جزيرة الريم'],
                ['name' => 'Yas Island', 'name_ar' => 'جزيرة ياس'],
                ['name' => 'Saadiyat Island', 'name_ar' => 'جزيرة السعديات'],
                ['name' => 'Al Raha Beach', 'name_ar' => 'شاطئ الراحة'],
                ['name' => 'Khalifa City', 'name_ar' => 'مدينة خليفة'],
                ['name' => 'Al Khalidiyah', 'name_ar' => 'الخالدية'],
                ['name' => 'Corniche', 'name_ar' => 'الكورنيش'],
                ['name' => 'Tourist Club Area', 'name_ar' => 'منطقة النادي السياحي'],
            ],
            'Sharjah' => [
                ['name' => 'Al Nahda', 'name_ar' => 'النهضة'],
                ['name' => 'Al Majaz', 'name_ar' => 'المجاز'],
                ['name' => 'Al Khan', 'name_ar' => 'الخان'],
                ['name' => 'Al Qasimia', 'name_ar' => 'القاسمية'],
                ['name' => 'Muwailih', 'name_ar' => 'مويلح'],
            ],
        ];

        foreach ($districtsByCity as $cityName => $districts) {
            $city = City::where('name', $cityName)->first();
            if (! $city) {
                continue;
            }

            foreach ($districts as $district) {
                District::updateOrCreate(
                    ['city_id' => $city->id, 'name' => $district['name']],
                    $district
                );
            }
        }
    }
}
