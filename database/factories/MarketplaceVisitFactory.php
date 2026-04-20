<?php

namespace Database\Factories;

use App\Models\MarketplaceUnit;
use App\Models\MarketplaceVisit;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MarketplaceVisit>
 */
class MarketplaceVisitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'marketplace_unit_id' => MarketplaceUnit::factory(),
            'status_id' => Status::factory()->state(['type' => 'property_visit']),
            'visitor_name' => $this->faker->name(),
            'visitor_phone' => $this->faker->phoneNumber(),
            'scheduled_at' => $this->faker->dateTimeBetween('now', '+14 days'),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
