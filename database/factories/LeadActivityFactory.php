<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeadActivity>
 */
class LeadActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lead_id' => Lead::factory(),
            'user_id' => User::factory(),
            'type' => LeadActivity::TYPE_NOTE,
            'data' => ['note' => $this->faker->sentence()],
        ];
    }

    public function note(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => LeadActivity::TYPE_NOTE,
            'data' => ['note' => $this->faker->sentence()],
        ]);
    }

    public function statusChange(string $from = 'New', string $to = 'Contacted'): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => LeadActivity::TYPE_STATUS_CHANGE,
            'data' => ['from' => $from, 'to' => $to],
        ]);
    }

    public function assigned(string $toName = 'John Doe'): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => LeadActivity::TYPE_ASSIGNED,
            'data' => ['to' => $toName],
        ]);
    }
}
