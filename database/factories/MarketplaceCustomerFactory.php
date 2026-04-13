<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\MarketplaceCustomer;
use App\Models\MarketplaceUnit;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MarketplaceCustomer>
 */
class MarketplaceCustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $budgetMin = fake()->randomFloat(2, 100000, 500000);
        $budgetMax = fake()->randomFloat(2, $budgetMin, $budgetMin * 3);

        return [
            'tenant_id' => Tenant::factory(),
            'contact_id' => fake()->optional(0.3)->passthrough(Contact::factory()),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'national_id' => fake()->optional(0.5)->numerify('##########'),
            'customer_type' => fake()->randomElement(['buyer', 'renter', 'investor']),
            'status' => fake()->randomElement(['lead', 'active', 'qualified', 'negotiating', 'converted', 'inactive']),
            'budget_min' => $budgetMin,
            'budget_max' => $budgetMax,
            'preferred_payment_method' => fake()->optional(0.5)->randomElement(['cash', 'mortgage', 'installments']),
            'preferred_unit_types' => fake()->optional(0.5)->randomElements(['apartment', 'villa', 'townhouse', 'studio', 'penthouse'], 2),
            'preferred_locations' => fake()->optional(0.5)->randomElements(['Riyadh', 'Jeddah', 'Dammam', 'Mecca', 'Medina'], 2),
            'preferred_bedrooms_min' => fake()->optional(0.5)->numberBetween(1, 3),
            'preferred_bedrooms_max' => fake()->optional(0.5)->numberBetween(3, 6),
            'preferred_area_min' => fake()->optional(0.5)->randomFloat(2, 80, 150),
            'preferred_area_max' => fake()->optional(0.5)->randomFloat(2, 150, 500),
            'source' => fake()->optional(0.7)->randomElement(['website', 'referral', 'social_media', 'agent', 'walk_in', 'advertisement']),
            'campaign' => fake()->optional(0.3)->word(),
            'lead_score' => fake()->numberBetween(0, 100),
            'notes' => fake()->optional(0.3)->sentence(),
            'assigned_agent' => fake()->optional(0.5)->passthrough(Contact::factory()),
        ];
    }

    /**
     * Indicate that the customer is a lead.
     */
    public function lead(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'lead',
            'lead_score' => fake()->numberBetween(0, 30),
        ]);
    }

    /**
     * Indicate that the customer is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'lead_score' => fake()->numberBetween(30, 50),
        ]);
    }

    /**
     * Indicate that the customer is qualified.
     */
    public function qualified(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'qualified',
            'lead_score' => fake()->numberBetween(50, 70),
        ]);
    }

    /**
     * Indicate that the customer is negotiating.
     */
    public function negotiating(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'negotiating',
            'lead_score' => fake()->numberBetween(70, 90),
        ]);
    }

    /**
     * Indicate that the customer is converted.
     */
    public function converted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'converted',
            'lead_score' => 100,
            'converted_at' => now()->subDays(fake()->numberBetween(1, 30)),
            'converted_unit_id' => MarketplaceUnit::factory(),
        ]);
    }

    /**
     * Indicate that the customer is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the customer is a buyer.
     */
    public function buyer(): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_type' => 'buyer',
        ]);
    }

    /**
     * Indicate that the customer is a renter.
     */
    public function renter(): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_type' => 'renter',
        ]);
    }

    /**
     * Indicate that the customer is an investor.
     */
    public function investor(): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_type' => 'investor',
        ]);
    }
}
