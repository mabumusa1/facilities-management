<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\MarketplaceUnit;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MarketplaceUnit>
 */
class MarketplaceUnitFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $listingPrice = fake()->randomFloat(2, 100000, 5000000);
        $originalPrice = fake()->optional(0.3)->randomFloat(2, $listingPrice, $listingPrice * 1.2);

        $listingTitles = [
            ['en' => 'Luxury Villa with Pool', 'ar' => 'فيلا فاخرة مع مسبح'],
            ['en' => 'Modern Apartment in City Center', 'ar' => 'شقة عصرية في وسط المدينة'],
            ['en' => 'Spacious Family Home', 'ar' => 'منزل عائلي واسع'],
            ['en' => 'Penthouse with Sea View', 'ar' => 'بنتهاوس مع إطلالة بحرية'],
            ['en' => 'Cozy Studio Apartment', 'ar' => 'شقة استوديو مريحة'],
            ['en' => 'Commercial Office Space', 'ar' => 'مساحة مكتبية تجارية'],
        ];

        $listing = fake()->randomElement($listingTitles);

        return [
            'tenant_id' => Tenant::factory(),
            'unit_id' => Unit::factory(),
            'status_id' => fn () => Status::firstOrCreate(
                ['slug' => 'marketplace_available'],
                ['domain' => 'marketplace', 'name' => 'Available']
            )->id,
            'listing_title_en' => $listing['en'],
            'listing_title_ar' => $listing['ar'],
            'listing_description_en' => fake()->paragraph(3),
            'listing_description_ar' => fake()->optional(0.5)->paragraph(3),
            'listing_price' => $listingPrice,
            'original_price' => $originalPrice,
            'price_per_sqm' => fake()->optional(0.5)->randomFloat(2, 5000, 20000),
            'price_negotiable' => fake()->boolean(30),
            'is_featured' => fake()->boolean(20),
            'is_published' => fake()->boolean(70),
            'published_at' => fn (array $attributes) => $attributes['is_published'] ? now()->subDays(fake()->numberBetween(1, 30)) : null,
            'expires_at' => fake()->optional(0.3)->dateTimeBetween('+30 days', '+90 days'),
            'listed_by' => Contact::factory(),
            'assigned_agent' => fake()->optional(0.5)->passthrough(Contact::factory()),
            'views_count' => fake()->numberBetween(0, 1000),
            'inquiries_count' => fake()->numberBetween(0, 50),
        ];
    }

    /**
     * Indicate that the listing is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'published_at' => now()->subDays(fake()->numberBetween(1, 30)),
        ]);
    }

    /**
     * Indicate that the listing is unpublished.
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }

    /**
     * Indicate that the listing is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the listing is sold.
     */
    public function sold(): static
    {
        return $this->state(fn (array $attributes) => [
            'buyer_id' => Contact::factory(),
            'sold_at' => now()->subDays(fake()->numberBetween(1, 30)),
            'sold_price' => $attributes['listing_price'] ?? fake()->randomFloat(2, 100000, 5000000),
            'is_published' => false,
        ]);
    }

    /**
     * Indicate that the listing is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDays(fake()->numberBetween(1, 30)),
        ]);
    }
}
