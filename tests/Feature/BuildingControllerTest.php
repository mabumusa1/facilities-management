<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\City;
use App\Models\Community;
use App\Models\District;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BuildingControllerTest extends TestCase
{
    use RefreshDatabase;

    private const BUILDINGS_ROUTE = '/buildings';

    private const BUILDINGS_ALIAS_ROUTE = '/properties-list/buildings';

    private const BUILDINGS_CREATE_ROUTE = '/buildings/create';

    private const BUILDINGS_ALIAS_CREATE_ROUTE = '/properties-list/new/building';

    private const VALID_BUILDING_NAME = 'Tower A';

    private const INVALID_BUILDING_NAME = 'Cross Tenant Building';

    protected Tenant $tenant;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_create_displays_building_form_options(): void
    {
        Community::factory()->forTenant($this->tenant)->create();

        $response = $this->actingAs($this->user)->get(self::BUILDINGS_CREATE_ROUTE);

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('properties/buildings/create')
            ->has('communities', 1)
        );
    }

    public function test_properties_list_buildings_alias_displays_building_index_page(): void
    {
        Building::factory()->forTenant($this->tenant)->create();

        $response = $this->actingAs($this->user)->get(self::BUILDINGS_ALIAS_ROUTE);

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('properties/buildings/index')
            ->has('buildings.data', 1)
            ->where('tabCounts.buildings', 1)
        );
    }

    public function test_properties_list_new_building_alias_displays_building_create_page(): void
    {
        Community::factory()->forTenant($this->tenant)->create();

        $response = $this->actingAs($this->user)->get(self::BUILDINGS_ALIAS_CREATE_ROUTE);

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('properties/buildings/create')
            ->has('communities', 1)
        );
    }

    public function test_store_creates_building_when_community_is_valid_for_tenant(): void
    {
        $community = Community::factory()->forTenant($this->tenant)->create();

        $response = $this->actingAs($this->user)->post(self::BUILDINGS_ROUTE, [
            'name' => self::VALID_BUILDING_NAME,
            'community_id' => $community->id,
            'no_floors' => 8,
            'year_built' => 2022,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('buildings', [
            'tenant_id' => $this->tenant->id,
            'name' => self::VALID_BUILDING_NAME,
            'community_id' => $community->id,
            'no_floors' => 8,
            'year_built' => 2022,
            'status' => Building::STATUS_ACTIVE,
        ]);
    }

    public function test_store_rejects_community_from_another_tenant(): void
    {
        $otherTenant = Tenant::factory()->create();
        $otherTenantCommunity = Community::factory()->forTenant($otherTenant)->create();

        $response = $this->actingAs($this->user)
            ->from(self::BUILDINGS_CREATE_ROUTE)
            ->post(self::BUILDINGS_ROUTE, [
                'name' => self::INVALID_BUILDING_NAME,
                'community_id' => $otherTenantCommunity->id,
                'no_floors' => 4,
            ]);

        $response->assertRedirect(self::BUILDINGS_CREATE_ROUTE);
        $response->assertSessionHasErrors(['community_id']);
        $this->assertDatabaseMissing('buildings', [
            'name' => self::INVALID_BUILDING_NAME,
        ]);
    }

    public function test_store_validates_city_and_district_relationship(): void
    {
        $community = Community::factory()->forTenant($this->tenant)->create();
        $cityA = City::factory()->create();
        $cityB = City::factory()->create();
        $districtFromCityB = District::factory()->forCity($cityB)->create();

        $response = $this->actingAs($this->user)
            ->from(self::BUILDINGS_CREATE_ROUTE)
            ->post(self::BUILDINGS_ROUTE, [
                'name' => 'Invalid District Building',
                'community_id' => $community->id,
                'city_id' => $cityA->id,
                'district_id' => $districtFromCityB->id,
                'no_floors' => 3,
            ]);

        $response->assertRedirect(self::BUILDINGS_CREATE_ROUTE);
        $response->assertSessionHasErrors(['district_id']);
        $this->assertDatabaseMissing('buildings', [
            'name' => 'Invalid District Building',
        ]);
    }
}
