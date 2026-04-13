<?php

namespace Database\Seeders;

use App\Models\FacilityCategory;
use Illuminate\Database\Seeder;

class FacilityCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Sports & Fitness',
                'name_ar' => 'الرياضة واللياقة البدنية',
                'description' => 'Sports and fitness facilities',
                'icon' => 'dumbbell',
            ],
            [
                'name' => 'Recreation',
                'name_ar' => 'الترفيه',
                'description' => 'Recreational activities and entertainment',
                'icon' => 'gamepad',
            ],
            [
                'name' => 'Community',
                'name_ar' => 'المجتمع',
                'description' => 'Community gathering spaces',
                'icon' => 'users',
            ],
            [
                'name' => 'Business',
                'name_ar' => 'الأعمال',
                'description' => 'Business and meeting facilities',
                'icon' => 'briefcase',
            ],
            [
                'name' => 'Wellness & Spa',
                'name_ar' => 'العافية والسبا',
                'description' => 'Health and wellness facilities',
                'icon' => 'spa',
            ],
            [
                'name' => 'Children',
                'name_ar' => 'الأطفال',
                'description' => 'Children play areas and facilities',
                'icon' => 'baby',
            ],
            [
                'name' => 'Outdoor',
                'name_ar' => 'الخارجية',
                'description' => 'Outdoor spaces and gardens',
                'icon' => 'tree',
            ],
            [
                'name' => 'Parking',
                'name_ar' => 'مواقف السيارات',
                'description' => 'Parking facilities',
                'icon' => 'car',
            ],
        ];

        foreach ($categories as $category) {
            FacilityCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
