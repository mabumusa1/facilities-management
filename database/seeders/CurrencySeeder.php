<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            // GCC currencies
            ['name' => 'Saudi Riyal', 'code' => 'SAR', 'symbol' => '﷼'],
            ['name' => 'UAE Dirham', 'code' => 'AED', 'symbol' => 'د.إ'],
            ['name' => 'Kuwaiti Dinar', 'code' => 'KWD', 'symbol' => 'د.ك'],
            ['name' => 'Bahraini Dinar', 'code' => 'BHD', 'symbol' => 'د.ب'],
            ['name' => 'Qatari Riyal', 'code' => 'QAR', 'symbol' => 'ر.ق'],
            ['name' => 'Omani Rial', 'code' => 'OMR', 'symbol' => 'ر.ع'],

            // Regional currencies
            ['name' => 'Egyptian Pound', 'code' => 'EGP', 'symbol' => '£'],
            ['name' => 'Jordanian Dinar', 'code' => 'JOD', 'symbol' => 'د.ا'],
            ['name' => 'Iraqi Dinar', 'code' => 'IQD', 'symbol' => 'ع.د'],
            ['name' => 'Lebanese Pound', 'code' => 'LBP', 'symbol' => 'ل.ل'],
            ['name' => 'Turkish Lira', 'code' => 'TRY', 'symbol' => '₺'],

            // Global currencies
            ['name' => 'US Dollar', 'code' => 'USD', 'symbol' => '$'],
            ['name' => 'Euro', 'code' => 'EUR', 'symbol' => '€'],
            ['name' => 'British Pound', 'code' => 'GBP', 'symbol' => '£'],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(['code' => $currency['code']], $currency);
        }
    }
}
