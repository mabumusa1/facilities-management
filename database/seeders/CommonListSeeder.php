<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommonListSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Cancellation reasons — Home services (IDs 1-9)
            ['id' => 1, 'name' => 'Incorrect request information', 'name_ar' => 'معلومات الطلب غير صحيحة', 'name_en' => 'Incorrect request information', 'type' => 'cancellation_home', 'priority' => 1],
            ['id' => 2, 'name' => "Cancelled at resident's request", 'name_ar' => 'تم إلغاءه بناءً على طلب الساكن', 'name_en' => "Cancelled at resident's request", 'type' => 'cancellation_home', 'priority' => 1],
            ['id' => 3, 'name' => 'Resident not available', 'name_ar' => 'الساكن غير متوفر', 'name_en' => 'Resident not available', 'type' => 'cancellation_home', 'priority' => 1],
            ['id' => 4, 'name' => 'Outside warranty scope', 'name_ar' => 'خارج نطاق الضمان', 'name_en' => 'Outside warranty scope', 'type' => 'cancellation_home', 'priority' => 1],
            ['id' => 5, 'name' => 'Other', 'name_ar' => 'أخرى', 'name_en' => 'Other', 'type' => 'cancellation_home', 'priority' => 1],
            ['id' => 6, 'name' => 'Not within my service scope', 'name_ar' => 'ليس ضمن نطاق خدمتي', 'name_en' => 'Not within my service scope', 'type' => 'cancellation_home', 'priority' => 1],
            ['id' => 7, 'name' => 'Not available', 'name_ar' => 'لست متاح', 'name_en' => 'Not available', 'type' => 'cancellation_home', 'priority' => 1],
            ['id' => 8, 'name' => 'Duplicate request', 'name_ar' => 'طلب مكرر', 'name_en' => 'Duplicate request', 'type' => 'cancellation_home', 'priority' => 1],
            ['id' => 9, 'name' => 'Other', 'name_ar' => 'أخرى', 'name_en' => 'Other', 'type' => 'cancellation_home', 'priority' => 1],

            // Business types (IDs 10-21)
            ['id' => 10, 'name' => 'Retail', 'name_ar' => 'تجزئة', 'name_en' => 'Retail', 'type' => 'business_type', 'priority' => 1],
            ['id' => 11, 'name' => 'Logistics and Storage', 'name_ar' => 'الخدمات اللوجستية والتخزين', 'name_en' => 'Logistics and Storage', 'type' => 'business_type', 'priority' => 1],
            ['id' => 12, 'name' => 'Food & Beverage', 'name_ar' => 'الأغذية والمشروبات (مطعم / مقهى)', 'name_en' => 'Food & Beverage (Restaurant/Cafe)', 'type' => 'business_type', 'priority' => 1],
            ['id' => 13, 'name' => 'Healthcare', 'name_ar' => 'الرعاية الصحية / العيادات / الصيدليات', 'name_en' => 'Healthcare/Clinics/Pharmacies', 'type' => 'business_type', 'priority' => 1],
            ['id' => 14, 'name' => 'Beauty & Personal Care', 'name_ar' => 'الجمال والعناية الشخصية (صالون / منتجع صحي)', 'name_en' => 'Beauty & Personal Care (Salon/Spa)', 'type' => 'business_type', 'priority' => 1],
            ['id' => 15, 'name' => 'Real Estate', 'name_ar' => 'العقارات وخدمات الممتلكات', 'name_en' => 'Real Estate & Property Services', 'type' => 'business_type', 'priority' => 1],
            ['id' => 16, 'name' => 'Fashion & Apparel', 'name_ar' => 'الأزياء والملابس', 'name_en' => 'Fashion & Apparel', 'type' => 'business_type', 'priority' => 1],
            ['id' => 17, 'name' => 'Professional Services', 'name_ar' => 'الخدمات المهنية (قانونية / استشارات / تسويق)', 'name_en' => 'Professional Services (Legal/Consulting/Marketing)', 'type' => 'business_type', 'priority' => 1],
            ['id' => 18, 'name' => 'Education & Training', 'name_ar' => 'مراكز التعليم والتدريب', 'name_en' => 'Education & Training Centers', 'type' => 'business_type', 'priority' => 1],
            ['id' => 19, 'name' => 'Maintenance & Repair', 'name_ar' => 'خدمات الصيانة والإصلاح (تكييف، سباكة، كهرباء)', 'name_en' => 'Maintenance & Repair Services', 'type' => 'business_type', 'priority' => 1],
            ['id' => 20, 'name' => 'Technology', 'name_ar' => 'شركة تقنية / برمجيات', 'name_en' => 'Technology/Software Company', 'type' => 'business_type', 'priority' => 1],
            ['id' => 21, 'name' => 'Other', 'name_ar' => 'أخرى', 'name_en' => 'Other', 'type' => 'business_type', 'priority' => 1],

            // Cancellation reasons — Common area (IDs 22-28)
            ['id' => 22, 'name' => 'Incorrect request information', 'name_ar' => 'معلومات الطلب غير صحيحة', 'name_en' => 'Incorrect request information', 'type' => 'cancellation_common', 'priority' => 1],
            ['id' => 23, 'name' => 'Duplicate request', 'name_ar' => 'طلب مكرر', 'name_en' => 'Duplicate request', 'type' => 'cancellation_common', 'priority' => 1],
            ['id' => 24, 'name' => 'Request outside scope', 'name_ar' => 'الطلب خارج نطاق خدمات المناطق المشتركة', 'name_en' => 'Request outside common area services scope', 'type' => 'cancellation_common', 'priority' => 1],
            ['id' => 25, 'name' => 'Issue already resolved', 'name_ar' => 'المشكلة محلولة مسبقًا', 'name_en' => 'Issue already resolved', 'type' => 'cancellation_common', 'priority' => 1],
            ['id' => 26, 'name' => 'Cancelled by admin', 'name_ar' => 'تم الإلغاء بناءً على قرار إداري', 'name_en' => 'Cancelled by administrative decision', 'type' => 'cancellation_common', 'priority' => 1],
            ['id' => 27, 'name' => 'Violates service policy', 'name_ar' => 'الطلب مخالف لسياسة الخدمة', 'name_en' => 'Request violates service policy', 'type' => 'cancellation_common', 'priority' => 1],
            ['id' => 28, 'name' => 'Other', 'name_ar' => 'أخرى', 'name_en' => 'Other', 'type' => 'cancellation_common', 'priority' => 1],
        ];

        DB::table('rf_common_lists')->upsert(
            $items,
            ['id'],
            ['name', 'name_ar', 'name_en', 'type', 'priority'],
        );
    }
}
