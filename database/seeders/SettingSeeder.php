<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Rental contract types
            ['name' => 'New Contract', 'name_ar' => 'عقد جديد', 'name_en' => 'New Contract', 'type' => 'rental_contract_type'],
            ['name' => 'Renewal Contract', 'name_ar' => 'عقد تجديد', 'name_en' => 'Renewal Contract', 'type' => 'rental_contract_type'],
            ['name' => 'Sub Lease Contract', 'name_ar' => 'عقد إيجار فرعي', 'name_en' => 'Sub Lease Contract', 'type' => 'rental_contract_type'],

            // Payment schedules
            ['name' => 'Monthly', 'name_ar' => 'شهري', 'name_en' => 'Monthly', 'type' => 'payment_schedule'],
            ['name' => 'Quarterly', 'name_ar' => 'ربع سنوي', 'name_en' => 'Quarterly', 'type' => 'payment_schedule'],
            ['name' => 'Semi-Annual', 'name_ar' => 'نصف سنوي', 'name_en' => 'Semi-Annual', 'type' => 'payment_schedule'],
            ['name' => 'Annual', 'name_ar' => 'سنوي', 'name_en' => 'Annual', 'type' => 'payment_schedule'],

            // Lease settings
            ['name' => 'Auto Renewal', 'name_ar' => 'تجديد تلقائي', 'name_en' => 'Auto Renewal', 'type' => 'lease_setting'],
            ['name' => 'Grace Period', 'name_ar' => 'فترة سماح', 'name_en' => 'Grace Period', 'type' => 'lease_setting'],

            // Invoice settings
            ['name' => 'VAT Enabled', 'name_ar' => 'ضريبة القيمة المضافة مفعلة', 'name_en' => 'VAT Enabled', 'type' => 'invoice_setting'],
            ['name' => 'Auto Generate Invoice', 'name_ar' => 'إنشاء فاتورة تلقائي', 'name_en' => 'Auto Generate Invoice', 'type' => 'invoice_setting'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['name' => $setting['name'], 'type' => $setting['type']],
                $setting,
            );
        }
    }
}
