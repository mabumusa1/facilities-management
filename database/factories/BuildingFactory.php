<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\City;
use App\Models\Community;
use App\Models\District;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Building>
 */
class BuildingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'community_id' => Community::factory(),
            'name' => fake()->randomElement(['Building', 'Tower', 'Block', 'Villa']).' '.fake()->randomLetter().fake()->numberBetween(1, 99),
            'city_id' => City::factory(),
            'district_id' => District::factory(),
            'no_floors' => fake()->numberBetween(1, 50),
            'year_built' => fake()->optional(0.7)->numberBetween(1990, 2025),
            'map' => null,
            'status' => Building::STATUS_ACTIVE,
        ];
    }

    /**
     * Set the building as active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Building::STATUS_ACTIVE,
        ]);
    }

    /**
     * Set the building as inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Building::STATUS_INACTIVE,
        ]);
    }

    /**
     * Set the building for a specific tenant.
     */
    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $tenant->id,
        ]);
    }

    /**
     * Set the building for a specific community.
     */
    public function forCommunity(Community $community): static
    {
        return $this->state(fn (array $attributes) => [
            'community_id' => $community->id,
            'tenant_id' => $community->tenant_id,
        ]);
    }

    /**
     * Set the building in a specific city.
     */
    public function inCity(City $city): static
    {
        return $this->state(fn (array $attributes) => [
            'city_id' => $city->id,
        ]);
    }

    /**
     * Set the building in a specific district.
     */
    public function inDistrict(District $district): static
    {
        return $this->state(fn (array $attributes) => [
            'district_id' => $district->id,
        ]);
    }

    /**
     * Set the number of floors.
     */
    public function withFloors(int $floors): static
    {
        return $this->state(fn (array $attributes) => [
            'no_floors' => $floors,
        ]);
    }

    /**
     * Set the year built.
     */
    public function builtIn(int $year): static
    {
        return $this->state(fn (array $attributes) => [
            'year_built' => $year,
        ]);
    }

    /**
     * Set map coordinates.
     */
    public function withMap(float $latitude, float $longitude): static
    {
        return $this->state(fn (array $attributes) => [
            'map' => [
                'latitude' => $latitude,
                'longitude' => $longitude,
            ],
        ]);
    }

    /**
     * Create a building as a tower (high-rise).
     */
    public function tower(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Tower '.fake()->randomLetter().fake()->numberBetween(1, 99),
            'no_floors' => fake()->numberBetween(20, 100),
        ]);
    }

    /**
     * Create a building as a villa.
     */
    public function villa(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Villa '.fake()->numberBetween(1, 999),
            'no_floors' => fake()->numberBetween(1, 3),
        ]);
    }
}
