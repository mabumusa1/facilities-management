<?php

namespace Database\Factories;

use App\Models\Community;
use App\Models\User;
use App\Models\VisitorInvitation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VisitorInvitation>
 */
class VisitorInvitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $expectedAt = $this->faker->dateTimeBetween('now', '+7 days');

        return [
            'community_id' => Community::factory(),
            'resident_id' => User::factory(),
            'visitor_name' => $this->faker->name(),
            'visitor_phone' => $this->faker->optional()->phoneNumber(),
            'visitor_purpose' => $this->faker->randomElement(['visit', 'delivery', 'service', 'other']),
            'expected_at' => $expectedAt,
            'valid_until' => (clone $expectedAt)->modify('+1 day'),
            'status' => 'pending',
            'notes' => $this->faker->optional()->sentence(),
            'qr_code_token' => bin2hex(random_bytes(16)),
            'qr_code_sent_via' => 'none',
        ];
    }

    /**
     * Mark the invitation as active.
     */
    public function active(): static
    {
        return $this->state(['status' => 'active']);
    }

    /**
     * Mark the invitation as used.
     */
    public function used(): static
    {
        return $this->state(['status' => 'used']);
    }

    /**
     * Mark the invitation as expired.
     */
    public function expired(): static
    {
        return $this->state([
            'status' => 'expired',
            'valid_until' => $this->faker->dateTimeBetween('-7 days', '-1 day'),
        ]);
    }

    /**
     * Mark the invitation as cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(['status' => 'cancelled']);
    }
}
