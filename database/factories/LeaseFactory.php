<?php

namespace Database\Factories;

use App\Enums\RentalType;
use App\Enums\TenantType;
use App\Models\Lease;
use App\Models\Resident;
use App\Models\Setting;
use App\Models\Status;
use App\Models\UnitCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lease>
 */
class LeaseFactory extends Factory
{
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 year', 'now');
        $endDate = fake()->dateTimeBetween($startDate, '+2 years');

        return [
            'contract_number' => fake()->unique()->numerify('LC-######'),
            'tenant_id' => Resident::factory(),
            'status_id' => Status::factory()->state(['type' => 'lease']),
            'lease_unit_type_id' => UnitCategory::factory(),
            'rental_contract_type_id' => Setting::factory()->state(['type' => 'rental_contract_type']),
            'payment_schedule_id' => Setting::factory()->state(['type' => 'payment_schedule']),
            'created_by_id' => User::factory(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'handover_date' => $startDate,
            'tenant_type' => fake()->randomElement(TenantType::cases()),
            'rental_type' => fake()->randomElement(RentalType::cases()),
            'rental_total_amount' => fake()->randomFloat(2, 10000, 200000),
        ];
    }
}
