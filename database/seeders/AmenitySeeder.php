<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenities = [
            // Building Amenities
            ['name' => 'Swimming Pool', 'name_ar' => 'مسبح', 'icon' => 'pool'],
            ['name' => 'Gym', 'name_ar' => 'صالة رياضية', 'icon' => 'dumbbell'],
            ['name' => '24/7 Security', 'name_ar' => 'أمن على مدار الساعة', 'icon' => 'shield'],
            ['name' => 'Concierge', 'name_ar' => 'خدمة الكونسيرج', 'icon' => 'bell-concierge'],
            ['name' => 'Parking', 'name_ar' => 'موقف سيارات', 'icon' => 'car'],
            ['name' => 'Covered Parking', 'name_ar' => 'موقف مغطى', 'icon' => 'warehouse'],
            ['name' => 'Children Play Area', 'name_ar' => 'منطقة لعب الأطفال', 'icon' => 'child'],
            ['name' => 'BBQ Area', 'name_ar' => 'منطقة شواء', 'icon' => 'fire'],
            ['name' => 'Garden', 'name_ar' => 'حديقة', 'icon' => 'tree'],
            ['name' => 'Rooftop Terrace', 'name_ar' => 'تراس على السطح', 'icon' => 'sun'],
            ['name' => 'Sauna', 'name_ar' => 'ساونا', 'icon' => 'hot-tub'],
            ['name' => 'Steam Room', 'name_ar' => 'غرفة بخار', 'icon' => 'cloud'],
            ['name' => 'Jacuzzi', 'name_ar' => 'جاكوزي', 'icon' => 'bath'],
            ['name' => 'Tennis Court', 'name_ar' => 'ملعب تنس', 'icon' => 'baseball'],
            ['name' => 'Basketball Court', 'name_ar' => 'ملعب كرة سلة', 'icon' => 'basketball'],
            ['name' => 'Squash Court', 'name_ar' => 'ملعب اسكواش', 'icon' => 'table-tennis'],
            ['name' => 'Jogging Track', 'name_ar' => 'مسار للركض', 'icon' => 'person-running'],
            ['name' => 'Business Center', 'name_ar' => 'مركز أعمال', 'icon' => 'briefcase'],
            ['name' => 'Meeting Room', 'name_ar' => 'غرفة اجتماعات', 'icon' => 'users'],
            ['name' => 'Lobby', 'name_ar' => 'ردهة', 'icon' => 'door-open'],
            ['name' => 'Elevator', 'name_ar' => 'مصعد', 'icon' => 'elevator'],
            ['name' => 'CCTV', 'name_ar' => 'كاميرات مراقبة', 'icon' => 'video'],
            ['name' => 'Intercom', 'name_ar' => 'انتركم', 'icon' => 'phone'],
            ['name' => 'Central AC', 'name_ar' => 'تكييف مركزي', 'icon' => 'snowflake'],
            ['name' => 'Backup Generator', 'name_ar' => 'مولد احتياطي', 'icon' => 'bolt'],
            ['name' => 'Fire Fighting System', 'name_ar' => 'نظام إطفاء الحريق', 'icon' => 'fire-extinguisher'],
            ['name' => 'Shared Kitchen', 'name_ar' => 'مطبخ مشترك', 'icon' => 'utensils'],
            ['name' => 'Laundry Room', 'name_ar' => 'غرفة غسيل', 'icon' => 'shirt'],
            ['name' => 'Storage Room', 'name_ar' => 'غرفة تخزين', 'icon' => 'box'],
            ['name' => 'Pet Friendly', 'name_ar' => 'مسموح بالحيوانات الأليفة', 'icon' => 'paw'],

            // Unit Amenities
            ['name' => 'Balcony', 'name_ar' => 'شرفة', 'icon' => 'door-open'],
            ['name' => 'Built-in Wardrobes', 'name_ar' => 'خزائن مدمجة', 'icon' => 'box-archive'],
            ['name' => 'Kitchen Appliances', 'name_ar' => 'أجهزة مطبخ', 'icon' => 'blender'],
            ['name' => 'Maid Room', 'name_ar' => 'غرفة خادمة', 'icon' => 'bed'],
            ['name' => 'Study Room', 'name_ar' => 'غرفة دراسة', 'icon' => 'book'],
            ['name' => 'Walk-in Closet', 'name_ar' => 'خزانة ملابس', 'icon' => 'shirt'],
            ['name' => 'Private Garden', 'name_ar' => 'حديقة خاصة', 'icon' => 'seedling'],
            ['name' => 'Private Pool', 'name_ar' => 'مسبح خاص', 'icon' => 'water-ladder'],
            ['name' => 'Sea View', 'name_ar' => 'إطلالة على البحر', 'icon' => 'water'],
            ['name' => 'City View', 'name_ar' => 'إطلالة على المدينة', 'icon' => 'city'],
        ];

        foreach ($amenities as $amenity) {
            Amenity::updateOrCreate(
                ['name' => $amenity['name']],
                $amenity
            );
        }
    }
}
