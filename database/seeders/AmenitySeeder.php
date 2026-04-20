<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            // Community amenities from API (26 items)
            ['id' => 1, 'name' => 'Pet amenities', 'name_ar' => 'مناطق للحيوانات الأليفة', 'name_en' => 'Pet amenities'],
            ['id' => 2, 'name' => 'Parking', 'name_ar' => 'مواقف سيارات', 'name_en' => 'Parking'],
            ['id' => 3, 'name' => 'Co-working space', 'name_ar' => 'مساحة عمل مشتركة', 'name_en' => 'Co-working space'],
            ['id' => 4, 'name' => 'Swimming pool', 'name_ar' => 'مسبح', 'name_en' => 'Swimming pool'],
            ['id' => 5, 'name' => 'Gardens', 'name_ar' => 'حديقة', 'name_en' => 'Gardens'],
            ['id' => 6, 'name' => 'Playgrounds', 'name_ar' => 'ملاعب', 'name_en' => 'Playgrounds'],
            ['id' => 7, 'name' => 'Padel Arena', 'name_ar' => 'ملعب بادل', 'name_en' => 'Padel Arena'],
            ['id' => 8, 'name' => 'Gym', 'name_ar' => 'صالة العاب رياضية', 'name_en' => 'Gym'],
            ['id' => 9, 'name' => 'Movie theaters', 'name_ar' => 'سينما', 'name_en' => 'Movie theaters'],
            ['id' => 10, 'name' => 'Bike storage areas', 'name_ar' => 'مواقف للدراجات', 'name_en' => 'Bike storage areas'],
            ['id' => 11, 'name' => 'Laundry facilities', 'name_ar' => 'مرافق لغسيل الملابس', 'name_en' => 'Laundry facilities'],
            ['id' => 12, 'name' => 'Package lockers', 'name_ar' => 'خزائن الطرود', 'name_en' => 'Package lockers'],
            ['id' => 13, 'name' => 'Smoke Alarm', 'name_ar' => 'جهاز انذار الحريق', 'name_en' => 'Smoke Alarm'],
            ['id' => 14, 'name' => 'Cafe', 'name_ar' => 'مقهى', 'name_en' => 'Cafe'],
            ['id' => 15, 'name' => 'Mosque', 'name_ar' => 'مسجد', 'name_en' => 'Mosque'],
            ['id' => 16, 'name' => 'Lounges', 'name_ar' => 'مجالس', 'name_en' => 'Lounges'],
            ['id' => 17, 'name' => "Children's Nursery", 'name_ar' => 'حضانة أطفال', 'name_en' => "Children's Nursery"],
            ['id' => 18, 'name' => 'Library', 'name_ar' => 'مكتبة', 'name_en' => 'Library'],
            ['id' => 19, 'name' => 'Salon', 'name_ar' => 'صالون', 'name_en' => 'Salon'],
            ['id' => 20, 'name' => 'Surveillance cameras', 'name_ar' => 'كاميرات مراقبة', 'name_en' => 'Surveillance cameras'],
            ['id' => 21, 'name' => 'Air conditioned corridors', 'name_ar' => 'ممرات مكيفة', 'name_en' => 'Air conditioned corridors'],
            ['id' => 22, 'name' => 'Boys School', 'name_ar' => 'مدارس للبنين', 'name_en' => 'Boys School'],
            ['id' => 23, 'name' => 'Girls School', 'name_ar' => 'مدارس للبنات', 'name_en' => 'Girls School'],
            ['id' => 24, 'name' => 'Hospital', 'name_ar' => 'مستشفى', 'name_en' => 'Hospital'],
            ['id' => 25, 'name' => 'Shopping Center', 'name_ar' => 'مركز تسوق', 'name_en' => 'Shopping Center'],
            ['id' => 26, 'name' => 'Health Center', 'name_ar' => 'مركز صحي', 'name_en' => 'Health Center'],
        ];

        foreach ($amenities as $amenity) {
            Amenity::query()->updateOrCreate(
                ['id' => $amenity['id']],
                $amenity,
            );
        }
    }
}
