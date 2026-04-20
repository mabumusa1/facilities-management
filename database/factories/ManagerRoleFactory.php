<?php

namespace Database\Factories;

use App\Enums\AdminRole;
use App\Models\ManagerRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ManagerRole>
 */
class ManagerRoleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'role' => fake()->randomElement(AdminRole::cases())->value,
            'name_ar' => fake()->word(),
            'name_en' => fake()->word(),
        ];
    }
}
