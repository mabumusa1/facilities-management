<?php

namespace Database\Seeders;

use App\Models\LeadSource;
use Illuminate\Database\Seeder;

class LeadSourceSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            ['name' => 'Website', 'name_ar' => 'الموقع الإلكتروني', 'name_en' => 'Website'],
            ['name' => 'Referral', 'name_ar' => 'إحالة', 'name_en' => 'Referral'],
            ['name' => 'Social Media', 'name_ar' => 'وسائل التواصل', 'name_en' => 'Social Media'],
            ['name' => 'Walk-in', 'name_ar' => 'زيارة مباشرة', 'name_en' => 'Walk-in'],
            ['name' => 'Agent', 'name_ar' => 'وكيل', 'name_en' => 'Agent'],
            ['name' => 'Other', 'name_ar' => 'أخرى', 'name_en' => 'Other'],
        ];

        foreach ($sources as $source) {
            LeadSource::query()->updateOrCreate(
                ['name_en' => $source['name_en']],
                $source,
            );
        }
    }
}
