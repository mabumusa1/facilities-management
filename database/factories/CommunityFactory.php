<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Community;
use App\Models\Country;
use App\Models\Currency;
use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Community>
 */
class CommunityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'country_id' => Country::factory(),
            'currency_id' => Currency::factory(),
            'city_id' => City::factory(),
            'district_id' => District::factory(),
            'sales_commission_rate' => fake()->optional()->randomFloat(2, 0, 15),
            'rental_commission_rate' => fake()->optional()->randomFloat(2, 0, 15),
            'is_market_place' => false,
            'is_buy' => false,
            'is_off_plan_sale' => false,
            'is_selected_property' => false,
            'count_selected_property' => 0,
            'total_income' => 0,
        ];
    }
}
