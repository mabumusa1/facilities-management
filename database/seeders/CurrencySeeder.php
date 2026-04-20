<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['name' => 'Saudi Riyal', 'code' => 'SAR', 'symbol' => '﷼'],
            ['name' => 'UAE Dirham', 'code' => 'AED', 'symbol' => 'د.إ'],
            ['name' => 'Kuwaiti Dinar', 'code' => 'KWD', 'symbol' => 'د.ك'],
            ['name' => 'Bahraini Dinar', 'code' => 'BHD', 'symbol' => 'د.ب'],
            ['name' => 'Qatari Riyal', 'code' => 'QAR', 'symbol' => 'ر.ق'],
            ['name' => 'Omani Rial', 'code' => 'OMR', 'symbol' => 'ر.ع'],
            ['name' => 'Egyptian Pound', 'code' => 'EGP', 'symbol' => '£'],
            ['name' => 'US Dollar', 'code' => 'USD', 'symbol' => '$'],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(['code' => $currency['code']], $currency);
        }
    }
}
