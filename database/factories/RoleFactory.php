<?php

namespace Database\Factories;

use App\Enums\RoleType;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->slug(2),
            'guard_name' => 'web',
            'name_ar' => fake()->words(2, true),
            'name_en' => fake()->words(2, true),
            'type' => fake()->randomElement(RoleType::cases()),
            'account_tenant_id' => null,
        ];
    }

    public function userRole(): static
    {
        return $this->state(['type' => RoleType::UserRole]);
    }

    public function adminRole(): static
    {
        return $this->state(['type' => RoleType::AdminRole]);
    }
}
