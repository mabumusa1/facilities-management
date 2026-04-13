<?php

namespace Database\Factories;

use App\Models\Community;
use App\Models\Facility;
use App\Models\FacilityCategory;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Facility>
 */
class FacilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $facilityNames = [
            ['en' => 'Swimming Pool', 'ar' => 'حمام السباحة'],
            ['en' => 'Gym', 'ar' => 'صالة الألعاب الرياضية'],
            ['en' => 'Tennis Court', 'ar' => 'ملعب التنس'],
            ['en' => 'BBQ Area', 'ar' => 'منطقة الشواء'],
            ['en' => 'Meeting Room', 'ar' => 'غرفة الاجتماعات'],
            ['en' => 'Kids Play Area', 'ar' => 'منطقة لعب الأطفال'],
        ];

        $facility = fake()->randomElement($facilityNames);

        return [
            'tenant_id' => Tenant::factory(),
            'community_id' => Community::factory(),
            'category_id' => FacilityCategory::factory(),
            'name_en' => $facility['en'],
            'name_ar' => $facility['ar'],
            'description_en' => fake()->optional(0.7)->paragraph(),
            'description_ar' => fake()->optional(0.5)->paragraph(),
            'gender' => fake()->randomElement(['male', 'female', 'mixed']),
            'booking_type' => fake()->randomElement(['hourly', 'daily', 'session']),
            'operating_days' => fake()->randomElements(['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'], fake()->numberBetween(5, 7)),
            'opening_time' => fake()->time('H:i'),
            'closing_time' => fake()->time('H:i'),
            'capacity' => fake()->numberBetween(10, 100),
            'price_per_hour' => fake()->randomFloat(2, 50, 500),
            'price_per_day' => fake()->randomFloat(2, 200, 2000),
            'price_per_session' => fake()->randomFloat(2, 100, 1000),
            'requires_approval' => fake()->boolean(70),
            'is_active' => fake()->boolean(90),
            'booking_duration_minutes' => fake()->randomElement([30, 60, 90, 120]),
            'max_advance_booking_days' => fake()->numberBetween(7, 90),
            'rules_en' => fake()->optional(0.6)->paragraph(),
            'rules_ar' => fake()->optional(0.4)->paragraph(),
        ];
    }

    /**
     * Indicate that the facility is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the facility is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
