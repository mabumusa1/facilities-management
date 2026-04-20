<?php

namespace Database\Factories;

use App\Enums\LeaseEscalationType;
use App\Models\Lease;
use App\Models\LeaseEscalation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaseEscalation>
 */
class LeaseEscalationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lease_id' => Lease::factory(),
            'year' => fake()->numberBetween(1, 5),
            'type' => fake()->randomElement(LeaseEscalationType::cases()),
            'value' => fake()->randomFloat(2, 1, 20),
        ];
    }
}
