<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['iso2' => 'SA', 'iso3' => 'SAU', 'name' => 'Saudi Arabia', 'name_ar' => 'المملكة العربية السعودية', 'name_en' => 'Saudi Arabia', 'dial' => '966', 'currency' => 'SAR', 'capital' => 'Riyadh', 'continent' => 'AS', 'unicode' => '🇸🇦', 'excel' => 'SA (966)'],
            ['iso2' => 'AE', 'iso3' => 'ARE', 'name' => 'United Arab Emirates', 'name_ar' => 'الإمارات العربية المتحدة', 'name_en' => 'United Arab Emirates', 'dial' => '971', 'currency' => 'AED', 'capital' => 'Abu Dhabi', 'continent' => 'AS', 'unicode' => '🇦🇪', 'excel' => 'AE (971)'],
            ['iso2' => 'KW', 'iso3' => 'KWT', 'name' => 'Kuwait', 'name_ar' => 'الكويت', 'name_en' => 'Kuwait', 'dial' => '965', 'currency' => 'KWD', 'capital' => 'Kuwait City', 'continent' => 'AS', 'unicode' => '🇰🇼', 'excel' => 'KW (965)'],
            ['iso2' => 'BH', 'iso3' => 'BHR', 'name' => 'Bahrain', 'name_ar' => 'البحرين', 'name_en' => 'Bahrain', 'dial' => '973', 'currency' => 'BHD', 'capital' => 'Manama', 'continent' => 'AS', 'unicode' => '🇧🇭', 'excel' => 'BH (973)'],
            ['iso2' => 'QA', 'iso3' => 'QAT', 'name' => 'Qatar', 'name_ar' => 'قطر', 'name_en' => 'Qatar', 'dial' => '974', 'currency' => 'QAR', 'capital' => 'Doha', 'continent' => 'AS', 'unicode' => '🇶🇦', 'excel' => 'QA (974)'],
            ['iso2' => 'OM', 'iso3' => 'OMN', 'name' => 'Oman', 'name_ar' => 'عُمان', 'name_en' => 'Oman', 'dial' => '968', 'currency' => 'OMR', 'capital' => 'Muscat', 'continent' => 'AS', 'unicode' => '🇴🇲', 'excel' => 'OM (968)'],
            ['iso2' => 'EG', 'iso3' => 'EGY', 'name' => 'Egypt', 'name_ar' => 'مصر', 'name_en' => 'Egypt', 'dial' => '20', 'currency' => 'EGP', 'capital' => 'Cairo', 'continent' => 'AF', 'unicode' => '🇪🇬', 'excel' => 'EG (20)'],
            ['iso2' => 'JO', 'iso3' => 'JOR', 'name' => 'Jordan', 'name_ar' => 'الأردن', 'name_en' => 'Jordan', 'dial' => '962', 'currency' => 'JOD', 'capital' => 'Amman', 'continent' => 'AS', 'unicode' => '🇯🇴', 'excel' => 'JO (962)'],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(['iso2' => $country['iso2']], $country);
        }
    }
}
