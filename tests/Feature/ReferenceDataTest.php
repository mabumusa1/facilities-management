<?php

namespace Tests\Feature;

use App\Models\Amenity;
use App\Models\City;
use App\Models\Country;
use App\Models\Currency;
use App\Models\District;
use App\Models\FacilityCategory;
use App\Models\UnitCategory;
use App\Models\UnitType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferenceDataTest extends TestCase
{
    use RefreshDatabase;

    // ==================== Country Tests ====================

    public function test_country_can_be_created(): void
    {
        $country = Country::factory()->create([
            'name' => 'United Arab Emirates',
            'iso2' => 'AE',
            'iso3' => 'ARE',
        ]);

        $this->assertDatabaseHas('countries', [
            'name' => 'United Arab Emirates',
            'iso2' => 'AE',
            'iso3' => 'ARE',
        ]);
        $this->assertTrue($country->is_active);
    }

    public function test_country_has_cities_relationship(): void
    {
        $country = Country::factory()->create();
        $city = City::factory()->forCountry($country)->create();

        $this->assertTrue($country->cities->contains($city));
        $this->assertEquals(1, $country->cities->count());
    }

    public function test_country_active_scope(): void
    {
        Country::factory()->count(3)->create();
        Country::factory()->inactive()->create();

        $this->assertEquals(3, Country::active()->count());
    }

    // ==================== Currency Tests ====================

    public function test_currency_can_be_created(): void
    {
        $currency = Currency::factory()->create([
            'name' => 'UAE Dirham',
            'code' => 'AED',
            'symbol' => 'د.إ',
            'decimal_places' => 2,
        ]);

        $this->assertDatabaseHas('currencies', [
            'name' => 'UAE Dirham',
            'code' => 'AED',
        ]);
        $this->assertEquals(2, $currency->decimal_places);
    }

    public function test_currency_format_method(): void
    {
        $currency = Currency::factory()->create([
            'symbol' => '$',
            'decimal_places' => 2,
        ]);

        $this->assertEquals('$1,234.56', $currency->format(1234.56));
    }

    public function test_currency_no_decimals(): void
    {
        $currency = Currency::factory()->noDecimals()->create([
            'symbol' => '¥',
        ]);

        $this->assertEquals(0, $currency->decimal_places);
        $this->assertEquals('¥1,235', $currency->format(1234.56));
    }

    public function test_currency_active_scope(): void
    {
        Currency::factory()->count(2)->create();
        Currency::factory()->inactive()->create();

        $this->assertEquals(2, Currency::active()->count());
    }

    // ==================== City Tests ====================

    public function test_city_can_be_created(): void
    {
        $country = Country::factory()->create();
        $city = City::factory()->forCountry($country)->create([
            'name' => 'Dubai',
            'name_ar' => 'دبي',
        ]);

        $this->assertDatabaseHas('cities', [
            'name' => 'Dubai',
            'country_id' => $country->id,
        ]);
    }

    public function test_city_belongs_to_country(): void
    {
        $country = Country::factory()->create(['name' => 'UAE']);
        $city = City::factory()->forCountry($country)->create();

        $this->assertEquals('UAE', $city->country->name);
    }

    public function test_city_has_districts(): void
    {
        $city = City::factory()->create();
        District::factory()->forCity($city)->count(3)->create();

        $this->assertEquals(3, $city->districts->count());
    }

    public function test_city_cascade_delete_with_country(): void
    {
        $country = Country::factory()->create();
        $city = City::factory()->forCountry($country)->create();
        $cityId = $city->id;

        $country->delete();

        $this->assertDatabaseMissing('cities', ['id' => $cityId]);
    }

    // ==================== District Tests ====================

    public function test_district_can_be_created(): void
    {
        $city = City::factory()->create();
        $district = District::factory()->forCity($city)->create([
            'name' => 'Downtown Dubai',
            'name_ar' => 'وسط مدينة دبي',
        ]);

        $this->assertDatabaseHas('districts', [
            'name' => 'Downtown Dubai',
            'city_id' => $city->id,
        ]);
    }

    public function test_district_belongs_to_city(): void
    {
        $city = City::factory()->create(['name' => 'Dubai']);
        $district = District::factory()->forCity($city)->create();

        $this->assertEquals('Dubai', $district->city->name);
    }

    public function test_district_cascade_delete_with_city(): void
    {
        $city = City::factory()->create();
        $district = District::factory()->forCity($city)->create();
        $districtId = $district->id;

        $city->delete();

        $this->assertDatabaseMissing('districts', ['id' => $districtId]);
    }

    // ==================== Unit Category Tests ====================

    public function test_unit_category_can_be_created(): void
    {
        $category = UnitCategory::factory()->create([
            'name' => 'Residential',
            'name_ar' => 'سكني',
            'description' => 'Residential properties',
        ]);

        $this->assertDatabaseHas('unit_categories', [
            'name' => 'Residential',
        ]);
        $this->assertTrue($category->is_active);
    }

    public function test_unit_category_has_unit_types(): void
    {
        $category = UnitCategory::factory()->create();
        UnitType::factory()->forCategory($category)->count(3)->create();

        $this->assertEquals(3, $category->unitTypes->count());
    }

    public function test_unit_category_active_scope(): void
    {
        UnitCategory::factory()->count(2)->create();
        UnitCategory::factory()->inactive()->create();

        $this->assertEquals(2, UnitCategory::active()->count());
    }

    // ==================== Unit Type Tests ====================

    public function test_unit_type_can_be_created(): void
    {
        $category = UnitCategory::factory()->create(['name' => 'Residential']);
        $type = UnitType::factory()->forCategory($category)->create([
            'name' => 'Apartment',
            'name_ar' => 'شقة',
        ]);

        $this->assertDatabaseHas('unit_types', [
            'name' => 'Apartment',
            'unit_category_id' => $category->id,
        ]);
    }

    public function test_unit_type_belongs_to_category(): void
    {
        $category = UnitCategory::factory()->create(['name' => 'Commercial']);
        $type = UnitType::factory()->forCategory($category)->create();

        $this->assertEquals('Commercial', $type->category->name);
    }

    public function test_unit_type_cascade_delete_with_category(): void
    {
        $category = UnitCategory::factory()->create();
        $type = UnitType::factory()->forCategory($category)->create();
        $typeId = $type->id;

        $category->delete();

        $this->assertDatabaseMissing('unit_types', ['id' => $typeId]);
    }

    // ==================== Facility Category Tests ====================

    public function test_facility_category_can_be_created(): void
    {
        $category = FacilityCategory::factory()->create([
            'name' => 'Sports & Fitness',
            'name_ar' => 'الرياضة واللياقة',
            'icon' => 'dumbbell',
        ]);

        $this->assertDatabaseHas('facility_categories', [
            'name' => 'Sports & Fitness',
            'icon' => 'dumbbell',
        ]);
    }

    public function test_facility_category_with_icon(): void
    {
        $category = FacilityCategory::factory()->withIcon('swimming-pool')->create();

        $this->assertEquals('swimming-pool', $category->icon);
    }

    public function test_facility_category_active_scope(): void
    {
        FacilityCategory::factory()->count(3)->create();
        FacilityCategory::factory()->inactive()->create();

        $this->assertEquals(3, FacilityCategory::active()->count());
    }

    // ==================== Amenity Tests ====================

    public function test_amenity_can_be_created(): void
    {
        $amenity = Amenity::factory()->create([
            'name' => 'Swimming Pool',
            'name_ar' => 'مسبح',
            'icon' => 'pool',
        ]);

        $this->assertDatabaseHas('amenities', [
            'name' => 'Swimming Pool',
            'icon' => 'pool',
        ]);
        $this->assertTrue($amenity->is_active);
    }

    public function test_amenity_with_icon(): void
    {
        $amenity = Amenity::factory()->withIcon('gym')->create();

        $this->assertEquals('gym', $amenity->icon);
    }

    public function test_amenity_active_scope(): void
    {
        Amenity::factory()->count(4)->create();
        Amenity::factory()->inactive()->create();

        $this->assertEquals(4, Amenity::active()->count());
    }

    // ==================== Seeder Tests ====================

    public function test_seeders_create_reference_data(): void
    {
        $this->seed([
            \Database\Seeders\CountrySeeder::class,
            \Database\Seeders\CurrencySeeder::class,
            \Database\Seeders\UnitCategorySeeder::class,
            \Database\Seeders\FacilityCategorySeeder::class,
            \Database\Seeders\AmenitySeeder::class,
        ]);

        // Countries
        $this->assertDatabaseHas('countries', ['iso2' => 'AE']);
        $this->assertTrue(Country::where('iso2', 'AE')->exists());

        // Currencies
        $this->assertDatabaseHas('currencies', ['code' => 'AED']);
        $this->assertTrue(Currency::where('code', 'USD')->exists());

        // Unit Categories
        $this->assertTrue(UnitCategory::where('name', 'Residential')->exists());
        $this->assertTrue(UnitCategory::where('name', 'Commercial')->exists());

        // Facility Categories
        $this->assertTrue(FacilityCategory::where('name', 'Sports & Fitness')->exists());

        // Amenities
        $this->assertTrue(Amenity::where('name', 'Swimming Pool')->exists());
        $this->assertTrue(Amenity::where('name', 'Gym')->exists());
    }

    public function test_city_seeder_creates_cities_for_countries(): void
    {
        $this->seed([
            \Database\Seeders\CountrySeeder::class,
            \Database\Seeders\CitySeeder::class,
        ]);

        $uae = Country::where('iso2', 'AE')->first();
        $this->assertNotNull($uae);
        $this->assertTrue($uae->cities()->where('name', 'Dubai')->exists());
        $this->assertTrue($uae->cities()->where('name', 'Abu Dhabi')->exists());
    }

    public function test_district_seeder_creates_districts_for_cities(): void
    {
        $this->seed([
            \Database\Seeders\CountrySeeder::class,
            \Database\Seeders\CitySeeder::class,
            \Database\Seeders\DistrictSeeder::class,
        ]);

        $dubai = City::where('name', 'Dubai')->first();
        $this->assertNotNull($dubai);
        $this->assertTrue($dubai->districts()->where('name', 'Downtown Dubai')->exists());
        $this->assertTrue($dubai->districts()->where('name', 'Dubai Marina')->exists());
    }

    public function test_unit_type_seeder_creates_types_for_categories(): void
    {
        $this->seed([
            \Database\Seeders\UnitCategorySeeder::class,
            \Database\Seeders\UnitTypeSeeder::class,
        ]);

        $residential = UnitCategory::where('name', 'Residential')->first();
        $this->assertNotNull($residential);
        $this->assertTrue($residential->unitTypes()->where('name', 'Apartment')->exists());
        $this->assertTrue($residential->unitTypes()->where('name', 'Villa')->exists());
    }
}
