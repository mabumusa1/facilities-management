<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'name' => 'United Arab Emirates',
                'name_ar' => 'الإمارات العربية المتحدة',
                'iso2' => 'AE',
                'iso3' => 'ARE',
                'dial_code' => '+971',
                'currency_code' => 'AED',
                'capital' => 'Abu Dhabi',
                'continent' => 'AS',
                'flag_emoji' => '🇦🇪',
            ],
            [
                'name' => 'Saudi Arabia',
                'name_ar' => 'المملكة العربية السعودية',
                'iso2' => 'SA',
                'iso3' => 'SAU',
                'dial_code' => '+966',
                'currency_code' => 'SAR',
                'capital' => 'Riyadh',
                'continent' => 'AS',
                'flag_emoji' => '🇸🇦',
            ],
            [
                'name' => 'Qatar',
                'name_ar' => 'قطر',
                'iso2' => 'QA',
                'iso3' => 'QAT',
                'dial_code' => '+974',
                'currency_code' => 'QAR',
                'capital' => 'Doha',
                'continent' => 'AS',
                'flag_emoji' => '🇶🇦',
            ],
            [
                'name' => 'Kuwait',
                'name_ar' => 'الكويت',
                'iso2' => 'KW',
                'iso3' => 'KWT',
                'dial_code' => '+965',
                'currency_code' => 'KWD',
                'capital' => 'Kuwait City',
                'continent' => 'AS',
                'flag_emoji' => '🇰🇼',
            ],
            [
                'name' => 'Bahrain',
                'name_ar' => 'البحرين',
                'iso2' => 'BH',
                'iso3' => 'BHR',
                'dial_code' => '+973',
                'currency_code' => 'BHD',
                'capital' => 'Manama',
                'continent' => 'AS',
                'flag_emoji' => '🇧🇭',
            ],
            [
                'name' => 'Oman',
                'name_ar' => 'عمان',
                'iso2' => 'OM',
                'iso3' => 'OMN',
                'dial_code' => '+968',
                'currency_code' => 'OMR',
                'capital' => 'Muscat',
                'continent' => 'AS',
                'flag_emoji' => '🇴🇲',
            ],
            [
                'name' => 'Egypt',
                'name_ar' => 'مصر',
                'iso2' => 'EG',
                'iso3' => 'EGY',
                'dial_code' => '+20',
                'currency_code' => 'EGP',
                'capital' => 'Cairo',
                'continent' => 'AF',
                'flag_emoji' => '🇪🇬',
            ],
            [
                'name' => 'Jordan',
                'name_ar' => 'الأردن',
                'iso2' => 'JO',
                'iso3' => 'JOR',
                'dial_code' => '+962',
                'currency_code' => 'JOD',
                'capital' => 'Amman',
                'continent' => 'AS',
                'flag_emoji' => '🇯🇴',
            ],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['iso2' => $country['iso2']],
                $country
            );
        }
    }
}
