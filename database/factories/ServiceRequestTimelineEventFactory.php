<?php

namespace Database\Factories;

use App\Models\Request;
use App\Models\Resident;
use App\Models\ServiceRequestTimelineEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceRequestTimelineEvent>
 */
class ServiceRequestTimelineEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_request_id' => Request::factory(),
            'event_type' => $this->faker->randomElement(ServiceRequestTimelineEvent::EVENT_TYPES),
            'actor_type' => Resident::class,
            'actor_id' => Resident::factory(),
            'metadata' => null,
        ];
    }

    /**
     * Set event_type to 'submitted'.
     */
    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => ['event_type' => 'submitted']);
    }

    /**
     * Set event_type to 'assigned'.
     */
    public function assigned(): static
    {
        return $this->state(fn (array $attributes) => ['event_type' => 'assigned']);
    }

    /**
     * Set event_type to 'in_progress'.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => ['event_type' => 'in_progress']);
    }

    /**
     * Set event_type to 'resolved'.
     */
    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => ['event_type' => 'resolved']);
    }

    /**
     * Set event_type to 'closed'.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => ['event_type' => 'closed']);
    }
}
