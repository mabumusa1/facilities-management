<?php

namespace Database\Factories;

use App\Models\Lease;
use App\Models\LeaseAdditionalFee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaseAdditionalFee>
 */
class LeaseAdditionalFeeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lease_id' => Lease::factory(),
            'name' => fake()->randomElement(['Parking', 'Maintenance', 'Insurance', 'Service Charge']),
            'amount' => fake()->randomFloat(2, 500, 10000),
        ];
    }
}
