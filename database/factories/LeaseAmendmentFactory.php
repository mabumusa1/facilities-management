<?php

namespace Database\Factories;

use App\Models\Lease;
use App\Models\LeaseAmendment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaseAmendment>
 */
class LeaseAmendmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lease_id' => Lease::factory(),
            'amended_by' => User::factory(),
            'reason' => fake()->sentence(),
            'changes' => [
                'end_date' => [
                    'from' => fake()->dateTimeBetween('+1 year', '+2 years')->format('Y-m-d'),
                    'to' => fake()->dateTimeBetween('+2 years', '+3 years')->format('Y-m-d'),
                ],
            ],
            'addendum_media_id' => null,
            'amendment_number' => 1,
        ];
    }
}
