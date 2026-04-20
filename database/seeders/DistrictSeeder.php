<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\District;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $riyadh = City::where('name', 'Riyadh')->first();

        if (! $riyadh) {
            return;
        }

        $districts = [
            ['name' => 'Al Olaya', 'name_ar' => 'العليا', 'name_en' => 'Al Olaya'],
            ['name' => 'Al Malqa', 'name_ar' => 'الملقا', 'name_en' => 'Al Malqa'],
            ['name' => 'Al Nakheel', 'name_ar' => 'النخيل', 'name_en' => 'Al Nakheel'],
            ['name' => 'Al Yasmin', 'name_ar' => 'الياسمين', 'name_en' => 'Al Yasmin'],
            ['name' => 'Al Sahafa', 'name_ar' => 'الصحافة', 'name_en' => 'Al Sahafa'],
        ];

        foreach ($districts as $district) {
            District::updateOrCreate(
                ['name' => $district['name'], 'city_id' => $riyadh->id],
                $district + ['city_id' => $riyadh->id],
            );
        }
    }
}
