<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            // Residential unit features
            ['id' => 55, 'name' => 'Smart Access Control', 'name_ar' => 'دخول ذكي', 'name_en' => 'Smart Access Control', 'type' => 'residential'],
            ['id' => 56, 'name' => 'Parking', 'name_ar' => 'مواقف سيارات', 'name_en' => 'Parking', 'type' => 'residential'],
            ['id' => 58, 'name' => 'Outdoor Garden', 'name_ar' => 'حديقة خارجية', 'name_en' => 'Outdoor Garden', 'type' => 'residential'],
            ['id' => 59, 'name' => 'Balcony', 'name_ar' => 'شرفة', 'name_en' => 'Balcony', 'type' => 'residential'],
            ['id' => 60, 'name' => 'Smoke Alarm', 'name_ar' => 'جهاز إنذار الحريق', 'name_en' => 'Smoke Alarm', 'type' => 'residential'],
            ['id' => 61, 'name' => 'Kitchen', 'name_ar' => 'مطبخ', 'name_en' => 'Kitchen', 'type' => 'residential'],
            ['id' => 62, 'name' => 'Washing Machine', 'name_ar' => 'غسالة', 'name_en' => 'Washing Machine', 'type' => 'residential'],
            ['id' => 63, 'name' => 'Wifi', 'name_ar' => 'واي فاي', 'name_en' => 'Wifi', 'type' => 'residential'],
            ['id' => 64, 'name' => 'Accessible for People of Determination', 'name_ar' => 'مناسب لذوي الاحتياجات الخاصة', 'name_en' => 'Accessible for People of Determination', 'type' => 'residential'],
            ['id' => 65, 'name' => 'Double Glazed Windows', 'name_ar' => 'نوافذ بزجاج مزدوج', 'name_en' => 'Double Glazed Windows', 'type' => 'residential'],
            ['id' => 66, 'name' => 'Elevator', 'name_ar' => 'مصعد', 'name_en' => 'Elevator', 'type' => 'residential'],
            ['id' => 67, 'name' => 'Smart Home', 'name_ar' => 'منزل ذكي', 'name_en' => 'Smart Home', 'type' => 'residential'],
            ['id' => 83, 'name' => 'Outdoor space', 'name_ar' => 'مساحة خارجية', 'name_en' => 'Outdoor space', 'type' => 'residential'],

            // Commercial unit features
            ['id' => 57, 'name' => 'Co-Working Space', 'name_ar' => 'مساحات عمل مشتركة', 'name_en' => 'Co-Working Space', 'type' => 'commercial'],
            ['id' => 68, 'name' => 'Accessible for People of Determination', 'name_ar' => 'مناسب لذوي الاحتياجات الخاصة', 'name_en' => 'Accessible for People of Determination', 'type' => 'commercial'],
            ['id' => 69, 'name' => 'Alarm Systems', 'name_ar' => 'أنظمة إنذار', 'name_en' => 'Alarm Systems', 'type' => 'commercial'],
            ['id' => 70, 'name' => 'Breakout Areas', 'name_ar' => 'مناطق استراحة', 'name_en' => 'Breakout Areas', 'type' => 'commercial'],
            ['id' => 71, 'name' => 'Conference Room', 'name_ar' => 'قاعة اجتماعات', 'name_en' => 'Conference Room', 'type' => 'commercial'],
            ['id' => 72, 'name' => 'Elevator', 'name_ar' => 'مصعد', 'name_en' => 'Elevator', 'type' => 'commercial'],
            ['id' => 73, 'name' => 'Energy-Efficient Systems', 'name_ar' => 'أنظمة موفرة للطاقة', 'name_en' => 'Energy-Efficient Systems', 'type' => 'commercial'],
            ['id' => 74, 'name' => 'High-Speed Internet', 'name_ar' => 'إنترنت عالي السرعة', 'name_en' => 'High-Speed Internet', 'type' => 'commercial'],
            ['id' => 75, 'name' => 'Meeting Rooms', 'name_ar' => 'غرف اجتماعات', 'name_en' => 'Meeting Rooms', 'type' => 'commercial'],
            ['id' => 76, 'name' => 'Natural Lighting', 'name_ar' => 'إضاءة طبيعية', 'name_en' => 'Natural Lighting', 'type' => 'commercial'],
            ['id' => 77, 'name' => 'Parking', 'name_ar' => 'مواقف سيارات', 'name_en' => 'Parking', 'type' => 'commercial'],
            ['id' => 78, 'name' => 'Power Backup System', 'name_ar' => 'نظام احتياطي للطاقة', 'name_en' => 'Power Backup System', 'type' => 'commercial'],
            ['id' => 79, 'name' => 'Private Offices', 'name_ar' => 'مكاتب خاصة', 'name_en' => 'Private Offices', 'type' => 'commercial'],
            ['id' => 80, 'name' => 'Smart Access Control', 'name_ar' => 'دخول ذكي', 'name_en' => 'Smart Access Control', 'type' => 'commercial'],
            ['id' => 81, 'name' => 'Soundproof Rooms', 'name_ar' => 'غرف عازلة للصوت', 'name_en' => 'Soundproof Rooms', 'type' => 'commercial'],
            ['id' => 82, 'name' => 'Storage Space', 'name_ar' => 'مساحة تخزين', 'name_en' => 'Storage Space', 'type' => 'commercial'],
            ['id' => 84, 'name' => '24/7 Security', 'name_ar' => 'أمن على مدار الساعة', 'name_en' => '24/7 Security', 'type' => 'commercial'],
        ];

        foreach ($features as $feature) {
            Feature::query()->updateOrCreate(
                ['id' => $feature['id']],
                $feature,
            );
        }
    }
}
