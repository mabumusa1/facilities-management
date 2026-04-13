<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\City;
use App\Models\Community;
use App\Models\District;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuildingEntityTest extends TestCase
{
    use RefreshDatabase;

    // ==========================================
    // Model Attribute Tests
    // ==========================================

    public function test_building_has_correct_fillable_attributes(): void
    {
        $tenant = Tenant::factory()->create();
        $community = Community::factory()->forTenant($tenant)->create();
        $city = City::factory()->create();
        $district = District::factory()->create();

        $building = Building::create([
            'tenant_id' => $tenant->id,
            'community_id' => $community->id,
            'name' => 'Test Building',
            'city_id' => $city->id,
            'district_id' => $district->id,
            'no_floors' => 10,
            'year_built' => 2020,
            'status' => 'active',
        ]);

        $this->assertEquals('Test Building', $building->name);
        $this->assertEquals($tenant->id, $building->tenant_id);
        $this->assertEquals($community->id, $building->community_id);
        $this->assertEquals($city->id, $building->city_id);
        $this->assertEquals($district->id, $building->district_id);
        $this->assertEquals(10, $building->no_floors);
        $this->assertEquals(2020, $building->year_built);
        $this->assertEquals('active', $building->status);
    }

    public function test_building_casts_integer_attributes(): void
    {
        $building = Building::factory()->create([
            'no_floors' => '15',
            'year_built' => '2015',
        ]);

        $this->assertIsInt($building->no_floors);
        $this->assertIsInt($building->year_built);
        $this->assertEquals(15, $building->no_floors);
        $this->assertEquals(2015, $building->year_built);
    }

    public function test_building_casts_map_as_array(): void
    {
        $building = Building::factory()->create([
            'map' => ['latitude' => 24.7136, 'longitude' => 46.6753],
        ]);

        $this->assertIsArray($building->map);
        $this->assertEquals(24.7136, $building->map['latitude']);
        $this->assertEquals(46.6753, $building->map['longitude']);
    }

    public function test_building_has_status_constants(): void
    {
        $this->assertEquals('active', Building::STATUS_ACTIVE);
        $this->assertEquals('inactive', Building::STATUS_INACTIVE);
    }

    public function test_building_statuses_method_returns_all_statuses(): void
    {
        $statuses = Building::statuses();

        $this->assertIsArray($statuses);
        $this->assertCount(2, $statuses);
        $this->assertContains('active', $statuses);
        $this->assertContains('inactive', $statuses);
    }

    // ==========================================
    // Relationship Tests
    // ==========================================

    public function test_building_belongs_to_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $building = Building::factory()->forTenant($tenant)->create();

        $this->assertInstanceOf(Tenant::class, $building->tenant);
        $this->assertEquals($tenant->id, $building->tenant->id);
    }

    public function test_building_belongs_to_community(): void
    {
        $community = Community::factory()->create();
        $building = Building::factory()->forCommunity($community)->create();

        $this->assertInstanceOf(Community::class, $building->community);
        $this->assertEquals($community->id, $building->community->id);
    }

    public function test_building_belongs_to_city(): void
    {
        $city = City::factory()->create();
        $building = Building::factory()->inCity($city)->create();

        $this->assertInstanceOf(City::class, $building->city);
        $this->assertEquals($city->id, $building->city->id);
    }

    public function test_building_belongs_to_district(): void
    {
        $district = District::factory()->create();
        $building = Building::factory()->inDistrict($district)->create();

        $this->assertInstanceOf(District::class, $building->district);
        $this->assertEquals($district->id, $building->district->id);
    }

    public function test_building_can_have_null_city_and_district(): void
    {
        $building = Building::factory()->create([
            'city_id' => null,
            'district_id' => null,
        ]);

        $this->assertNull($building->city);
        $this->assertNull($building->district);
    }

    public function test_building_has_many_units(): void
    {
        $building = Building::factory()->create();
        Unit::factory()->count(3)->create(['building_id' => $building->id]);

        $this->assertCount(3, $building->units);
        $this->assertInstanceOf(Unit::class, $building->units->first());
    }

    // ==========================================
    // Scope Tests
    // ==========================================

    public function test_building_active_scope(): void
    {
        Building::factory()->count(3)->create(['status' => 'active']);
        Building::factory()->count(2)->create(['status' => 'inactive']);

        $activeBuildings = Building::active()->get();

        $this->assertCount(3, $activeBuildings);
    }

    public function test_building_inactive_scope(): void
    {
        Building::factory()->count(3)->create(['status' => 'active']);
        Building::factory()->count(2)->create(['status' => 'inactive']);

        $inactiveBuildings = Building::inactive()->get();

        $this->assertCount(2, $inactiveBuildings);
    }

    public function test_building_for_tenant_scope(): void
    {
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();

        Building::factory()->count(3)->forTenant($tenant1)->create();
        Building::factory()->count(2)->forTenant($tenant2)->create();

        $tenant1Buildings = Building::forTenant($tenant1)->get();
        $tenant2Buildings = Building::forTenant($tenant2->id)->get();

        $this->assertCount(3, $tenant1Buildings);
        $this->assertCount(2, $tenant2Buildings);
    }

    public function test_building_for_community_scope(): void
    {
        $community1 = Community::factory()->create();
        $community2 = Community::factory()->create();

        Building::factory()->count(3)->forCommunity($community1)->create();
        Building::factory()->count(2)->forCommunity($community2)->create();

        $community1Buildings = Building::forCommunity($community1)->get();
        $community2Buildings = Building::forCommunity($community2->id)->get();

        $this->assertCount(3, $community1Buildings);
        $this->assertCount(2, $community2Buildings);
    }

    public function test_building_in_city_scope(): void
    {
        $city1 = City::factory()->create();
        $city2 = City::factory()->create();

        Building::factory()->count(3)->inCity($city1)->create();
        Building::factory()->count(2)->inCity($city2)->create();

        $city1Buildings = Building::inCity($city1)->get();
        $city2Buildings = Building::inCity($city2->id)->get();

        $this->assertCount(3, $city1Buildings);
        $this->assertCount(2, $city2Buildings);
    }

    public function test_building_in_district_scope(): void
    {
        $district1 = District::factory()->create();
        $district2 = District::factory()->create();

        Building::factory()->count(3)->inDistrict($district1)->create();
        Building::factory()->count(2)->inDistrict($district2)->create();

        $district1Buildings = Building::inDistrict($district1)->get();
        $district2Buildings = Building::inDistrict($district2->id)->get();

        $this->assertCount(3, $district1Buildings);
        $this->assertCount(2, $district2Buildings);
    }

    // ==========================================
    // Helper Method Tests
    // ==========================================

    public function test_building_is_active_method(): void
    {
        $activeBuilding = Building::factory()->active()->create();
        $inactiveBuilding = Building::factory()->inactive()->create();

        $this->assertTrue($activeBuilding->isActive());
        $this->assertFalse($inactiveBuilding->isActive());
    }

    public function test_building_is_inactive_method(): void
    {
        $activeBuilding = Building::factory()->active()->create();
        $inactiveBuilding = Building::factory()->inactive()->create();

        $this->assertFalse($activeBuilding->isInactive());
        $this->assertTrue($inactiveBuilding->isInactive());
    }

    public function test_building_activate_method(): void
    {
        $building = Building::factory()->inactive()->create();

        $this->assertTrue($building->isInactive());

        $building->activate();

        $this->assertTrue($building->isActive());
    }

    public function test_building_deactivate_method(): void
    {
        $building = Building::factory()->active()->create();

        $this->assertTrue($building->isActive());

        $building->deactivate();

        $this->assertTrue($building->isInactive());
    }

    public function test_building_units_count_attribute(): void
    {
        $building = Building::factory()->create();
        Unit::factory()->count(5)->create(['building_id' => $building->id]);

        $this->assertEquals(5, $building->units_count);
    }

    // ==========================================
    // Soft Delete Tests
    // ==========================================

    public function test_building_uses_soft_deletes(): void
    {
        $building = Building::factory()->create();

        $building->delete();

        $this->assertSoftDeleted('buildings', ['id' => $building->id]);
        $this->assertNull(Building::find($building->id));
        $this->assertNotNull(Building::withTrashed()->find($building->id));
    }

    public function test_building_can_be_restored(): void
    {
        $building = Building::factory()->create();
        $building->delete();

        $this->assertSoftDeleted('buildings', ['id' => $building->id]);

        $building->restore();

        $this->assertNotSoftDeleted('buildings', ['id' => $building->id]);
        $this->assertNotNull(Building::find($building->id));
    }

    // ==========================================
    // Factory State Tests
    // ==========================================

    public function test_factory_creates_valid_building(): void
    {
        $building = Building::factory()->create();

        $this->assertNotNull($building->id);
        $this->assertNotEmpty($building->name);
        $this->assertNotNull($building->tenant_id);
        $this->assertNotNull($building->community_id);
        $this->assertEquals('active', $building->status);
    }

    public function test_factory_active_state(): void
    {
        $building = Building::factory()->active()->create();

        $this->assertEquals('active', $building->status);
    }

    public function test_factory_inactive_state(): void
    {
        $building = Building::factory()->inactive()->create();

        $this->assertEquals('inactive', $building->status);
    }

    public function test_factory_for_tenant_state(): void
    {
        $tenant = Tenant::factory()->create();
        $building = Building::factory()->forTenant($tenant)->create();

        $this->assertEquals($tenant->id, $building->tenant_id);
    }

    public function test_factory_for_community_state(): void
    {
        $community = Community::factory()->create();
        $building = Building::factory()->forCommunity($community)->create();

        $this->assertEquals($community->id, $building->community_id);
        $this->assertEquals($community->tenant_id, $building->tenant_id);
    }

    public function test_factory_in_city_state(): void
    {
        $city = City::factory()->create();
        $building = Building::factory()->inCity($city)->create();

        $this->assertEquals($city->id, $building->city_id);
    }

    public function test_factory_in_district_state(): void
    {
        $district = District::factory()->create();
        $building = Building::factory()->inDistrict($district)->create();

        $this->assertEquals($district->id, $building->district_id);
    }

    public function test_factory_with_floors_state(): void
    {
        $building = Building::factory()->withFloors(25)->create();

        $this->assertEquals(25, $building->no_floors);
    }

    public function test_factory_built_in_state(): void
    {
        $building = Building::factory()->builtIn(2018)->create();

        $this->assertEquals(2018, $building->year_built);
    }

    public function test_factory_with_map_state(): void
    {
        $building = Building::factory()->withMap(24.7136, 46.6753)->create();

        $this->assertEquals(24.7136, $building->map['latitude']);
        $this->assertEquals(46.6753, $building->map['longitude']);
    }

    public function test_factory_tower_state(): void
    {
        $building = Building::factory()->tower()->create();

        $this->assertStringContainsString('Tower', $building->name);
        $this->assertGreaterThanOrEqual(20, $building->no_floors);
    }

    public function test_factory_villa_state(): void
    {
        $building = Building::factory()->villa()->create();

        $this->assertStringContainsString('Villa', $building->name);
        $this->assertLessThanOrEqual(3, $building->no_floors);
    }

    // ==========================================
    // Multi-tenant Isolation Tests
    // ==========================================

    public function test_buildings_are_tenant_isolated(): void
    {
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();

        $building1 = Building::factory()->forTenant($tenant1)->create(['name' => 'Tenant 1 Building']);
        $building2 = Building::factory()->forTenant($tenant2)->create(['name' => 'Tenant 2 Building']);

        $tenant1Buildings = Building::forTenant($tenant1)->get();
        $tenant2Buildings = Building::forTenant($tenant2)->get();

        $this->assertCount(1, $tenant1Buildings);
        $this->assertCount(1, $tenant2Buildings);
        $this->assertEquals('Tenant 1 Building', $tenant1Buildings->first()->name);
        $this->assertEquals('Tenant 2 Building', $tenant2Buildings->first()->name);
    }

    // ==========================================
    // Property Hierarchy Tests
    // ==========================================

    public function test_building_belongs_to_community_hierarchy(): void
    {
        $tenant = Tenant::factory()->create();
        $community = Community::factory()->forTenant($tenant)->create();
        $building = Building::factory()->forCommunity($community)->create();

        $this->assertEquals($community->id, $building->community_id);
        $this->assertEquals($tenant->id, $building->tenant_id);
        $this->assertInstanceOf(Community::class, $building->community);
        $this->assertInstanceOf(Tenant::class, $building->community->tenant);
        $this->assertEquals($tenant->id, $building->community->tenant->id);
    }

    public function test_community_has_many_buildings(): void
    {
        $community = Community::factory()->create();
        Building::factory()->count(3)->forCommunity($community)->create();

        $this->assertCount(3, $community->buildings);
    }

    // ==========================================
    // Cascade Delete Tests
    // ==========================================

    public function test_building_is_deleted_when_community_is_deleted(): void
    {
        $community = Community::factory()->create();
        $building = Building::factory()->forCommunity($community)->create();

        $community->forceDelete();

        $this->assertDatabaseMissing('buildings', ['id' => $building->id]);
    }

    public function test_building_is_deleted_when_tenant_is_deleted(): void
    {
        $tenant = Tenant::factory()->create();
        $building = Building::factory()->forTenant($tenant)->create();

        $tenant->forceDelete();

        $this->assertDatabaseMissing('buildings', ['id' => $building->id]);
    }
}
