<?php

namespace Database\Factories;

use App\Models\Community;
use App\Models\User;
use App\Models\VisitorInvitation;
use App\Models\VisitorLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VisitorLog>
 */
class VisitorLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invitation_id' => null,
            'community_id' => Community::factory(),
            'visitor_name' => $this->faker->name(),
            'visitor_phone' => $this->faker->optional()->phoneNumber(),
            'purpose' => $this->faker->randomElement(['visit', 'delivery', 'service', 'other']),
            'gate_officer_id' => User::factory(),
            'entry_at' => $this->faker->dateTimeBetween('-1 hour', 'now'),
            'exit_at' => null,
            'id_verified' => false,
            'photo_path' => null,
        ];
    }

    /**
     * Associate a pre-scheduled invitation (not a walk-in).
     */
    public function withInvitation(): static
    {
        return $this->state(fn (array $attributes) => [
            'invitation_id' => VisitorInvitation::factory()->state([
                'community_id' => $attributes['community_id'],
            ]),
        ]);
    }

    /**
     * Mark the visitor as having exited.
     */
    public function withExit(): static
    {
        return $this->state(['exit_at' => $this->faker->dateTimeBetween('now', '+1 hour')]);
    }

    /**
     * Mark the visitor's ID as verified.
     */
    public function idVerified(): static
    {
        return $this->state(['id_verified' => true]);
    }
}
