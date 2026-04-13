<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Community;
use App\Models\Contact;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestCategory;
use App\Models\ServiceRequestSubcategory;
use App\Models\Status;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceRequest>
 */
class ServiceRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $scheduledDate = $this->faker->dateTimeBetween('now', '+30 days');

        return [
            'category_id' => ServiceRequestCategory::factory(),
            'subcategory_id' => ServiceRequestSubcategory::factory(),
            'status_id' => Status::where('domain', 'request')->inRandomOrder()->first()->id ?? 1,
            'community_id' => Community::factory(),
            'building_id' => Building::factory(),
            'unit_id' => Unit::factory(),
            'requester_id' => Contact::factory(),
            'requester_type' => $this->faker->randomElement(['Owner', 'Tenant', 'Admin']),
            'professional_id' => null,
            'assigned_by' => null,
            'request_number' => 'SR-'.now()->format('Ymd').'-'.str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph,
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'scheduled_date' => $scheduledDate,
            'scheduled_time' => $this->faker->time(),
            'is_all_day' => false,
            'accepted_at' => null,
            'started_at' => null,
            'completed_at' => null,
            'canceled_at' => null,
            'estimated_cost' => $this->faker->randomFloat(2, 100, 5000),
            'actual_cost' => null,
            'currency' => 'SAR',
            'attachments' => null,
            'notes' => $this->faker->optional()->paragraph,
            'admin_notes' => null,
            'professional_notes' => null,
            'rejection_reason' => null,
            'cancellation_reason' => null,
            'rating' => null,
            'feedback' => null,
            'created_by' => Contact::factory(),
        ];
    }

    /**
     * Indicate that the request is new.
     */
    public function newRequest(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => Status::where('domain', 'request')->where('slug', 'request_new')->first()->id ?? 1,
            'professional_id' => null,
            'assigned_by' => null,
        ]);
    }

    /**
     * Indicate that the request is assigned.
     */
    public function assigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => Status::where('domain', 'request')->where('slug', 'request_assigned')->first()->id ?? 2,
            'professional_id' => Contact::factory(),
            'assigned_by' => Contact::factory(),
        ]);
    }

    /**
     * Indicate that the request is accepted.
     */
    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => Status::where('domain', 'request')->where('slug', 'request_accepted')->first()->id ?? 6,
            'professional_id' => Contact::factory(),
            'assigned_by' => Contact::factory(),
            'accepted_at' => now()->subHours(2),
        ]);
    }

    /**
     * Indicate that the request is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => Status::where('domain', 'request')->where('slug', 'request_in_progress')->first()->id ?? 5,
            'professional_id' => Contact::factory(),
            'assigned_by' => Contact::factory(),
            'accepted_at' => now()->subHours(4),
            'started_at' => now()->subHours(2),
        ]);
    }

    /**
     * Indicate that the request is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => Status::where('domain', 'request')->where('slug', 'request_completed')->first()->id ?? 3,
            'professional_id' => Contact::factory(),
            'assigned_by' => Contact::factory(),
            'accepted_at' => now()->subDays(3),
            'started_at' => now()->subDays(2),
            'completed_at' => now()->subDays(1),
            'actual_cost' => $this->faker->randomFloat(2, 100, 5000),
        ]);
    }

    /**
     * Indicate that the request is canceled.
     */
    public function canceled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => Status::where('domain', 'request')->where('slug', 'request_canceled')->first()->id ?? 4,
            'canceled_at' => now(),
            'cancellation_reason' => $this->faker->sentence,
        ]);
    }

    /**
     * Indicate that the request is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => Status::where('domain', 'request')->where('slug', 'request_rejected')->first()->id ?? 10,
            'rejection_reason' => $this->faker->sentence,
        ]);
    }

    /**
     * Indicate that the request is high priority.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'high',
        ]);
    }

    /**
     * Indicate that the request is urgent.
     */
    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'urgent',
        ]);
    }

    /**
     * Indicate that the request is scheduled for all day.
     */
    public function allDay(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_all_day' => true,
            'scheduled_time' => null,
        ]);
    }

    /**
     * Indicate that the request is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'scheduled_date' => now()->subDays(3),
            'completed_at' => null,
            'canceled_at' => null,
        ]);
    }

    /**
     * Indicate that the request has attachments.
     */
    public function withAttachments(): static
    {
        return $this->state(fn (array $attributes) => [
            'attachments' => [
                [
                    'id' => $this->faker->uuid,
                    'name' => 'photo1.jpg',
                    'url' => $this->faker->imageUrl(),
                    'type' => 'image/jpeg',
                    'size' => $this->faker->numberBetween(100000, 5000000),
                ],
                [
                    'id' => $this->faker->uuid,
                    'name' => 'photo2.jpg',
                    'url' => $this->faker->imageUrl(),
                    'type' => 'image/jpeg',
                    'size' => $this->faker->numberBetween(100000, 5000000),
                ],
            ],
        ]);
    }

    /**
     * Indicate that the request has a rating.
     */
    public function rated(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => $this->faker->numberBetween(1, 5),
            'feedback' => $this->faker->paragraph,
        ]);
    }

    /**
     * Create a request for a specific category.
     */
    public function forCategory(int $categoryId): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $categoryId,
        ]);
    }

    /**
     * Create a request for a specific subcategory.
     */
    public function forSubcategory(int $subcategoryId): static
    {
        return $this->state(fn (array $attributes) => [
            'subcategory_id' => $subcategoryId,
        ]);
    }

    /**
     * Create a request for a specific unit.
     */
    public function forUnit(int $unitId): static
    {
        return $this->state(fn (array $attributes) => [
            'unit_id' => $unitId,
        ]);
    }

    /**
     * Create a request for a specific requester.
     */
    public function forRequester(int $requesterId, string $requesterType = 'Tenant'): static
    {
        return $this->state(fn (array $attributes) => [
            'requester_id' => $requesterId,
            'requester_type' => $requesterType,
        ]);
    }

    /**
     * Create a request assigned to a specific professional.
     */
    public function assignedTo(int $professionalId, ?int $assignedBy = null): static
    {
        return $this->state(fn (array $attributes) => [
            'professional_id' => $professionalId,
            'assigned_by' => $assignedBy ?? Contact::factory(),
        ]);
    }
}
