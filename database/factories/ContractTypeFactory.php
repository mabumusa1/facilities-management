<?php

namespace Database\Factories;

use App\Models\ContractType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContractType>
 */
class ContractTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name_en' => fake()->unique()->words(2, asString: true),
            'name_ar' => null,
            'default_payment_terms_days' => null,
            'default_escalation_type' => null,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    /**
     * Inactive contract type state.
     */
    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
