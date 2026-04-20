<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadSourceSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            ['id' => 1, 'name' => 'Marketplace', 'name_ar' => 'السوق', 'name_en' => 'Marketplace'],
            ['id' => 2, 'name' => 'Manual Entry', 'name_ar' => 'إدخال يدوي', 'name_en' => 'Manual Entry'],
            ['id' => 3, 'name' => 'LinkedIn', 'name_ar' => 'لينكد إن', 'name_en' => 'LinkedIn'],
            ['id' => 4, 'name' => 'Facebook', 'name_ar' => 'فيسبوك', 'name_en' => 'Facebook'],
            ['id' => 5, 'name' => 'Instagram', 'name_ar' => 'إنستغرام', 'name_en' => 'Instagram'],
            ['id' => 6, 'name' => 'X', 'name_ar' => 'إكس', 'name_en' => 'X'],
            ['id' => 7, 'name' => 'TikTok', 'name_ar' => 'تيك توك', 'name_en' => 'TikTok'],
            ['id' => 8, 'name' => 'Snapchat', 'name_ar' => 'سناب شات', 'name_en' => 'Snapchat'],
            ['id' => 9, 'name' => 'WhatsApp', 'name_ar' => 'واتساب', 'name_en' => 'WhatsApp'],
            ['id' => 10, 'name' => 'Telegram', 'name_ar' => 'تيليجرام', 'name_en' => 'Telegram'],
            ['id' => 11, 'name' => 'Other', 'name_ar' => 'أخرى', 'name_en' => 'Other'],
        ];

        DB::table('rf_lead_sources')->upsert(
            $sources,
            ['id'],
            ['name', 'name_ar', 'name_en'],
        );
    }
}
