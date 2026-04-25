<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Fit-out statuses (IDs 1-3)
            ['id' => 1, 'name' => 'Shell & Core', 'name_ar' => 'عظم', 'name_en' => 'Shell & Core', 'type' => 'fit_out_status', 'subtype' => null, 'parent_id' => null, 'is_active' => true, 'is_default' => false],
            ['id' => 2, 'name' => 'Fitted-out', 'name_ar' => 'مكتمل', 'name_en' => 'Fitted-out', 'type' => 'fit_out_status', 'subtype' => null, 'parent_id' => null, 'is_active' => true, 'is_default' => false],
            ['id' => 3, 'name' => 'Not Applicable', 'name_ar' => 'لا يطبق', 'name_en' => 'Not Applicable', 'type' => 'fit_out_status', 'subtype' => null, 'parent_id' => null, 'is_active' => true, 'is_default' => false],

            // Payment schedules — standalone schedules for yearly contracts (IDs 4-7)
            ['id' => 4, 'name' => 'Monthly', 'name_ar' => 'شهري', 'name_en' => 'Monthly', 'type' => 'payment_schedule', 'subtype' => null, 'parent_id' => null, 'is_active' => true, 'is_default' => false],
            ['id' => 5, 'name' => 'Quarterly', 'name_ar' => 'ربع سنوي', 'name_en' => 'Quarterly', 'type' => 'payment_schedule', 'subtype' => null, 'parent_id' => null, 'is_active' => true, 'is_default' => false],
            ['id' => 6, 'name' => 'Semi-Annual', 'name_ar' => 'نصف سنوي', 'name_en' => 'Semi-Annual', 'type' => 'payment_schedule', 'subtype' => null, 'parent_id' => null, 'is_active' => true, 'is_default' => false],
            ['id' => 7, 'name' => 'Annual', 'name_ar' => 'سنوي', 'name_en' => 'Annual', 'type' => 'payment_schedule', 'subtype' => null, 'parent_id' => 13, 'is_active' => true, 'is_default' => false],

            // Calculation basis (IDs 8-9)
            ['id' => 8, 'name' => 'Fixed Yearly Amount', 'name_ar' => 'مبلغ سنوي ثابت', 'name_en' => 'Fixed Yearly Amount', 'type' => 'calculation_basis', 'subtype' => null, 'parent_id' => null, 'is_active' => true, 'is_default' => false],
            ['id' => 9, 'name' => 'Percentage of Annual Rent', 'name_ar' => 'نسبة من الإيجار السنوي', 'name_en' => 'Percentage of Annual Rent', 'type' => 'calculation_basis', 'subtype' => null, 'parent_id' => null, 'is_active' => true, 'is_default' => false],

            // Payment frequency (IDs 10-11)
            ['id' => 10, 'name' => 'Paid annually as one payment', 'name_ar' => 'يدفع سنويا كدفعة واحدة', 'name_en' => 'Paid annually as one payment', 'type' => 'payment_frequency', 'subtype' => null, 'parent_id' => null, 'is_active' => true, 'is_default' => false],
            ['id' => 11, 'name' => 'Paid with rental payments', 'name_ar' => 'يدفع مع دفعات الإيجار', 'name_en' => 'Paid with rental payments', 'type' => 'payment_frequency', 'subtype' => null, 'parent_id' => null, 'is_active' => true, 'is_default' => false],

            // Rental contract types (IDs 13-15)
            ['id' => 13, 'name' => 'Yearly Rental', 'name_ar' => 'ايجار سنوي', 'name_en' => 'Yearly Rental', 'type' => 'rental_contract_type', 'subtype' => null, 'parent_id' => null, 'is_active' => true, 'is_default' => false],
            ['id' => 14, 'name' => 'Monthly Rental', 'name_ar' => 'ايجار شهري', 'name_en' => 'Monthly Rental', 'type' => 'rental_contract_type', 'subtype' => null, 'parent_id' => null, 'is_active' => true, 'is_default' => false],
            ['id' => 15, 'name' => 'Daily Rental', 'name_ar' => 'ايجار يومي', 'name_en' => 'Daily Rental', 'type' => 'rental_contract_type', 'subtype' => null, 'parent_id' => null, 'is_active' => true, 'is_default' => false],

            // Payment schedules — child schedules for monthly/daily contracts (IDs 16-18)
            ['id' => 16, 'name' => 'Monthly Payment', 'name_ar' => 'دفع شهري', 'name_en' => 'Monthly Payment', 'type' => 'payment_schedule', 'subtype' => null, 'parent_id' => 14, 'is_active' => true, 'is_default' => false],
            ['id' => 17, 'name' => 'Upfront Payment', 'name_ar' => 'دفع مقدم', 'name_en' => 'Upfront Payment', 'type' => 'payment_schedule', 'subtype' => null, 'parent_id' => 14, 'is_active' => true, 'is_default' => false],
            ['id' => 18, 'name' => 'Upfront Payment (Daily)', 'name_ar' => 'دفع مقدم', 'name_en' => 'Upfront Payment (Daily)', 'type' => 'payment_schedule', 'subtype' => null, 'parent_id' => 15, 'is_active' => true, 'is_default' => false],

            // Transaction categories — income (IDs 19-21)
            ['id' => 19, 'name' => 'Rent', 'name_ar' => 'إيجار', 'name_en' => 'Rent', 'type' => 'transaction_category', 'subtype' => 'income', 'parent_id' => null, 'is_active' => true, 'is_default' => true],
            ['id' => 20, 'name' => 'Late Fee', 'name_ar' => 'غرامة تأخير', 'name_en' => 'Late Fee', 'type' => 'transaction_category', 'subtype' => 'income', 'parent_id' => null, 'is_active' => true, 'is_default' => true],
            ['id' => 21, 'name' => 'Service Fee', 'name_ar' => 'رسوم خدمة', 'name_en' => 'Service Fee', 'type' => 'transaction_category', 'subtype' => 'income', 'parent_id' => null, 'is_active' => true, 'is_default' => true],

            // Transaction categories — expense (IDs 22-24)
            ['id' => 22, 'name' => 'Maintenance', 'name_ar' => 'صيانة', 'name_en' => 'Maintenance', 'type' => 'transaction_category', 'subtype' => 'expense', 'parent_id' => null, 'is_active' => true, 'is_default' => true],
            ['id' => 23, 'name' => 'Utility', 'name_ar' => 'مرافق', 'name_en' => 'Utility', 'type' => 'transaction_category', 'subtype' => 'expense', 'parent_id' => null, 'is_active' => true, 'is_default' => true],
            ['id' => 24, 'name' => 'Repairs', 'name_ar' => 'إصلاحات', 'name_en' => 'Repairs', 'type' => 'transaction_category', 'subtype' => 'expense', 'parent_id' => null, 'is_active' => true, 'is_default' => true],
        ];

        // Insert parent types first so parent_id FK is satisfied
        $parentRows = array_filter($settings, fn (array $s): bool => $s['parent_id'] === null);
        $childRows = array_filter($settings, fn (array $s): bool => $s['parent_id'] !== null);

        DB::table('rf_settings')->upsert(array_values($parentRows), ['id'], ['name', 'name_ar', 'name_en', 'type', 'subtype', 'parent_id', 'is_active', 'is_default']);
        DB::table('rf_settings')->upsert(array_values($childRows), ['id'], ['name', 'name_ar', 'name_en', 'type', 'subtype', 'parent_id', 'is_active', 'is_default']);
    }
}
