<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Community;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Unit>
 *
 * Note: This is a minimal factory stub to support Building relationship tests.
 * Full Unit factory will be implemented in Issue #12.
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
            'name' => 'Unit '.fake()->numberBetween(100, 9999),
            'status' => 'active',
        ];
    }
}
