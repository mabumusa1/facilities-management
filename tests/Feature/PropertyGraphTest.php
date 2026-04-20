<?php

namespace Tests\Feature;

use App\Enums\AdminRole;
use App\Models\Admin;
use App\Models\Building;
use App\Models\City;
use App\Models\Community;
use App\Models\Country;
use App\Models\Currency;
use App\Models\District;
use App\Models\Owner;
use App\Models\Professional;
use App\Models\Resident;
use App\Models\Status;
use App\Models\Unit;
use App\Models\UnitCategory;
use App\Models\UnitType;
use Database\Seeders\ManagerRoleSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PropertyGraphTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function createCommunityWithDeps(): Community
    {
        $country = Country::factory()->create();
        $currency = Currency::factory()->create();
        $city = City::factory()->recycle($country)->create();
        $district = District::factory()->recycle($city)->create();

        return Community::factory()
            ->recycle([$country, $currency, $city, $district])
            ->create();
    }

    public function test_community_belongs_to_country_currency_city_district(): void
    {
        $community = $this->createCommunityWithDeps();

        $this->assertNotNull($community->country);
        $this->assertNotNull($community->currency);
        $this->assertNotNull($community->city);
        $this->assertNotNull($community->district);
    }

    public function test_community_has_many_buildings(): void
    {
        $community = $this->createCommunityWithDeps();
        Building::factory()->count(3)->recycle($community)->create();

        $this->assertCount(3, $community->buildings);
    }

    public function test_building_belongs_to_community(): void
    {
        $community = $this->createCommunityWithDeps();
        $building = Building::factory()->recycle($community)->create();

        $this->assertTrue($building->community->is($community));
    }

    public function test_building_has_many_units(): void
    {
        $community = $this->createCommunityWithDeps();
        $building = Building::factory()->recycle($community)->create();
        $category = UnitCategory::factory()->create();
        $type = UnitType::factory()->recycle($category)->create();
        $status = Status::factory()->create(['type' => 'unit']);

        Unit::factory()->count(2)->create([
            'rf_community_id' => $community->id,
            'rf_building_id' => $building->id,
            'category_id' => $category->id,
            'type_id' => $type->id,
            'status_id' => $status->id,
        ]);

        $this->assertCount(2, $building->units);
    }

    public function test_unit_belongs_to_community_building_category_type_status(): void
    {
        $community = $this->createCommunityWithDeps();
        $building = Building::factory()->recycle($community)->create();
        $category = UnitCategory::factory()->create();
        $type = UnitType::factory()->recycle($category)->create();
        $status = Status::factory()->create(['type' => 'unit']);

        $unit = Unit::factory()->create([
            'rf_community_id' => $community->id,
            'rf_building_id' => $building->id,
            'category_id' => $category->id,
            'type_id' => $type->id,
            'status_id' => $status->id,
        ]);

        $this->assertTrue($unit->community->is($community));
        $this->assertTrue($unit->building->is($building));
        $this->assertTrue($unit->category->is($category));
        $this->assertTrue($unit->type->is($type));
        $this->assertTrue($unit->status->is($status));
    }

    public function test_resident_factory_creates_valid_model(): void
    {
        $resident = Resident::factory()->create();

        $this->assertModelExists($resident);
        $this->assertTrue($resident->active);
        $this->assertFalse($resident->accepted_invite);
    }

    public function test_resident_has_name_accessor(): void
    {
        $resident = Resident::factory()->create([
            'first_name' => 'Ahmed',
            'last_name' => 'Al-Saud',
        ]);

        $this->assertEquals('Ahmed Al-Saud', $resident->name);
    }

    public function test_resident_soft_deletes(): void
    {
        $resident = Resident::factory()->create();
        $resident->delete();

        $this->assertSoftDeleted($resident);
        $this->assertNotNull(Resident::withTrashed()->find($resident->id));
    }

    public function test_owner_factory_creates_valid_model(): void
    {
        $owner = Owner::factory()->create();

        $this->assertModelExists($owner);
        $this->assertTrue($owner->active);
    }

    public function test_owner_soft_deletes(): void
    {
        $owner = Owner::factory()->create();
        $owner->delete();

        $this->assertSoftDeleted($owner);
    }

    public function test_admin_has_role_cast(): void
    {
        $admin = Admin::factory()->create(['role' => AdminRole::Admins]);

        $this->assertEquals(AdminRole::Admins, $admin->role);
    }

    public function test_professional_factory_creates_valid_model(): void
    {
        $professional = Professional::factory()->create();

        $this->assertModelExists($professional);
        $this->assertTrue($professional->active);
    }

    public function test_manager_role_seeder_creates_expected_roles(): void
    {
        $this->seed(ManagerRoleSeeder::class);

        $this->assertDatabaseCount('rf_manager_roles', 5);
        $this->assertDatabaseHas('rf_manager_roles', ['role' => 'Admins']);
        $this->assertDatabaseHas('rf_manager_roles', ['role' => 'serviceManagers']);
    }

    public function test_community_to_building_to_unit_graph_integrity(): void
    {
        $community = $this->createCommunityWithDeps();
        $building = Building::factory()->recycle($community)->create();
        $category = UnitCategory::factory()->create();
        $type = UnitType::factory()->recycle($category)->create();
        $status = Status::factory()->create(['type' => 'unit']);

        $unit = Unit::factory()->create([
            'rf_community_id' => $community->id,
            'rf_building_id' => $building->id,
            'category_id' => $category->id,
            'type_id' => $type->id,
            'status_id' => $status->id,
        ]);

        // Navigate the full graph
        $this->assertTrue($unit->building->community->is($community));
        $this->assertTrue($community->buildings->first()->units->first()->is($unit));
    }

    public function test_unit_without_building(): void
    {
        $community = $this->createCommunityWithDeps();
        $category = UnitCategory::factory()->create();
        $type = UnitType::factory()->recycle($category)->create();
        $status = Status::factory()->create(['type' => 'unit']);

        $unit = Unit::factory()->create([
            'rf_community_id' => $community->id,
            'rf_building_id' => null,
            'category_id' => $category->id,
            'type_id' => $type->id,
            'status_id' => $status->id,
        ]);

        $this->assertNull($unit->building);
        $this->assertTrue($unit->community->is($community));
    }
}
