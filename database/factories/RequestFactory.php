<?php

namespace Database\Factories;

use App\Models\Request;
use App\Models\RequestCategory;
use App\Models\RequestSubcategory;
use App\Models\Resident;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Request>
 */
class RequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => RequestCategory::factory(),
            'subcategory_id' => RequestSubcategory::factory(),
            'status_id' => Status::factory()->state(['type' => 'request']),
            'requester_type' => Resident::class,
            'requester_id' => Resident::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'preferred_date' => $this->faker->dateTimeBetween('now', '+30 days'),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
        ];
    }
}
