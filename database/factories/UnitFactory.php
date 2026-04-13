<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\City;
use App\Models\Community;
use App\Models\District;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\UnitCategory;
use App\Models\UnitType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Unit>
 */
class UnitFactory extends Factory
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
            'building_id' => Building::factory(),
            'unit_category_id' => UnitCategory::factory(),
            'unit_type_id' => UnitType::factory(),
            'status_id' => null,
            'city_id' => null,
            'district_id' => null,
            'name' => 'Unit '.fake()->numberBetween(100, 9999),
            'floor_no' => fake()->numberBetween(1, 50),
            'net_area' => fake()->randomFloat(2, 50, 500),
            'year_built' => fake()->optional(0.7)->numberBetween(1990, 2025),
            'market_rent' => fake()->randomFloat(2, 1000, 50000),
            'about' => fake()->optional(0.5)->paragraph(),
            'map' => null,
            'photos' => null,
            'is_marketplace' => false,
            'is_off_plan_sale' => false,
        ];
    }

    /**
     * Set the unit for a specific tenant.
     */
    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $tenant->id,
        ]);
    }

    /**
     * Set the unit for a specific community.
     */
    public function forCommunity(Community $community): static
    {
        return $this->state(fn (array $attributes) => [
            'community_id' => $community->id,
            'tenant_id' => $community->tenant_id,
        ]);
    }

    /**
     * Set the unit for a specific building.
     */
    public function forBuilding(Building $building): static
    {
        return $this->state(fn (array $attributes) => [
            'building_id' => $building->id,
            'community_id' => $building->community_id,
            'tenant_id' => $building->tenant_id,
        ]);
    }

    /**
     * Set the unit category.
     */
    public function withCategory(UnitCategory $category): static
    {
        return $this->state(fn (array $attributes) => [
            'unit_category_id' => $category->id,
        ]);
    }

    /**
     * Set the unit type.
     */
    public function withType(UnitType $type): static
    {
        return $this->state(fn (array $attributes) => [
            'unit_type_id' => $type->id,
        ]);
    }

    /**
     * Set the unit status.
     */
    public function withStatus(Status $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => $status->id,
        ]);
    }

    /**
     * Set the unit in a specific city.
     */
    public function inCity(City $city): static
    {
        return $this->state(fn (array $attributes) => [
            'city_id' => $city->id,
        ]);
    }

    /**
     * Set the unit in a specific district.
     */
    public function inDistrict(District $district): static
    {
        return $this->state(fn (array $attributes) => [
            'district_id' => $district->id,
        ]);
    }

    /**
     * Set the floor number.
     */
    public function onFloor(int $floor): static
    {
        return $this->state(fn (array $attributes) => [
            'floor_no' => $floor,
        ]);
    }

    /**
     * Set the net area.
     */
    public function withArea(float $area): static
    {
        return $this->state(fn (array $attributes) => [
            'net_area' => $area,
        ]);
    }

    /**
     * Set the market rent.
     */
    public function withRent(float $rent): static
    {
        return $this->state(fn (array $attributes) => [
            'market_rent' => $rent,
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
     * Set photos.
     *
     * @param  array<string>  $photos
     */
    public function withPhotos(array $photos): static
    {
        return $this->state(fn (array $attributes) => [
            'photos' => $photos,
        ]);
    }

    /**
     * List the unit on marketplace.
     */
    public function marketplace(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_marketplace' => true,
        ]);
    }

    /**
     * Mark the unit as off-plan sale.
     */
    public function offPlanSale(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_off_plan_sale' => true,
        ]);
    }

    /**
     * Create a studio apartment.
     */
    public function studio(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Studio '.fake()->numberBetween(100, 999),
            'net_area' => fake()->randomFloat(2, 25, 50),
            'floor_no' => fake()->numberBetween(1, 20),
        ]);
    }

    /**
     * Create a penthouse unit.
     */
    public function penthouse(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Penthouse '.fake()->numberBetween(1, 99),
            'net_area' => fake()->randomFloat(2, 200, 600),
            'floor_no' => fake()->numberBetween(30, 100),
            'market_rent' => fake()->randomFloat(2, 20000, 100000),
        ]);
    }

    /**
     * Create a villa unit.
     */
    public function villa(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Villa '.fake()->numberBetween(1, 999),
            'net_area' => fake()->randomFloat(2, 300, 1000),
            'floor_no' => 1,
            'market_rent' => fake()->randomFloat(2, 15000, 80000),
        ]);
    }

    /**
     * Create a commercial unit (retail/office).
     */
    public function commercial(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Shop '.fake()->numberBetween(1, 100),
            'net_area' => fake()->randomFloat(2, 30, 200),
            'floor_no' => fake()->numberBetween(0, 3),
            'market_rent' => fake()->randomFloat(2, 5000, 30000),
        ]);
    }

    /**
     * Create a unit without a building (standalone community unit).
     */
    public function withoutBuilding(): static
    {
        return $this->state(fn (array $attributes) => [
            'building_id' => null,
        ]);
    }

    /**
     * Create a unit with description.
     */
    public function withDescription(string $description): static
    {
        return $this->state(fn (array $attributes) => [
            'about' => $description,
        ]);
    }
}
