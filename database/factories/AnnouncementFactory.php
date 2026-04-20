<?php

namespace Database\Factories;

use App\Models\Announcement;
use App\Models\Community;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'community_id' => Community::factory(),
            'title' => $this->faker->sentence(4),
            'body' => $this->faker->paragraphs(2, true),
            'priority' => $this->faker->randomElement(['low', 'normal', 'high', 'urgent']),
            'is_published' => $this->faker->boolean(),
            'published_at' => $this->faker->optional()->dateTimeBetween('-7 days', 'now'),
        ];
    }
}
