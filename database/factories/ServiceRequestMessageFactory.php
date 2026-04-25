<?php

namespace Database\Factories;

use App\Models\Request;
use App\Models\Resident;
use App\Models\ServiceRequestMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceRequestMessage>
 */
class ServiceRequestMessageFactory extends Factory
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
            'sender_type' => Resident::class,
            'sender_id' => Resident::factory(),
            'body' => $this->faker->paragraph(),
            'is_internal' => false,
        ];
    }

    /**
     * Mark the message as an internal (staff-only) note.
     */
    public function internal(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_internal' => true,
        ]);
    }
}
