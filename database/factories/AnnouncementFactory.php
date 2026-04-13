<?php

namespace Database\Factories;

use App\Models\Announcement;
use App\Models\Tenant;
use App\Models\User;
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
        $startDate = $this->faker->dateTimeBetween('-1 week', '+1 week');
        $endDate = $this->faker->dateTimeBetween($startDate, '+1 month');

        return [
            'tenant_id' => Tenant::factory(),
            'created_by' => User::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraphs(2, true),
            'start_date' => $startDate,
            'start_time' => $this->faker->time('H:i:s'),
            'end_date' => $endDate,
            'end_time' => $this->faker->time('H:i:s'),
            'is_visible' => true,
            'notify_user_types' => $this->faker->randomElements(['tenant', 'owner', 'all'], $this->faker->numberBetween(1, 3)),
            'community_ids' => null,
            'building_ids' => null,
            'priority' => $this->faker->randomElement(['low', 'normal', 'high', 'urgent']),
            'status' => $this->faker->randomElement(['draft', 'scheduled', 'active', 'expired', 'cancelled']),
        ];
    }

    /**
     * Indicate that the announcement is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'is_visible' => true,
            'start_date' => now()->subDay(),
            'end_date' => now()->addWeek(),
        ]);
    }

    /**
     * Indicate that the announcement is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Indicate that the announcement is scheduled.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'scheduled',
            'start_date' => now()->addWeek(),
            'end_date' => now()->addMonth(),
        ]);
    }

    /**
     * Indicate that the announcement has expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'start_date' => now()->subMonth(),
            'end_date' => now()->subWeek(),
        ]);
    }

    /**
     * Indicate that the announcement is high priority.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'high',
        ]);
    }

    /**
     * Indicate that the announcement is urgent.
     */
    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'urgent',
        ]);
    }
}
