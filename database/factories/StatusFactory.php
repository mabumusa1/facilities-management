<?php

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Status>
 */
class StatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);
        $domain = fake()->randomElement(Status::domains());

        return [
            'name' => ucwords($name),
            'name_ar' => null,
            'domain' => $domain,
            'slug' => $domain.'_'.Str::slug($name),
            'color' => fake()->hexColor(),
            'icon' => fake()->randomElement(['check', 'clock', 'x', 'alert', 'info']),
            'priority' => fake()->numberBetween(1, 10),
            'is_active' => true,
        ];
    }

    /**
     * Create status for a specific domain.
     */
    public function forDomain(string $domain): static
    {
        return $this->state(fn (array $attributes) => [
            'domain' => $domain,
            'slug' => $domain.'_'.Str::slug($attributes['name']),
        ]);
    }

    /**
     * Create an inactive status.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create status with specific slug.
     */
    public function withSlug(string $slug): static
    {
        return $this->state(fn (array $attributes) => [
            'slug' => $slug,
        ]);
    }

    /**
     * Create status with color.
     */
    public function withColor(string $color): static
    {
        return $this->state(fn (array $attributes) => [
            'color' => $color,
        ]);
    }
}
