<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\City;
use App\Models\Community;
use App\Models\District;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\UnitCategory;
use App\Models\UnitType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitEntityTest extends TestCase
{
    use RefreshDatabase;

    // ==========================================
    // Basic Model Tests
    // ==========================================

    public function test_unit_can_be_created_with_factory(): void
    {
        $unit = Unit::factory()->create();

        $this->assertDatabaseHas('units', [
            'id' => $unit->id,
            'name' => $unit->name,
        ]);
    }

    public function test_unit_has_required_attributes(): void
    {
        $unit = Unit::factory()->create([
            'name' => 'Unit 101',
            'floor_no' => 5,
            'net_area' => 150.50,
            'market_rent' => 5000.00,
        ]);

        $this->assertEquals('Unit 101', $unit->name);
        $this->assertEquals(5, $unit->floor_no);
        $this->assertEquals(150.50, $unit->net_area);
        $this->assertEquals(5000.00, $unit->market_rent);
    }

    public function test_unit_has_fillable_attributes(): void
    {
        $unit = new Unit;
        $fillable = $unit->getFillable();

        $this->assertContains('tenant_id', $fillable);
        $this->assertContains('community_id', $fillable);
        $this->assertContains('building_id', $fillable);
        $this->assertContains('unit_category_id', $fillable);
        $this->assertContains('unit_type_id', $fillable);
        $this->assertContains('status_id', $fillable);
        $this->assertContains('name', $fillable);
        $this->assertContains('floor_no', $fillable);
        $this->assertContains('net_area', $fillable);
        $this->assertContains('market_rent', $fillable);
        $this->assertContains('is_marketplace', $fillable);
        $this->assertContains('is_off_plan_sale', $fillable);
    }

    public function test_unit_casts_attributes_correctly(): void
    {
        $unit = Unit::factory()->create([
            'floor_no' => '10',
            'net_area' => '200.50',
            'year_built' => '2020',
            'market_rent' => '15000.75',
            'is_marketplace' => 1,
            'is_off_plan_sale' => 0,
            'map' => ['latitude' => 25.2048, 'longitude' => 55.2708],
            'photos' => ['photo1.jpg', 'photo2.jpg'],
        ]);

        $this->assertIsInt($unit->floor_no);
        $this->assertIsString($unit->net_area); // decimal cast returns string
        $this->assertIsInt($unit->year_built);
        $this->assertIsString($unit->market_rent); // decimal cast returns string
        $this->assertIsBool($unit->is_marketplace);
        $this->assertIsBool($unit->is_off_plan_sale);
        $this->assertIsArray($unit->map);
        $this->assertIsArray($unit->photos);
    }

    public function test_unit_uses_soft_deletes(): void
    {
        $unit = Unit::factory()->create();
        $unitId = $unit->id;

        $unit->delete();

        $this->assertSoftDeleted('units', ['id' => $unitId]);
        $this->assertNotNull(Unit::withTrashed()->find($unitId));
    }

    public function test_deleted_unit_can_be_restored(): void
    {
        $unit = Unit::factory()->create();
        $unit->delete();

        $unit->restore();

        $this->assertDatabaseHas('units', [
            'id' => $unit->id,
            'deleted_at' => null,
        ]);
    }

    // ==========================================
    // Relationship Tests
    // ==========================================

    public function test_unit_belongs_to_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $unit = Unit::factory()->forTenant($tenant)->create();

        $this->assertTrue($unit->tenant->is($tenant));
        $this->assertInstanceOf(Tenant::class, $unit->tenant);
    }

    public function test_unit_belongs_to_community(): void
    {
        $community = Community::factory()->create();
        $unit = Unit::factory()->forCommunity($community)->create();

        $this->assertTrue($unit->community->is($community));
        $this->assertInstanceOf(Community::class, $unit->community);
    }

    public function test_unit_belongs_to_building(): void
    {
        $building = Building::factory()->create();
        $unit = Unit::factory()->forBuilding($building)->create();

        $this->assertTrue($unit->building->is($building));
        $this->assertInstanceOf(Building::class, $unit->building);
    }

    public function test_unit_can_exist_without_building(): void
    {
        $unit = Unit::factory()->withoutBuilding()->create();

        $this->assertNull($unit->building_id);
        $this->assertNull($unit->building);
    }

    public function test_unit_belongs_to_category(): void
    {
        $category = UnitCategory::factory()->create();
        $unit = Unit::factory()->withCategory($category)->create();

        $this->assertTrue($unit->category->is($category));
        $this->assertInstanceOf(UnitCategory::class, $unit->category);
    }

    public function test_unit_belongs_to_type(): void
    {
        $type = UnitType::factory()->create();
        $unit = Unit::factory()->withType($type)->create();

        $this->assertTrue($unit->type->is($type));
        $this->assertInstanceOf(UnitType::class, $unit->type);
    }

    public function test_unit_belongs_to_status(): void
    {
        $status = Status::factory()->forDomain(Status::DOMAIN_UNIT)->create();
        $unit = Unit::factory()->withStatus($status)->create();

        $this->assertTrue($unit->status->is($status));
        $this->assertInstanceOf(Status::class, $unit->status);
    }

    public function test_unit_can_exist_without_status(): void
    {
        $unit = Unit::factory()->create(['status_id' => null]);

        $this->assertNull($unit->status_id);
        $this->assertNull($unit->status);
    }

    public function test_unit_belongs_to_city(): void
    {
        $city = City::factory()->create();
        $unit = Unit::factory()->inCity($city)->create();

        $this->assertTrue($unit->city->is($city));
        $this->assertInstanceOf(City::class, $unit->city);
    }

    public function test_unit_belongs_to_district(): void
    {
        $district = District::factory()->create();
        $unit = Unit::factory()->inDistrict($district)->create();

        $this->assertTrue($unit->district->is($district));
        $this->assertInstanceOf(District::class, $unit->district);
    }

    // ==========================================
    // Scope Tests
    // ==========================================

    public function test_scope_for_tenant_filters_correctly(): void
    {
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();

        Unit::factory()->count(3)->forTenant($tenant1)->create();
        Unit::factory()->count(2)->forTenant($tenant2)->create();

        $units = Unit::forTenant($tenant1)->get();

        $this->assertCount(3, $units);
        $units->each(fn ($unit) => $this->assertEquals($tenant1->id, $unit->tenant_id));
    }

    public function test_scope_for_community_filters_correctly(): void
    {
        $community1 = Community::factory()->create();
        $community2 = Community::factory()->create();

        Unit::factory()->count(2)->forCommunity($community1)->create();
        Unit::factory()->count(4)->forCommunity($community2)->create();

        $units = Unit::forCommunity($community1)->get();

        $this->assertCount(2, $units);
        $units->each(fn ($unit) => $this->assertEquals($community1->id, $unit->community_id));
    }

    public function test_scope_for_building_filters_correctly(): void
    {
        $building1 = Building::factory()->create();
        $building2 = Building::factory()->create();

        Unit::factory()->count(5)->forBuilding($building1)->create();
        Unit::factory()->count(3)->forBuilding($building2)->create();

        $units = Unit::forBuilding($building1)->get();

        $this->assertCount(5, $units);
        $units->each(fn ($unit) => $this->assertEquals($building1->id, $unit->building_id));
    }

    public function test_scope_for_category_filters_correctly(): void
    {
        $category = UnitCategory::factory()->create();
        Unit::factory()->count(3)->withCategory($category)->create();
        Unit::factory()->count(2)->create(); // Different category

        $units = Unit::forCategory($category)->get();

        $this->assertCount(3, $units);
    }

    public function test_scope_for_type_filters_correctly(): void
    {
        $type = UnitType::factory()->create();
        Unit::factory()->count(4)->withType($type)->create();
        Unit::factory()->count(2)->create(); // Different type

        $units = Unit::forType($type)->get();

        $this->assertCount(4, $units);
    }

    public function test_scope_with_status_filters_correctly(): void
    {
        $status = Status::factory()->forDomain(Status::DOMAIN_UNIT)->create();
        Unit::factory()->count(2)->withStatus($status)->create();
        Unit::factory()->count(3)->create(); // No status

        $units = Unit::withStatus($status)->get();

        $this->assertCount(2, $units);
    }

    public function test_scope_in_city_filters_correctly(): void
    {
        $city = City::factory()->create();
        Unit::factory()->count(3)->inCity($city)->create();
        Unit::factory()->count(2)->create(); // Different city

        $units = Unit::inCity($city)->get();

        $this->assertCount(3, $units);
    }

    public function test_scope_in_district_filters_correctly(): void
    {
        $district = District::factory()->create();
        Unit::factory()->count(2)->inDistrict($district)->create();
        Unit::factory()->count(4)->create(); // Different district

        $units = Unit::inDistrict($district)->get();

        $this->assertCount(2, $units);
    }

    public function test_scope_marketplace_filters_correctly(): void
    {
        Unit::factory()->count(3)->marketplace()->create();
        Unit::factory()->count(2)->create(['is_marketplace' => false]);

        $units = Unit::marketplace()->get();

        $this->assertCount(3, $units);
        $units->each(fn ($unit) => $this->assertTrue($unit->is_marketplace));
    }

    public function test_scope_not_marketplace_filters_correctly(): void
    {
        Unit::factory()->count(2)->marketplace()->create();
        Unit::factory()->count(4)->create(['is_marketplace' => false]);

        $units = Unit::notMarketplace()->get();

        $this->assertCount(4, $units);
        $units->each(fn ($unit) => $this->assertFalse($unit->is_marketplace));
    }

    public function test_scope_off_plan_sale_filters_correctly(): void
    {
        Unit::factory()->count(2)->offPlanSale()->create();
        Unit::factory()->count(3)->create(['is_off_plan_sale' => false]);

        $units = Unit::offPlanSale()->get();

        $this->assertCount(2, $units);
        $units->each(fn ($unit) => $this->assertTrue($unit->is_off_plan_sale));
    }

    public function test_scope_on_floor_filters_correctly(): void
    {
        Unit::factory()->count(3)->onFloor(5)->create();
        Unit::factory()->count(2)->onFloor(10)->create();

        $units = Unit::onFloor(5)->get();

        $this->assertCount(3, $units);
        $units->each(fn ($unit) => $this->assertEquals(5, $unit->floor_no));
    }

    public function test_scope_min_area_filters_correctly(): void
    {
        Unit::factory()->withArea(100)->create();
        Unit::factory()->withArea(200)->create();
        Unit::factory()->withArea(50)->create();

        $units = Unit::minArea(100)->get();

        $this->assertCount(2, $units);
    }

    public function test_scope_max_area_filters_correctly(): void
    {
        Unit::factory()->withArea(100)->create();
        Unit::factory()->withArea(200)->create();
        Unit::factory()->withArea(50)->create();

        $units = Unit::maxArea(100)->get();

        $this->assertCount(2, $units);
    }

    public function test_scope_area_between_filters_correctly(): void
    {
        Unit::factory()->withArea(50)->create();
        Unit::factory()->withArea(100)->create();
        Unit::factory()->withArea(150)->create();
        Unit::factory()->withArea(200)->create();

        $units = Unit::areaBetween(75, 175)->get();

        $this->assertCount(2, $units);
    }

    public function test_scope_min_rent_filters_correctly(): void
    {
        Unit::factory()->withRent(5000)->create();
        Unit::factory()->withRent(10000)->create();
        Unit::factory()->withRent(2000)->create();

        $units = Unit::minRent(5000)->get();

        $this->assertCount(2, $units);
    }

    public function test_scope_max_rent_filters_correctly(): void
    {
        Unit::factory()->withRent(5000)->create();
        Unit::factory()->withRent(10000)->create();
        Unit::factory()->withRent(2000)->create();

        $units = Unit::maxRent(5000)->get();

        $this->assertCount(2, $units);
    }

    public function test_scope_rent_between_filters_correctly(): void
    {
        Unit::factory()->withRent(2000)->create();
        Unit::factory()->withRent(5000)->create();
        Unit::factory()->withRent(8000)->create();
        Unit::factory()->withRent(12000)->create();

        $units = Unit::rentBetween(4000, 9000)->get();

        $this->assertCount(2, $units);
    }

    // ==========================================
    // Helper Method Tests
    // ==========================================

    public function test_is_on_marketplace_returns_correct_value(): void
    {
        $marketplaceUnit = Unit::factory()->marketplace()->create();
        $normalUnit = Unit::factory()->create(['is_marketplace' => false]);

        $this->assertTrue($marketplaceUnit->isOnMarketplace());
        $this->assertFalse($normalUnit->isOnMarketplace());
    }

    public function test_is_off_plan_sale_returns_correct_value(): void
    {
        $offPlanUnit = Unit::factory()->offPlanSale()->create();
        $normalUnit = Unit::factory()->create(['is_off_plan_sale' => false]);

        $this->assertTrue($offPlanUnit->isOffPlanSale());
        $this->assertFalse($normalUnit->isOffPlanSale());
    }

    public function test_list_on_marketplace_updates_unit(): void
    {
        $unit = Unit::factory()->create(['is_marketplace' => false]);

        $result = $unit->listOnMarketplace();

        $this->assertTrue($result);
        $this->assertTrue($unit->fresh()->is_marketplace);
    }

    public function test_remove_from_marketplace_updates_unit(): void
    {
        $unit = Unit::factory()->marketplace()->create();

        $result = $unit->removeFromMarketplace();

        $this->assertTrue($result);
        $this->assertFalse($unit->fresh()->is_marketplace);
    }

    public function test_full_address_attribute_includes_all_parts(): void
    {
        $community = Community::factory()->create(['name' => 'Green Valley']);
        $building = Building::factory()->forCommunity($community)->create(['name' => 'Tower A']);
        $unit = Unit::factory()->forBuilding($building)->create(['name' => 'Unit 101']);

        $this->assertEquals('Unit 101, Tower A, Green Valley', $unit->full_address);
    }

    public function test_full_address_attribute_without_building(): void
    {
        $community = Community::factory()->create(['name' => 'Green Valley']);
        $unit = Unit::factory()->forCommunity($community)->withoutBuilding()->create(['name' => 'Villa 5']);

        $this->assertEquals('Villa 5, Green Valley', $unit->full_address);
    }

    public function test_has_photos_returns_correct_value(): void
    {
        $unitWithPhotos = Unit::factory()->withPhotos(['photo1.jpg', 'photo2.jpg'])->create();
        $unitWithoutPhotos = Unit::factory()->create(['photos' => null]);
        $unitWithEmptyPhotos = Unit::factory()->create(['photos' => []]);

        $this->assertTrue($unitWithPhotos->hasPhotos());
        $this->assertFalse($unitWithoutPhotos->hasPhotos());
        $this->assertFalse($unitWithEmptyPhotos->hasPhotos());
    }

    public function test_photo_count_attribute_returns_correct_count(): void
    {
        $unitWithPhotos = Unit::factory()->withPhotos(['photo1.jpg', 'photo2.jpg', 'photo3.jpg'])->create();
        $unitWithoutPhotos = Unit::factory()->create(['photos' => null]);

        $this->assertEquals(3, $unitWithPhotos->photo_count);
        $this->assertEquals(0, $unitWithoutPhotos->photo_count);
    }

    public function test_has_map_coordinates_returns_correct_value(): void
    {
        $unitWithMap = Unit::factory()->withMap(25.2048, 55.2708)->create();
        $unitWithoutMap = Unit::factory()->create(['map' => null]);
        $unitWithIncompleteMap = Unit::factory()->create(['map' => ['latitude' => 25.2048]]);

        $this->assertTrue($unitWithMap->hasMapCoordinates());
        $this->assertFalse($unitWithoutMap->hasMapCoordinates());
        $this->assertFalse($unitWithIncompleteMap->hasMapCoordinates());
    }

    // ==========================================
    // Factory State Tests
    // ==========================================

    public function test_factory_studio_state(): void
    {
        $unit = Unit::factory()->studio()->create();

        $this->assertStringContainsString('Studio', $unit->name);
        $this->assertGreaterThanOrEqual(25, $unit->net_area);
        $this->assertLessThanOrEqual(50, $unit->net_area);
    }

    public function test_factory_penthouse_state(): void
    {
        $unit = Unit::factory()->penthouse()->create();

        $this->assertStringContainsString('Penthouse', $unit->name);
        $this->assertGreaterThanOrEqual(200, $unit->net_area);
        $this->assertGreaterThanOrEqual(30, $unit->floor_no);
    }

    public function test_factory_villa_state(): void
    {
        $unit = Unit::factory()->villa()->create();

        $this->assertStringContainsString('Villa', $unit->name);
        $this->assertGreaterThanOrEqual(300, $unit->net_area);
        $this->assertEquals(1, $unit->floor_no);
    }

    public function test_factory_commercial_state(): void
    {
        $unit = Unit::factory()->commercial()->create();

        $this->assertStringContainsString('Shop', $unit->name);
        $this->assertLessThanOrEqual(3, $unit->floor_no);
    }

    public function test_factory_with_description_state(): void
    {
        $description = 'A beautiful apartment with sea view';
        $unit = Unit::factory()->withDescription($description)->create();

        $this->assertEquals($description, $unit->about);
    }

    // ==========================================
    // Property Hierarchy Tests
    // ==========================================

    public function test_property_hierarchy_tenant_community_building_unit(): void
    {
        $tenant = Tenant::factory()->create();
        $community = Community::factory()->forTenant($tenant)->create();
        $building = Building::factory()->forCommunity($community)->create();
        $unit = Unit::factory()->forBuilding($building)->create();

        // Verify the hierarchy
        $this->assertEquals($tenant->id, $unit->tenant_id);
        $this->assertEquals($community->id, $unit->community_id);
        $this->assertEquals($building->id, $unit->building_id);

        // Verify through relationships
        $this->assertTrue($unit->tenant->is($tenant));
        $this->assertTrue($unit->community->is($community));
        $this->assertTrue($unit->building->is($building));
        $this->assertTrue($unit->building->community->is($community));
        $this->assertTrue($unit->community->tenant->is($tenant));
    }

    public function test_building_has_many_units(): void
    {
        $building = Building::factory()->create();
        Unit::factory()->count(5)->forBuilding($building)->create();

        $this->assertCount(5, $building->units);
        $building->units->each(fn ($unit) => $this->assertEquals($building->id, $unit->building_id));
    }

    // ==========================================
    // Multi-Tenant Isolation Tests
    // ==========================================

    public function test_units_are_isolated_by_tenant(): void
    {
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();

        Unit::factory()->count(4)->forTenant($tenant1)->create();
        Unit::factory()->count(3)->forTenant($tenant2)->create();

        $tenant1Units = Unit::forTenant($tenant1)->get();
        $tenant2Units = Unit::forTenant($tenant2)->get();

        $this->assertCount(4, $tenant1Units);
        $this->assertCount(3, $tenant2Units);

        // Ensure no cross-tenant data
        $tenant1Units->each(fn ($unit) => $this->assertEquals($tenant1->id, $unit->tenant_id));
        $tenant2Units->each(fn ($unit) => $this->assertEquals($tenant2->id, $unit->tenant_id));
    }

    // ==========================================
    // Cascade Delete Tests
    // ==========================================

    public function test_units_are_deleted_when_community_is_deleted(): void
    {
        $community = Community::factory()->create();
        $unit = Unit::factory()->forCommunity($community)->withoutBuilding()->create();

        $community->forceDelete();

        $this->assertDatabaseMissing('units', ['id' => $unit->id]);
    }

    public function test_units_are_deleted_when_tenant_is_deleted(): void
    {
        $tenant = Tenant::factory()->create();
        $unit = Unit::factory()->forTenant($tenant)->create();

        $tenant->forceDelete();

        $this->assertDatabaseMissing('units', ['id' => $unit->id]);
    }

    public function test_unit_building_id_is_nulled_when_building_is_deleted(): void
    {
        $building = Building::factory()->create();
        $unit = Unit::factory()->forBuilding($building)->create();

        $building->forceDelete();

        $this->assertDatabaseHas('units', [
            'id' => $unit->id,
            'building_id' => null,
        ]);
    }

    // ==========================================
    // Chained Scope Tests
    // ==========================================

    public function test_multiple_scopes_can_be_chained(): void
    {
        $tenant = Tenant::factory()->create();
        $community = Community::factory()->forTenant($tenant)->create();
        $building = Building::factory()->forCommunity($community)->create();

        // Create various units
        Unit::factory()->forBuilding($building)->marketplace()->onFloor(5)->withArea(150)->create();
        Unit::factory()->forBuilding($building)->marketplace()->onFloor(5)->withArea(200)->create();
        Unit::factory()->forBuilding($building)->onFloor(5)->withArea(150)->create(); // Not marketplace
        Unit::factory()->forBuilding($building)->marketplace()->onFloor(10)->withArea(150)->create(); // Different floor

        $units = Unit::forTenant($tenant)
            ->forCommunity($community)
            ->forBuilding($building)
            ->marketplace()
            ->onFloor(5)
            ->minArea(100)
            ->get();

        $this->assertCount(2, $units);
    }
}
