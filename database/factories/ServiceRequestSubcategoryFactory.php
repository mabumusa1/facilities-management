<?php

namespace Database\Factories;

use App\Models\ServiceRequestCategory;
use App\Models\ServiceRequestSubcategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceRequestSubcategory>
 */
class ServiceRequestSubcategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => ServiceRequestCategory::factory(),
            'name' => $this->faker->words(2, true),
            'name_ar' => null,
            'active' => true,
            'icon_id' => null,
        ];
    }

    /**
     * Indicate that the subcategory is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }

    /**
     * Set the category for the subcategory.
     */
    public function forCategory(int $categoryId): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $categoryId,
        ]);
    }

    /**
     * Create a "Maintenance" subcategory.
     */
    public function maintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Maintenance',
            'name_ar' => 'صيانة',
        ]);
    }

    /**
     * Create a "House Cleaning" subcategory.
     */
    public function houseCleaning(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'House Cleaning',
            'name_ar' => 'تنظيف المنزل',
        ]);
    }

    /**
     * Create a "Car Wash" subcategory.
     */
    public function carWash(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Car Wash',
            'name_ar' => 'غسيل السيارات',
        ]);
    }

    /**
     * Create a "Security & Safety" subcategory.
     */
    public function securityAndSafety(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Security & Safety',
            'name_ar' => 'الأمن و السلامة',
        ]);
    }
}
