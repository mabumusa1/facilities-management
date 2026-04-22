<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\District;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $enPath = database_path('seeders/raw/en/districts.json');
        $arPath = database_path('seeders/raw/ar/districts.json');

        if (! file_exists($enPath) || ! file_exists($arPath)) {
            $this->command?->warn('District JSON files not found, seeding sample data only.');
            $this->seedSample();

            return;
        }

        // Load city_id mapping if available (captured via fetch-district-mapping.js)
        $mappingPath = database_path('seeders/raw/district-city-mapping.json');
        /** @var array<int, int> $cityMapping */
        $cityMapping = file_exists($mappingPath)
            ? json_decode((string) file_get_contents($mappingPath), true)
            : [];

        $defaultCityId = City::where('name_en', 'Riyadh')->value('id') ?? 1;

        if ($cityMapping === []) {
            $this->command?->warn('District-city mapping not found. Defaulting all districts to Riyadh. Run fetch-district-mapping.js to capture the mapping.');
        }

        /** @var array{data: list<array{id: int, name: string}>} $enData */
        $enData = json_decode((string) file_get_contents($enPath), true);
        /** @var array{data: list<array{id: int, name: string}>} $arData */
        $arData = json_decode((string) file_get_contents($arPath), true);

        $arMap = collect($arData['data'])->keyBy('id');

        $rows = [];
        $now = now();

        foreach ($enData['data'] as $district) {
            $arName = $arMap->get($district['id'])['name'] ?? $district['name'];

            $rows[] = [
                'id' => $district['id'],
                'city_id' => $cityMapping[$district['id']] ?? $defaultCityId,
                'name' => $district['name'],
                'name_en' => $district['name'],
                'name_ar' => $arName,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Batch upsert in chunks to avoid memory issues
        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('districts')->upsert(
                $chunk,
                ['id'],
                ['city_id', 'name', 'name_en', 'name_ar', 'updated_at'],
            );
        }

        $this->command?->info('Seeded '.count($rows).' districts from JSON data.');
    }

    private function seedSample(): void
    {
        $riyadh = City::where('name', 'Riyadh')->first();

        if (! $riyadh) {
            return;
        }

        $districts = [
            ['name' => 'Al Olaya', 'name_ar' => 'العليا', 'name_en' => 'Al Olaya'],
            ['name' => 'Al Malqa', 'name_ar' => 'الملقا', 'name_en' => 'Al Malqa'],
            ['name' => 'Al Nakheel', 'name_ar' => 'النخيل', 'name_en' => 'Al Nakheel'],
            ['name' => 'Al Yasmin', 'name_ar' => 'الياسمين', 'name_en' => 'Al Yasmin'],
            ['name' => 'Al Sahafa', 'name_ar' => 'الصحافة', 'name_en' => 'Al Sahafa'],
        ];

        foreach ($districts as $district) {
            District::updateOrCreate(
                ['name' => $district['name'], 'city_id' => $riyadh->id],
                $district + ['city_id' => $riyadh->id],
            );
        }
    }
}
