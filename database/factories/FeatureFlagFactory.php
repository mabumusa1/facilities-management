<?php

namespace Database\Factories;

use App\Models\FeatureFlag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<FeatureFlag>
 */
class FeatureFlagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = 'ENABLE_'.fake()->unique()->words(2, true);

        return [
            'key' => Str::upper(Str::snake($name)),
            'name' => ucwords(str_replace('_', ' ', $name)),
            'name_ar' => null,
            'description' => fake()->sentence(),
            'category' => fake()->randomElement(FeatureFlag::categories()),
            'default_value' => fake()->boolean(70),
            'is_active' => true,
        ];
    }

    /**
     * Create a feature flag for a specific category.
     */
    public function forCategory(string $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => $category,
        ]);
    }

    /**
     * Create an inactive feature flag.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create a feature flag that is enabled by default.
     */
    public function enabledByDefault(): static
    {
        return $this->state(fn (array $attributes) => [
            'default_value' => true,
        ]);
    }

    /**
     * Create a feature flag that is disabled by default.
     */
    public function disabledByDefault(): static
    {
        return $this->state(fn (array $attributes) => [
            'default_value' => false,
        ]);
    }

    /**
     * Create a feature flag with a specific key.
     */
    public function withKey(string $key): static
    {
        return $this->state(fn (array $attributes) => [
            'key' => $key,
        ]);
    }
}
