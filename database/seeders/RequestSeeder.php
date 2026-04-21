<?php

namespace Database\Seeders;

use App\Models\Professional;
use App\Models\Request as ServiceRequest;
use App\Models\RequestSubcategory;
use App\Models\Resident;
use App\Models\Status;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class RequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            UnitSeeder::class,
            ResidentSeeder::class,
            ProfessionalSeeder::class,
            RequestSubcategorySeeder::class,
        ]);

        $targetCount = 8;
        $missingCount = $targetCount - ServiceRequest::query()->count();

        if ($missingCount <= 0) {
            return;
        }

        $subcategories = RequestSubcategory::query()->get(['id', 'category_id']);
        $statusIds = Status::query()->where('type', 'request')->pluck('id');
        $units = Unit::query()->get(['id', 'rf_community_id', 'rf_building_id']);
        $residentIds = Resident::query()->pluck('id');
        $professionalIds = Professional::query()->pluck('id');
        $priorities = ['low', 'medium', 'high', 'urgent'];

        if ($subcategories->isEmpty() || $statusIds->isEmpty() || $units->isEmpty() || $residentIds->isEmpty()) {
            return;
        }

        for ($index = 0; $index < $missingCount; $index++) {
            $subcategory = $subcategories->random();
            $unit = $units->random();

            ServiceRequest::factory()->create([
                'category_id' => $subcategory->category_id,
                'subcategory_id' => $subcategory->id,
                'status_id' => $statusIds->random(),
                'requester_type' => Resident::class,
                'requester_id' => $residentIds->random(),
                'unit_id' => $unit->id,
                'community_id' => $unit->rf_community_id,
                'building_id' => $unit->rf_building_id,
                'professional_id' => $professionalIds->isNotEmpty() && random_int(0, 1) === 1 ? $professionalIds->random() : null,
                'request_code' => sprintf('REQ-%05d', ServiceRequest::query()->count() + $index + 1),
                'priority' => $priorities[array_rand($priorities)],
            ]);
        }
    }
}
