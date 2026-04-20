<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\Setting;
use App\Models\Status;
use App\Models\UnitCategory;
use App\Models\UnitType;
use Database\Seeders\CitySeeder;
use Database\Seeders\CountrySeeder;
use Database\Seeders\CurrencySeeder;
use Database\Seeders\DistrictSeeder;
use Database\Seeders\SettingSeeder;
use Database\Seeders\StatusSeeder;
use Database\Seeders\UnitCategorySeeder;
use Database\Seeders\UnitTypeSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ReferenceDataSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_countries_are_seeded(): void
    {
        $this->seed(CountrySeeder::class);

        $this->assertDatabaseCount('countries', 8);
        $this->assertDatabaseHas('countries', ['iso2' => 'SA', 'name_en' => 'Saudi Arabia']);
    }

    public function test_cities_are_seeded(): void
    {
        $this->seed([CountrySeeder::class, CitySeeder::class]);

        $this->assertDatabaseCount('cities', 8);
        $this->assertDatabaseHas('cities', ['name' => 'Riyadh']);
    }

    public function test_districts_are_seeded(): void
    {
        $this->seed([CountrySeeder::class, CitySeeder::class, DistrictSeeder::class]);

        $this->assertDatabaseCount('districts', 5);
        $this->assertDatabaseHas('districts', ['name' => 'Al Olaya']);
    }

    public function test_currencies_are_seeded(): void
    {
        $this->seed(CurrencySeeder::class);

        $this->assertDatabaseCount('currencies', 8);
        $this->assertDatabaseHas('currencies', ['code' => 'SAR']);
    }

    public function test_statuses_are_seeded(): void
    {
        $this->seed(StatusSeeder::class);

        $this->assertGreaterThanOrEqual(40, Status::count());
        $this->assertDatabaseHas('rf_statuses', ['id' => 1, 'type' => 'request']);
        $this->assertDatabaseHas('rf_statuses', ['id' => 30, 'type' => 'lease']);
        $this->assertDatabaseHas('rf_statuses', ['id' => 23, 'type' => 'unit']);
    }

    public function test_settings_are_seeded(): void
    {
        $this->seed(SettingSeeder::class);

        $this->assertGreaterThanOrEqual(11, Setting::count());
        $this->assertDatabaseHas('rf_settings', ['name' => 'Monthly', 'type' => 'payment_schedule']);
        $this->assertDatabaseHas('rf_settings', ['name' => 'New Contract', 'type' => 'rental_contract_type']);
    }

    public function test_unit_categories_are_seeded(): void
    {
        $this->seed(UnitCategorySeeder::class);

        $this->assertDatabaseCount('rf_unit_categories', 4);
        $this->assertDatabaseHas('rf_unit_categories', ['name' => 'Residential']);
    }

    public function test_unit_types_are_seeded(): void
    {
        $this->seed([UnitCategorySeeder::class, UnitTypeSeeder::class]);

        $this->assertGreaterThanOrEqual(11, UnitType::count());
        $this->assertDatabaseHas('rf_unit_types', ['name' => 'Apartment']);
        $this->assertDatabaseHas('rf_unit_types', ['name' => 'Office']);
    }

    public function test_city_belongs_to_country(): void
    {
        $country = Country::factory()->create();
        $city = City::factory()->recycle($country)->create();

        $this->assertTrue($city->country->is($country));
    }

    public function test_district_belongs_to_city(): void
    {
        $city = City::factory()->create();
        $district = District::factory()->recycle($city)->create();

        $this->assertTrue($district->city->is($city));
    }

    public function test_unit_type_belongs_to_category(): void
    {
        $category = UnitCategory::factory()->create();
        $type = UnitType::factory()->recycle($category)->create();

        $this->assertTrue($type->category->is($category));
    }

    public function test_setting_self_referencing_parent(): void
    {
        $parent = Setting::factory()->create(['type' => 'lease_setting']);
        $child = Setting::factory()->childOf($parent)->create();

        $this->assertTrue($child->parent->is($parent));
        $this->assertTrue($parent->children->contains($child));
    }

    public function test_bilingual_name_returns_locale_based_value(): void
    {
        $country = Country::factory()->create([
            'name_en' => 'Saudi Arabia',
            'name_ar' => 'المملكة العربية السعودية',
        ]);

        app()->setLocale('en');
        $this->assertEquals('Saudi Arabia', $country->name);

        app()->setLocale('ar');
        $this->assertEquals('المملكة العربية السعودية', $country->name);
    }

    public function test_seeders_are_idempotent(): void
    {
        $this->seed([
            CountrySeeder::class,
            CurrencySeeder::class,
            StatusSeeder::class,
            UnitCategorySeeder::class,
        ]);

        $countBefore = Country::count();

        $this->seed(CountrySeeder::class);

        $this->assertEquals($countBefore, Country::count());
    }
}
