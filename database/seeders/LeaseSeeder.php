<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Lease;
use App\Models\Resident;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Unit;
use App\Models\UnitCategory;
use Illuminate\Database\Seeder;

class LeaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            UnitSeeder::class,
            ResidentSeeder::class,
            AdminSeeder::class,
        ]);

        $targetCount = 5;
        $missingCount = $targetCount - Lease::query()->count();

        if ($missingCount <= 0) {
            return;
        }

        $residentIds = Resident::query()->pluck('id');
        $adminIds = Admin::query()->pluck('id');
        $statusIds = Status::query()->where('type', 'lease')->pluck('id');
        $unitIds = Unit::query()->pluck('id');
        $unitCategoryIds = UnitCategory::query()->pluck('id');
        $rentalContractTypeIds = Setting::query()->where('type', 'rental_contract_type')->pluck('id');
        $paymentScheduleIds = Setting::query()->where('type', 'payment_schedule')->pluck('id');

        if (
            $residentIds->isEmpty()
            || $adminIds->isEmpty()
            || $statusIds->isEmpty()
            || $unitIds->isEmpty()
            || $unitCategoryIds->isEmpty()
            || $rentalContractTypeIds->isEmpty()
            || $paymentScheduleIds->isEmpty()
        ) {
            return;
        }

        for ($index = 0; $index < $missingCount; $index++) {
            $startDate = now()->subMonths(random_int(1, 18))->startOfDay();
            $endDate = (clone $startDate)->addYear();

            $lease = Lease::factory()->create([
                'tenant_id' => $residentIds->random(),
                'status_id' => $statusIds->random(),
                'lease_unit_type_id' => $unitCategoryIds->random(),
                'rental_contract_type_id' => $rentalContractTypeIds->random(),
                'payment_schedule_id' => $paymentScheduleIds->random(),
                'created_by_id' => $adminIds->random(),
                'deal_owner_id' => $adminIds->random(),
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'handover_date' => $startDate->toDateString(),
                'tenant_type' => random_int(0, 1) === 1 ? 'individual' : 'company',
                'rental_type' => random_int(0, 1) === 1 ? 'total' : 'detailed',
                'rental_total_amount' => random_int(80000, 220000),
                'security_deposit_amount' => random_int(5000, 20000),
            ]);

            $lease->units()->syncWithoutDetaching([
                $unitIds->random() => [
                    'rental_annual_type' => 'annual',
                    'annual_rental_amount' => $lease->rental_total_amount,
                    'net_area' => random_int(60, 220),
                    'meter_cost' => random_int(400, 1200),
                ],
            ]);
        }
    }
}
