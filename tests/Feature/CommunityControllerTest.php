<?php

namespace Tests\Feature;

use App\Models\Amenity;
use App\Models\Building;
use App\Models\City;
use App\Models\Community;
use App\Models\Country;
use App\Models\Currency;
use App\Models\District;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CommunityControllerTest extends TestCase
{
    use RefreshDatabase;

    private const INDEX_COMPONENT = 'properties/communities/index';

    private const COMMUNITIES_ROUTE = '/communities';

    private const COMMUNITIES_ALIAS_ROUTE = '/properties-list/communities';

    private const COMMUNITY_CREATE_ALIAS_ROUTE = '/properties-list/new/community';

    private const COMMUNITY_ALPHA = 'Community Alpha';

    private const INVALID_DYNAMIC_FIELDS_COMMUNITY = 'Invalid Dynamic Fields Community';

    protected Tenant $tenant;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);
    }

    /**
     * Captured listing route: /properties-list/communities
     */
    public function test_communities_index_includes_units_count_and_tab_counts(): void
    {
        $community = Community::factory()->forTenant($this->tenant)->create([
            'name' => 'Test Community 1',
        ]);
        $building = Building::factory()->forTenant($this->tenant)->forCommunity($community)->create();
        Unit::factory()->forTenant($this->tenant)->forCommunity($community)->forBuilding($building)->create();

        $response = $this->actingAs($this->user)->get(self::COMMUNITIES_ROUTE);

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component(self::INDEX_COMPONENT)
            ->where('tabCounts.communities', 1)
            ->where('tabCounts.buildings', 1)
            ->where('tabCounts.units', 1)
            ->has('communities.data', 1, fn (Assert $communityItem) => $communityItem
                ->where('name', 'Test Community 1')
                ->where('buildings_count', 1)
                ->where('units_count', 1)
                ->etc()
            )
        );
    }

    public function test_properties_list_communities_alias_resolves_to_index_component(): void
    {
        Community::factory()->forTenant($this->tenant)->create();

        $response = $this->actingAs($this->user)->get(self::COMMUNITIES_ALIAS_ROUTE);

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component(self::INDEX_COMPONENT)
            ->has('communities')
            ->has('filters')
            ->has('tabCounts')
        );
    }

    public function test_properties_list_communities_alias_accepts_sortby_and_sortdirection_filters(): void
    {
        Community::factory()->forTenant($this->tenant)->create(['name' => 'Alpha Community']);
        Community::factory()->forTenant($this->tenant)->create(['name' => 'Beta Community']);

        $response = $this->actingAs($this->user)->get(self::COMMUNITIES_ALIAS_ROUTE.'?sortBy=name&sortDirection=asc');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component(self::INDEX_COMPONENT)
            ->where('filters.sortBy', 'name')
            ->where('filters.sortDirection', 'asc')
        );
    }

    public function test_properties_list_new_community_alias_renders_create_component_with_form_options(): void
    {
        $country = Country::factory()->create(['name' => 'Saudi Arabia', 'currency_code' => 'SAR']);
        $currency = Currency::factory()->create(['name' => 'Saudi Riyal', 'code' => 'SAR']);
        $city = City::factory()->forCountry($country)->create(['name' => 'Riyadh']);
        District::factory()->forCity($city)->create(['name' => 'Al Diriyah']);
        Amenity::factory()->create(['name' => 'Swimming Pool', 'is_active' => true]);

        $response = $this->actingAs($this->user)->get(self::COMMUNITY_CREATE_ALIAS_ROUTE);

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('properties/communities/create')
            ->has('countries', 1)
            ->has('currencies', 1)
            ->has('cities', 1)
            ->has('districts', 1)
            ->has('amenities', 1)
            ->where('defaults.country_id', $country->id)
            ->where('defaults.currency_id', $currency->id)
        );
    }

    public function test_store_creates_community_with_required_form_fields(): void
    {
        $country = Country::factory()->create(['currency_code' => 'SAR']);
        $currency = Currency::factory()->create(['code' => 'SAR']);
        $city = City::factory()->forCountry($country)->create();
        $district = District::factory()->forCity($city)->create();
        $amenity = Amenity::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->user)->post(self::COMMUNITIES_ROUTE, [
            'name' => self::COMMUNITY_ALPHA,
            'country_id' => $country->id,
            'currency_id' => $currency->id,
            'city_id' => $city->id,
            'district_id' => $district->id,
            'location' => 'Riyadh Main Road',
            'sales_commission_rate' => 2.5,
            'rental_commission_rate' => 5,
            'about' => 'A modern mixed-use neighborhood.',
            'amenity_ids' => [$amenity->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('communities', [
            'tenant_id' => $this->tenant->id,
            'name' => self::COMMUNITY_ALPHA,
            'country_id' => $country->id,
            'currency_id' => $currency->id,
            'city_id' => $city->id,
            'district_id' => $district->id,
            'sales_commission_rate' => 2.5,
            'rental_commission_rate' => 5,
        ]);

        $community = Community::query()->where('name', self::COMMUNITY_ALPHA)->firstOrFail();
        $this->assertSame('Riyadh Main Road', $community->map['location'] ?? null);
        $this->assertSame('A modern mixed-use neighborhood.', $community->map['about'] ?? null);
        $this->assertDatabaseHas('community_amenities', [
            'community_id' => $community->id,
            'amenity_id' => $amenity->id,
        ]);
    }

    public function test_store_validates_dynamic_location_field_dependencies(): void
    {
        $countryA = Country::factory()->create(['currency_code' => 'SAR']);
        $countryB = Country::factory()->create(['currency_code' => 'USD']);

        Currency::factory()->create(['code' => 'SAR']);
        $currencyUsd = Currency::factory()->create(['code' => 'USD']);

        $cityFromCountryB = City::factory()->forCountry($countryB)->create();
        $districtFromCityB = District::factory()->forCity($cityFromCountryB)->create();

        $response = $this->actingAs($this->user)
            ->from(self::COMMUNITY_CREATE_ALIAS_ROUTE)
            ->post(self::COMMUNITIES_ROUTE, [
                'name' => self::INVALID_DYNAMIC_FIELDS_COMMUNITY,
                'country_id' => $countryA->id,
                'currency_id' => $currencyUsd->id,
                'city_id' => $cityFromCountryB->id,
                'district_id' => $districtFromCityB->id,
                'location' => 'Auto Filled Location',
                'sales_commission_rate' => 1,
                'rental_commission_rate' => 2,
            ]);

        $response->assertRedirect(self::COMMUNITY_CREATE_ALIAS_ROUTE);
        $response->assertSessionHasErrors(['currency_id', 'city_id']);
        $this->assertDatabaseMissing('communities', [
            'name' => self::INVALID_DYNAMIC_FIELDS_COMMUNITY,
        ]);
    }
}
