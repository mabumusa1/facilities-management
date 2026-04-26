<?php

namespace Database\Factories;

use App\Enums\ReportType;
use App\Models\ReportSnapshot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReportSnapshot>
 */
class ReportSnapshotFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-1 year', '-1 month');
        $end = fake()->dateTimeBetween($start, 'now');

        return [
            'report_type' => fake()->randomElement(ReportType::cases()),
            'period_start' => $start,
            'period_end' => $end,
            'generated_at' => null,
            'payload' => null,
            'status' => 'pending',
            'requested_by_user_id' => User::factory(),
            'filters' => null,
            'error_message' => null,
        ];
    }

    /**
     * Mark the snapshot as successfully generated with a payload.
     */
    public function ready(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ready',
            'generated_at' => now(),
            'payload' => [
                'summary' => fake()->sentence(),
                'total' => fake()->randomFloat(2, 0, 100000),
            ],
            'error_message' => null,
        ]);
    }

    /**
     * Mark the snapshot generation as failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'generated_at' => null,
            'payload' => null,
            'error_message' => fake()->sentence(),
        ]);
    }

    /**
     * Use a specific report type.
     */
    public function ofType(ReportType $type): static
    {
        return $this->state(fn (array $attributes) => [
            'report_type' => $type,
        ]);
    }
}
