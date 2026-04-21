<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\Resident;
use App\Models\Status;
use Illuminate\Database\Seeder;

class FacilityBookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            FacilitySeeder::class,
            ResidentSeeder::class,
        ]);

        $targetCount = 5;
        $missingCount = $targetCount - FacilityBooking::query()->count();

        if ($missingCount <= 0) {
            return;
        }

        $facilityIds = Facility::query()->pluck('id');
        $residentIds = Resident::query()->pluck('id');
        $statusIds = Status::query()->where('type', 'facility_booking')->pluck('id');

        if ($facilityIds->isEmpty() || $residentIds->isEmpty() || $statusIds->isEmpty()) {
            return;
        }

        for ($index = 0; $index < $missingCount; $index++) {
            $startHour = random_int(8, 18);
            $endHour = min($startHour + 2, 22);

            FacilityBooking::factory()->create([
                'facility_id' => $facilityIds->random(),
                'status_id' => $statusIds->random(),
                'booker_type' => Resident::class,
                'booker_id' => $residentIds->random(),
                'booking_date' => now()->addDays(random_int(1, 21))->toDateString(),
                'start_time' => sprintf('%02d:00', $startHour),
                'end_time' => sprintf('%02d:00', $endHour),
                'number_of_guests' => random_int(1, 8),
            ]);
        }
    }
}
