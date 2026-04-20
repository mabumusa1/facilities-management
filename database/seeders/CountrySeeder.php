<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            // GCC countries first
            ['iso2' => 'SA', 'iso3' => 'SAU', 'name' => 'Saudi Arabia', 'name_ar' => 'المملكة العربية السعودية', 'name_en' => 'Saudi Arabia', 'dial' => '966', 'currency' => 'SAR', 'capital' => 'Riyadh', 'continent' => 'AS', 'unicode' => '🇸🇦', 'excel' => 'SA (966)'],
            ['iso2' => 'QA', 'iso3' => 'QAT', 'name' => 'Qatar', 'name_ar' => 'قطر', 'name_en' => 'Qatar', 'dial' => '974', 'currency' => 'QAR', 'capital' => 'Doha', 'continent' => 'AS', 'unicode' => '🇶🇦', 'excel' => 'QA (974)'],
            ['iso2' => 'KW', 'iso3' => 'KWT', 'name' => 'Kuwait', 'name_ar' => 'الكويت', 'name_en' => 'Kuwait', 'dial' => '965', 'currency' => 'KWD', 'capital' => 'Kuwait City', 'continent' => 'AS', 'unicode' => '🇰🇼', 'excel' => 'KW (965)'],
            ['iso2' => 'BH', 'iso3' => 'BHR', 'name' => 'Bahrain', 'name_ar' => 'البحرين', 'name_en' => 'Bahrain', 'dial' => '973', 'currency' => 'BHD', 'capital' => 'Manama', 'continent' => 'AS', 'unicode' => '🇧🇭', 'excel' => 'BH (973)'],
            ['iso2' => 'OM', 'iso3' => 'OMN', 'name' => 'Oman', 'name_ar' => 'عمان', 'name_en' => 'Oman', 'dial' => '968', 'currency' => 'OMR', 'capital' => 'Muscat', 'continent' => 'AS', 'unicode' => '🇴🇲', 'excel' => 'OM (968)'],
            ['iso2' => 'AE', 'iso3' => 'ARE', 'name' => 'United Arab Emirates', 'name_ar' => 'الإمارات العربية المتحدة', 'name_en' => 'United Arab Emirates', 'dial' => '971', 'currency' => 'AED', 'capital' => 'Abu Dhabi', 'continent' => 'AS', 'unicode' => '🇦🇪', 'excel' => 'AE (971)'],

            // Middle East & North Africa
            ['iso2' => 'AF', 'iso3' => 'AFG', 'name' => 'Afghanistan', 'name_ar' => 'أفغانستان', 'name_en' => 'Afghanistan', 'dial' => '93', 'currency' => 'AFN', 'capital' => 'Kabul', 'continent' => 'AS', 'unicode' => '🇦🇫', 'excel' => 'AF (93)'],
            ['iso2' => 'DZ', 'iso3' => 'DZA', 'name' => 'Algeria', 'name_ar' => 'الجزائر', 'name_en' => 'Algeria', 'dial' => '213', 'currency' => 'DZD', 'capital' => 'Algiers', 'continent' => 'AF', 'unicode' => '🇩🇿', 'excel' => 'DZ (213)'],
            ['iso2' => 'EG', 'iso3' => 'EGY', 'name' => 'Egypt', 'name_ar' => 'مصر', 'name_en' => 'Egypt', 'dial' => '20', 'currency' => 'EGP', 'capital' => 'Cairo', 'continent' => 'AF', 'unicode' => '🇪🇬', 'excel' => 'EG (20)'],
            ['iso2' => 'IQ', 'iso3' => 'IRQ', 'name' => 'Iraq', 'name_ar' => 'العراق', 'name_en' => 'Iraq', 'dial' => '964', 'currency' => 'IQD', 'capital' => 'Baghdad', 'continent' => 'AS', 'unicode' => '🇮🇶', 'excel' => 'IQ (964)'],
            ['iso2' => 'JO', 'iso3' => 'JOR', 'name' => 'Jordan', 'name_ar' => 'الأردن', 'name_en' => 'Jordan', 'dial' => '962', 'currency' => 'JOD', 'capital' => 'Amman', 'continent' => 'AS', 'unicode' => '🇯🇴', 'excel' => 'JO (962)'],
            ['iso2' => 'LB', 'iso3' => 'LBN', 'name' => 'Lebanon', 'name_ar' => 'لبنان', 'name_en' => 'Lebanon', 'dial' => '961', 'currency' => 'LBP', 'capital' => 'Beirut', 'continent' => 'AS', 'unicode' => '🇱🇧', 'excel' => 'LB (961)'],
            ['iso2' => 'LY', 'iso3' => 'LBY', 'name' => 'Libya', 'name_ar' => 'ليبيا', 'name_en' => 'Libya', 'dial' => '218', 'currency' => 'LYD', 'capital' => 'Tripolis', 'continent' => 'AF', 'unicode' => '🇱🇾', 'excel' => 'LY (218)'],
            ['iso2' => 'MA', 'iso3' => 'MAR', 'name' => 'Morocco', 'name_ar' => 'المغرب', 'name_en' => 'Morocco', 'dial' => '212', 'currency' => 'MAD', 'capital' => 'Rabat', 'continent' => 'AF', 'unicode' => '🇲🇦', 'excel' => 'MA (212)'],
            ['iso2' => 'PS', 'iso3' => 'PSE', 'name' => 'Palestine', 'name_ar' => 'فلسطين', 'name_en' => 'Palestine', 'dial' => '970', 'currency' => 'ILS', 'capital' => 'East Jerusalem', 'continent' => 'AS', 'unicode' => '🇵🇸', 'excel' => 'PS (970)'],
            ['iso2' => 'SD', 'iso3' => 'SDN', 'name' => 'Sudan', 'name_ar' => 'السودان', 'name_en' => 'Sudan', 'dial' => '249', 'currency' => 'SDG', 'capital' => 'Khartoum', 'continent' => 'AF', 'unicode' => '🇸🇩', 'excel' => 'SD (249)'],
            ['iso2' => 'SY', 'iso3' => 'SYR', 'name' => 'Syria', 'name_ar' => 'سوريا', 'name_en' => 'Syria', 'dial' => '963', 'currency' => 'SYP', 'capital' => 'Damascus', 'continent' => 'AS', 'unicode' => '🇸🇾', 'excel' => 'SY (963)'],
            ['iso2' => 'TN', 'iso3' => 'TUN', 'name' => 'Tunisia', 'name_ar' => 'تونس', 'name_en' => 'Tunisia', 'dial' => '216', 'currency' => 'TND', 'capital' => 'Tunis', 'continent' => 'AF', 'unicode' => '🇹🇳', 'excel' => 'TN (216)'],
            ['iso2' => 'TR', 'iso3' => 'TUR', 'name' => 'Turkey', 'name_ar' => 'تركيا', 'name_en' => 'Turkey', 'dial' => '90', 'currency' => 'TRY', 'capital' => 'Ankara', 'continent' => 'AS', 'unicode' => '🇹🇷', 'excel' => 'TR (90)'],
            ['iso2' => 'YE', 'iso3' => 'YEM', 'name' => 'Yemen', 'name_ar' => 'اليمن', 'name_en' => 'Yemen', 'dial' => '967', 'currency' => 'YER', 'capital' => 'Sanaa', 'continent' => 'AS', 'unicode' => '🇾🇪', 'excel' => 'YE (967)'],

            // Asia
            ['iso2' => 'AU', 'iso3' => 'AUS', 'name' => 'Australia', 'name_ar' => 'أستراليا', 'name_en' => 'Australia', 'dial' => '61', 'currency' => 'AUD', 'capital' => 'Canberra', 'continent' => 'OC', 'unicode' => '🇦🇺', 'excel' => 'AU (61)'],
            ['iso2' => 'BD', 'iso3' => 'BGD', 'name' => 'Bangladesh', 'name_ar' => 'بنجلاديش', 'name_en' => 'Bangladesh', 'dial' => '880', 'currency' => 'BDT', 'capital' => 'Dhaka', 'continent' => 'AS', 'unicode' => '🇧🇩', 'excel' => 'BD (880)'],
            ['iso2' => 'CN', 'iso3' => 'CHN', 'name' => 'China', 'name_ar' => 'الصين', 'name_en' => 'China', 'dial' => '86', 'currency' => 'CNY', 'capital' => 'Beijing', 'continent' => 'AS', 'unicode' => '🇨🇳', 'excel' => 'CN (86)'],
            ['iso2' => 'IN', 'iso3' => 'IND', 'name' => 'India', 'name_ar' => 'الهند', 'name_en' => 'India', 'dial' => '91', 'currency' => 'INR', 'capital' => 'New Delhi', 'continent' => 'AS', 'unicode' => '🇮🇳', 'excel' => 'IN (91)'],
            ['iso2' => 'ID', 'iso3' => 'IDN', 'name' => 'Indonesia', 'name_ar' => 'إندونيسيا', 'name_en' => 'Indonesia', 'dial' => '62', 'currency' => 'IDR', 'capital' => 'Jakarta', 'continent' => 'AS', 'unicode' => '🇮🇩', 'excel' => 'ID (62)'],
            ['iso2' => 'JP', 'iso3' => 'JPN', 'name' => 'Japan', 'name_ar' => 'اليابان', 'name_en' => 'Japan', 'dial' => '81', 'currency' => 'JPY', 'capital' => 'Tokyo', 'continent' => 'AS', 'unicode' => 'JP', 'excel' => 'JP (+81)'],
            ['iso2' => 'KR', 'iso3' => 'KOR', 'name' => 'South Korea', 'name_ar' => 'كوريا الجنوبية', 'name_en' => 'South Korea', 'dial' => '82', 'currency' => 'KRW', 'capital' => 'Seoul', 'continent' => 'AS', 'unicode' => 'KR', 'excel' => 'KR (+82)'],
            ['iso2' => 'MY', 'iso3' => 'MYS', 'name' => 'Malaysia', 'name_ar' => 'ماليزيا', 'name_en' => 'Malaysia', 'dial' => '60', 'currency' => 'MYR', 'capital' => 'Kuala Lumpur', 'continent' => 'AS', 'unicode' => 'MY', 'excel' => 'MY (+60)'],
            ['iso2' => 'PK', 'iso3' => 'PAK', 'name' => 'Pakistan', 'name_ar' => 'باكستان', 'name_en' => 'Pakistan', 'dial' => '92', 'currency' => 'PKR', 'capital' => 'Islamabad', 'continent' => 'AS', 'unicode' => '🇵🇰', 'excel' => 'PK (92)'],
            ['iso2' => 'PH', 'iso3' => 'PHL', 'name' => 'Philippines', 'name_ar' => 'الفلبين', 'name_en' => 'Philippines', 'dial' => '63', 'currency' => 'PHP', 'capital' => 'Manila', 'continent' => 'AS', 'unicode' => '🇵🇭', 'excel' => 'PH (63)'],

            // Europe
            ['iso2' => 'FR', 'iso3' => 'FRA', 'name' => 'France', 'name_ar' => 'فرنسا', 'name_en' => 'France', 'dial' => '33', 'currency' => 'EUR', 'capital' => 'Paris', 'continent' => 'EU', 'unicode' => 'FR', 'excel' => 'FR (+33)'],
            ['iso2' => 'DE', 'iso3' => 'DEU', 'name' => 'Germany', 'name_ar' => 'ألمانيا', 'name_en' => 'Germany', 'dial' => '49', 'currency' => 'EUR', 'capital' => 'Berlin', 'continent' => 'EU', 'unicode' => 'DE', 'excel' => 'DE (+49)'],
            ['iso2' => 'GR', 'iso3' => 'GRC', 'name' => 'Greece', 'name_ar' => 'اليونان', 'name_en' => 'Greece', 'dial' => '30', 'currency' => 'EUR', 'capital' => 'Athens', 'continent' => 'EU', 'unicode' => 'GR', 'excel' => 'GR (+30)'],
            ['iso2' => 'IE', 'iso3' => 'IRL', 'name' => 'Ireland', 'name_ar' => 'أيرلندا', 'name_en' => 'Ireland', 'dial' => '353', 'currency' => 'EUR', 'capital' => 'Dublin', 'continent' => 'EU', 'unicode' => 'IE', 'excel' => 'IE (+353)'],
            ['iso2' => 'IT', 'iso3' => 'ITA', 'name' => 'Italy', 'name_ar' => 'إيطاليا', 'name_en' => 'Italy', 'dial' => '39', 'currency' => 'EUR', 'capital' => 'Rome', 'continent' => 'EU', 'unicode' => 'IT', 'excel' => 'IT (+39)'],
            ['iso2' => 'PT', 'iso3' => 'PRT', 'name' => 'Portugal', 'name_ar' => 'البرتغال', 'name_en' => 'Portugal', 'dial' => '351', 'currency' => 'EUR', 'capital' => 'Lisbon', 'continent' => 'EU', 'unicode' => 'PT', 'excel' => 'PT (+351)'],
            ['iso2' => 'RO', 'iso3' => 'ROU', 'name' => 'Romania', 'name_ar' => 'رومانيا', 'name_en' => 'Romania', 'dial' => '40', 'currency' => 'RON', 'capital' => 'Bucharest', 'continent' => 'EU', 'unicode' => 'RO', 'excel' => 'RO (+40)'],
            ['iso2' => 'RU', 'iso3' => 'RUS', 'name' => 'Russia', 'name_ar' => 'روسيا', 'name_en' => 'Russia', 'dial' => '7', 'currency' => 'RUB', 'capital' => 'Moscow', 'continent' => 'EU', 'unicode' => 'RU', 'excel' => 'RU (+7)'],
            ['iso2' => 'GB', 'iso3' => 'GBR', 'name' => 'United Kingdom', 'name_ar' => 'المملكة المتحدة', 'name_en' => 'United Kingdom', 'dial' => '44', 'currency' => 'GBP', 'capital' => 'London', 'continent' => 'EU', 'unicode' => '🇬🇧', 'excel' => 'GB (44)'],

            // Americas
            ['iso2' => 'BR', 'iso3' => 'BRA', 'name' => 'Brazil', 'name_ar' => 'البرازيل', 'name_en' => 'Brazil', 'dial' => '55', 'currency' => 'BRL', 'capital' => 'Brasília', 'continent' => 'SA', 'unicode' => 'BR', 'excel' => 'BR (+55)'],
            ['iso2' => 'CA', 'iso3' => 'CAN', 'name' => 'Canada', 'name_ar' => 'كندا', 'name_en' => 'Canada', 'dial' => '1', 'currency' => 'CAD', 'capital' => 'Ottawa', 'continent' => 'NA', 'unicode' => '🇨🇦', 'excel' => 'CA (1)'],
            ['iso2' => 'MX', 'iso3' => 'MEX', 'name' => 'Mexico', 'name_ar' => 'المكسيك', 'name_en' => 'Mexico', 'dial' => '52', 'currency' => 'MXN', 'capital' => 'Mexico City', 'continent' => 'NA', 'unicode' => 'MX', 'excel' => 'MX (+52)'],
            ['iso2' => 'PE', 'iso3' => 'PER', 'name' => 'Peru', 'name_ar' => 'بيرو', 'name_en' => 'Peru', 'dial' => '51', 'currency' => 'PEN', 'capital' => 'Lima', 'continent' => 'SA', 'unicode' => 'PE', 'excel' => 'PE (+51)'],
            ['iso2' => 'US', 'iso3' => 'USA', 'name' => 'United States', 'name_ar' => 'الولايات المتحدة الأمريكية', 'name_en' => 'United States', 'dial' => '1', 'currency' => 'USD', 'capital' => 'Washington', 'continent' => 'NA', 'unicode' => '🇺🇸', 'excel' => 'US (1)'],

            // Africa
            ['iso2' => 'ZA', 'iso3' => 'ZAF', 'name' => 'South Africa', 'name_ar' => 'جنوب أفريقيا', 'name_en' => 'South Africa', 'dial' => '27', 'currency' => 'ZAR', 'capital' => 'Pretoria', 'continent' => 'AF', 'unicode' => 'ZA', 'excel' => 'ZA (+27)'],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(['iso2' => $country['iso2']], $country);
        }
    }
}
