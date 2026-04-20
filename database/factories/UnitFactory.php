<?php

namespace Database\Factories;

use App\Models\Community;
use App\Models\Status;
use App\Models\Unit;
use App\Models\UnitCategory;
use App\Models\UnitType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Unit>
 */
class UnitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->numerify('Unit ###'),
            'rf_community_id' => Community::factory(),
            'rf_building_id' => null,
            'category_id' => UnitCategory::factory(),
            'type_id' => UnitType::factory(),
            'status_id' => Status::factory(),
            'net_area' => fake()->optional()->randomFloat(2, 30, 500),
            'floor_no' => fake()->optional()->numberBetween(0, 30),
            'is_market_place' => false,
            'is_buy' => false,
            'is_off_plan_sale' => false,
            'renewal_status' => false,
        ];
    }
}
