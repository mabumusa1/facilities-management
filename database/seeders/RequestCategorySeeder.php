<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RequestCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['id' => 1, 'name' => 'Unit Services', 'name_ar' => 'خدمات الوحدات', 'name_en' => 'Unit Services', 'description' => 'For unit-specific services', 'has_sub_categories' => true, 'status' => true],
            ['id' => 2, 'name' => 'Common Area Requests', 'name_ar' => 'طلبات المناطق المشتركة', 'name_en' => 'Common Area Requests', 'description' => 'For common area services', 'has_sub_categories' => true, 'status' => true],
            ['id' => 3, 'name' => 'Visitor Access Requests', 'name_ar' => 'طلبات تصاريح الزوار', 'name_en' => 'Visitor Access Requests', 'description' => 'For visitor access permit requests', 'has_sub_categories' => false, 'status' => true],
            ['id' => 4, 'name' => 'Manager Requests', 'name_ar' => 'طلبات المدير', 'name_en' => 'Manager Requests', 'description' => 'For contacting the community manager', 'has_sub_categories' => false, 'status' => true],
            ['id' => 5, 'name' => 'Facility Bookings', 'name_ar' => 'حجوزات المرافق', 'name_en' => 'Facility Bookings', 'description' => 'For facility booking requests', 'has_sub_categories' => false, 'status' => true],
        ];

        DB::table('rf_request_categories')->upsert(
            $categories,
            ['id'],
            ['name', 'name_ar', 'name_en', 'description', 'has_sub_categories', 'status'],
        );

        $subcategories = [
            // Unit Services subcategories (category_id: 1)
            ['id' => 1, 'category_id' => 1, 'name' => 'Maintenance', 'name_ar' => 'صيانة', 'name_en' => 'Maintenance', 'status' => true],
            ['id' => 2, 'category_id' => 1, 'name' => 'House Cleaning', 'name_ar' => 'تنظيف المنزل', 'name_en' => 'House Cleaning', 'status' => true],
            ['id' => 3, 'category_id' => 1, 'name' => 'Car Wash', 'name_ar' => 'غسيل السيارات', 'name_en' => 'Car Wash', 'status' => true],
            ['id' => 4, 'category_id' => 1, 'name' => 'Electrical Appliances', 'name_ar' => 'الأجهزة الكهربائية', 'name_en' => 'Electrical Appliances', 'status' => true],
            ['id' => 5, 'category_id' => 1, 'name' => 'Furniture Repair', 'name_ar' => 'إصلاح الأثاث', 'name_en' => 'Furniture Repair', 'status' => true],
            ['id' => 6, 'category_id' => 1, 'name' => 'Other Services', 'name_ar' => 'خدمات أخرى', 'name_en' => 'Other Services', 'status' => true],

            // Common Area subcategories (category_id: 2)
            ['id' => 7, 'category_id' => 2, 'name' => 'Security & Safety', 'name_ar' => 'الأمن و السلامة', 'name_en' => 'Security & Safety', 'status' => true],
            ['id' => 8, 'category_id' => 2, 'name' => 'Unit Issues', 'name_ar' => 'مشاكل الوحدات', 'name_en' => 'Unit Issues', 'status' => true],
            ['id' => 9, 'category_id' => 2, 'name' => 'Resident Issues', 'name_ar' => 'مشاكل السكان', 'name_en' => 'Resident Issues', 'status' => true],
            ['id' => 10, 'category_id' => 2, 'name' => 'Service Issues', 'name_ar' => 'مشاكل الخدمات', 'name_en' => 'Service Issues', 'status' => true],
            ['id' => 11, 'category_id' => 2, 'name' => 'Other Issues', 'name_ar' => 'مشاكل اخرى', 'name_en' => 'Other Issues', 'status' => true],
        ];

        DB::table('rf_request_subcategories')->upsert(
            $subcategories,
            ['id'],
            ['category_id', 'name', 'name_ar', 'name_en', 'status'],
        );
    }
}
