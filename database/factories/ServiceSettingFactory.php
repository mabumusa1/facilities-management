<?php

namespace Database\Factories;

use App\Models\RequestCategory;
use App\Models\ServiceSetting;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceSetting>
 */
class ServiceSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_tenant_id' => Tenant::current()?->id,
            'category_id' => RequestCategory::factory(),
            'visibilities' => ['tenant', 'owner'],
            'permissions' => ['create', 'view'],
            'submit_request_before_type' => $this->faker->randomElement(['hours', 'days']),
            'submit_request_before_value' => $this->faker->numberBetween(1, 48),
            'capacity_type' => 'per_slot',
            'capacity_value' => $this->faker->numberBetween(1, 50),
        ];
    }
}
