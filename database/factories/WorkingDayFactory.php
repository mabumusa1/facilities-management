<?php

namespace Database\Factories;

use App\Models\RequestSubcategory;
use App\Models\WorkingDay;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkingDay>
 */
class WorkingDayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subcategory_id' => RequestSubcategory::factory(),
            'day' => $this->faker->randomElement(['sunday', 'monday', 'tuesday', 'wednesday', 'thursday']),
            'start' => '08:00',
            'end' => '17:00',
            'is_active' => true,
        ];
    }
}
