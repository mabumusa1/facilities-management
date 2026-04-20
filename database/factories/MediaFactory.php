<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => fake()->imageUrl(),
            'name' => fake()->word(),
            'notes' => fake()->optional()->sentence(),
            'mediable_type' => 'App\\Models\\Country',
            'mediable_id' => Country::factory(),
            'collection' => fake()->randomElement(['photos', 'documents', 'floor_plans']),
        ];
    }
}
