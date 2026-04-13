<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'name' => 'UAE Dirham',
                'name_ar' => 'درهم إماراتي',
                'code' => 'AED',
                'symbol' => 'د.إ',
                'decimal_places' => 2,
            ],
            [
                'name' => 'Saudi Riyal',
                'name_ar' => 'ريال سعودي',
                'code' => 'SAR',
                'symbol' => 'ر.س',
                'decimal_places' => 2,
            ],
            [
                'name' => 'Qatari Riyal',
                'name_ar' => 'ريال قطري',
                'code' => 'QAR',
                'symbol' => 'ر.ق',
                'decimal_places' => 2,
            ],
            [
                'name' => 'Kuwaiti Dinar',
                'name_ar' => 'دينار كويتي',
                'code' => 'KWD',
                'symbol' => 'د.ك',
                'decimal_places' => 3,
            ],
            [
                'name' => 'Bahraini Dinar',
                'name_ar' => 'دينار بحريني',
                'code' => 'BHD',
                'symbol' => 'د.ب',
                'decimal_places' => 3,
            ],
            [
                'name' => 'Omani Rial',
                'name_ar' => 'ريال عماني',
                'code' => 'OMR',
                'symbol' => 'ر.ع',
                'decimal_places' => 3,
            ],
            [
                'name' => 'Egyptian Pound',
                'name_ar' => 'جنيه مصري',
                'code' => 'EGP',
                'symbol' => 'ج.م',
                'decimal_places' => 2,
            ],
            [
                'name' => 'Jordanian Dinar',
                'name_ar' => 'دينار أردني',
                'code' => 'JOD',
                'symbol' => 'د.أ',
                'decimal_places' => 3,
            ],
            [
                'name' => 'US Dollar',
                'name_ar' => 'دولار أمريكي',
                'code' => 'USD',
                'symbol' => '$',
                'decimal_places' => 2,
            ],
            [
                'name' => 'Euro',
                'name_ar' => 'يورو',
                'code' => 'EUR',
                'symbol' => '€',
                'decimal_places' => 2,
            ],
            [
                'name' => 'British Pound',
                'name_ar' => 'جنيه استرليني',
                'code' => 'GBP',
                'symbol' => '£',
                'decimal_places' => 2,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }
    }
}
