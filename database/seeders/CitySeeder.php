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
            ['id' => 1, 'name' => 'Riyadh', 'name_ar' => 'الرياض', 'name_en' => 'Riyadh'],
            ['id' => 2, 'name' => 'Dammam', 'name_ar' => 'الدمام', 'name_en' => 'Dammam'],
            ['id' => 3, 'name' => 'Al Khobar', 'name_ar' => 'الخبر', 'name_en' => 'Al Khobar'],
            ['id' => 4, 'name' => 'Jeddah', 'name_ar' => 'جدة', 'name_en' => 'Jeddah'],
            ['id' => 5, 'name' => 'Diriyah', 'name_ar' => 'الدرعية', 'name_en' => 'Diriyah'],
            ['id' => 6, 'name' => 'King Abdullah Economic City', 'name_ar' => 'مدينة الملك عبدالله الاقتصادية', 'name_en' => 'King Abdullah Economic City'],
            ['id' => 7, 'name' => 'Makkah', 'name_ar' => 'مكة المكرمة', 'name_en' => 'Makkah'],
            ['id' => 8, 'name' => 'Madinah', 'name_ar' => 'المدينة المنورة', 'name_en' => 'Madinah'],
            ['id' => 9, 'name' => 'Al Hofuf', 'name_ar' => 'الهفوف', 'name_en' => 'Al Hofuf'],
            ['id' => 10, 'name' => 'Al Mubarraz', 'name_ar' => 'المبرز', 'name_en' => 'Al Mubarraz'],
            ['id' => 11, 'name' => 'Taif', 'name_ar' => 'الطائف', 'name_en' => 'Taif'],
            ['id' => 12, 'name' => 'Kharj', 'name_ar' => 'الخرج', 'name_en' => 'Kharj'],
            ['id' => 13, 'name' => 'Unayzah', 'name_ar' => 'عنيزة', 'name_en' => 'Unayzah'],
            ['id' => 14, 'name' => 'Buraydah', 'name_ar' => 'بريدة', 'name_en' => 'Buraydah'],
            ['id' => 15, 'name' => 'Ar Rass', 'name_ar' => 'الرس', 'name_en' => 'Ar Rass'],
            ['id' => 16, 'name' => 'Al Bukairiyah', 'name_ar' => 'البكيرية', 'name_en' => 'Al Bukairiyah'],
            ['id' => 17, 'name' => 'Tabuk', 'name_ar' => 'تبوك', 'name_en' => 'Tabuk'],
            ['id' => 18, 'name' => 'Dhahran', 'name_ar' => 'الظهران', 'name_en' => 'Dhahran'],
            ['id' => 19, 'name' => 'Al Jubail Industrial City', 'name_ar' => 'الجبيل الصناعية', 'name_en' => 'Al Jubail Industrial City'],
            ['id' => 20, 'name' => 'Al Jubail', 'name_ar' => 'الجبيل', 'name_en' => 'Al Jubail'],
            ['id' => 21, 'name' => 'Qatif', 'name_ar' => 'القطيف', 'name_en' => 'Qatif'],
            ['id' => 22, 'name' => 'Hafr Al-Batin', 'name_ar' => 'حفر الباطن', 'name_en' => 'Hafr Al-Batin'],
            ['id' => 23, 'name' => 'Khamis Mushait', 'name_ar' => 'خميس مشيط', 'name_en' => 'Khamis Mushait'],
            ['id' => 24, 'name' => 'Zulfi', 'name_ar' => 'الزلفي', 'name_en' => 'Zulfi'],
            ['id' => 25, 'name' => 'Abha', 'name_ar' => 'أبها', 'name_en' => 'Abha'],
            ['id' => 26, 'name' => 'Hail', 'name_ar' => 'حائل', 'name_en' => 'Hail'],
        ];

        foreach ($cities as $city) {
            City::updateOrCreate(
                ['id' => $city['id']],
                $city + ['country_id' => $sa->id],
            );
        }
    }
}
