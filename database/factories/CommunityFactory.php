<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Community;
use App\Models\Country;
use App\Models\Currency;
use App\Models\District;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Community>
 */
class CommunityFactory extends Factory
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
            'name' => fake()->company().' '.fake()->randomElement(['Compound', 'Residences', 'Villas', 'Heights', 'Gardens']),
            'country_id' => Country::factory(),
            'currency_id' => Currency::factory(),
            'city_id' => City::factory(),
            'district_id' => District::factory(),
            'sales_commission_rate' => fake()->randomFloat(2, 0, 10),
            'rental_commission_rate' => fake()->randomFloat(2, 0, 10),
            'map' => null,
            'is_marketplace' => false,
            'is_buy' => false,
            'marketplace_type' => Community::MARKETPLACE_TYPE_RENT,
            'is_off_plan_sale' => false,
            'status' => Community::STATUS_ACTIVE,
        ];
    }

    /**
     * Set the community as active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Community::STATUS_ACTIVE,
        ]);
    }

    /**
     * Set the community as inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Community::STATUS_INACTIVE,
        ]);
    }

    /**
     * Set the community for a specific tenant.
     */
    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $tenant->id,
        ]);
    }

    /**
     * Set the community as marketplace-enabled.
     */
    public function marketplace(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_marketplace' => true,
        ]);
    }

    /**
     * Set the community for buying.
     */
    public function forBuy(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_marketplace' => true,
            'is_buy' => true,
            'marketplace_type' => Community::MARKETPLACE_TYPE_BUY,
        ]);
    }

    /**
     * Set the community for rent.
     */
    public function forRent(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_marketplace' => true,
            'marketplace_type' => Community::MARKETPLACE_TYPE_RENT,
        ]);
    }

    /**
     * Set the community for both rent and buy.
     */
    public function forRentAndBuy(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_marketplace' => true,
            'is_buy' => true,
            'marketplace_type' => Community::MARKETPLACE_TYPE_BOTH,
        ]);
    }

    /**
     * Set the community as off-plan sale.
     */
    public function offPlanSale(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_marketplace' => true,
            'is_off_plan_sale' => true,
        ]);
    }

    /**
     * Set the community in a specific city.
     */
    public function inCity(City $city): static
    {
        return $this->state(fn (array $attributes) => [
            'city_id' => $city->id,
        ]);
    }

    /**
     * Set the community in a specific district.
     */
    public function inDistrict(District $district): static
    {
        return $this->state(fn (array $attributes) => [
            'district_id' => $district->id,
        ]);
    }

    /**
     * Set commission rates.
     */
    public function withCommission(float $salesRate, float $rentalRate): static
    {
        return $this->state(fn (array $attributes) => [
            'sales_commission_rate' => $salesRate,
            'rental_commission_rate' => $rentalRate,
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
}
