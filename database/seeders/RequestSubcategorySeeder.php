<?php

namespace Database\Seeders;

use App\Models\RequestCategory;
use App\Models\RequestSubcategory;
use Illuminate\Database\Seeder;

class RequestSubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = RequestCategory::query()
            ->where('has_sub_categories', true)
            ->get(['id', 'name', 'name_en']);

        foreach ($categories as $category) {
            $existingCount = RequestSubcategory::query()
                ->where('category_id', $category->id)
                ->count();

            $neededCount = max(0, 2 - $existingCount);

            for ($index = 1; $index <= $neededCount; $index++) {
                $sequence = $existingCount + $index;
                $baseName = $category->name_en ?? $category->name;

                RequestSubcategory::query()->create([
                    'category_id' => $category->id,
                    'name' => trim($baseName.' '.$sequence),
                    'name_ar' => 'فرعي '.$sequence,
                    'name_en' => trim($baseName.' '.$sequence),
                    'status' => true,
                    'is_all_day' => true,
                ]);
            }
        }
    }
}
