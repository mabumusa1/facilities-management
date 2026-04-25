<?php

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Setting>
 */
class SettingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'name_ar' => fake()->word(),
            'name_en' => fake()->word(),
            'type' => fake()->randomElement(['rental_contract_type', 'payment_schedule', 'lease_setting', 'invoice_setting']),
            'subtype' => null,
            'parent_id' => null,
            'is_active' => true,
            'is_default' => false,
        ];
    }

    public function childOf(Setting $parent): static
    {
        return $this->state(fn () => [
            'parent_id' => $parent->id,
            'type' => $parent->type,
        ]);
    }

    public function transactionCategory(string $subtype = 'income'): static
    {
        return $this->state(fn () => [
            'type' => 'transaction_category',
            'subtype' => $subtype,
        ]);
    }
}
