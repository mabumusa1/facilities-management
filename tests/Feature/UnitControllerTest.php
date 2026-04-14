<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Community;
use App\Models\Tenant;
use App\Models\UnitCategory;
use App\Models\UnitType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class UnitControllerTest extends TestCase
{
    use RefreshDatabase;

    private const UNITS_ROUTE = '/units';

    private const UNITS_CREATE_ROUTE = '/units/create';

    private const VALID_UNIT_NAME = 'Unit 101';

    private const INVALID_RELATIONS_UNIT_NAME = 'Invalid Relations Unit';

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

    public function test_create_displays_unit_form_options(): void
    {
        $community = Community::factory()->forTenant($this->tenant)->create();
        Building::factory()->forCommunity($community)->forTenant($this->tenant)->create();
        $category = UnitCategory::factory()->create();
        UnitType::factory()->forCategory($category)->create();

        $response = $this->actingAs($this->user)->get(self::UNITS_CREATE_ROUTE);

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('properties/units/create')
            ->has('communities', 1)
            ->has('buildings', 1)
            ->has('categories', 1)
            ->has('types', 1)
        );
    }

    public function test_store_creates_unit_when_dependencies_are_valid(): void
    {
        $community = Community::factory()->forTenant($this->tenant)->create();
        $building = Building::factory()->forCommunity($community)->forTenant($this->tenant)->create();
        $category = UnitCategory::factory()->create();
        $type = UnitType::factory()->forCategory($category)->create();

        $response = $this->actingAs($this->user)->post(self::UNITS_ROUTE, [
            'name' => self::VALID_UNIT_NAME,
            'community_id' => $community->id,
            'building_id' => $building->id,
            'unit_category_id' => $category->id,
            'unit_type_id' => $type->id,
            'floor_no' => 4,
            'net_area' => 120,
            'year_built' => 2020,
            'market_rent' => 15000,
            'is_marketplace' => true,
            'is_off_plan_sale' => false,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('units', [
            'tenant_id' => $this->tenant->id,
            'name' => self::VALID_UNIT_NAME,
            'community_id' => $community->id,
            'building_id' => $building->id,
            'unit_category_id' => $category->id,
            'unit_type_id' => $type->id,
        ]);
    }

    public function test_store_validates_dynamic_field_dependencies(): void
    {
        $communityA = Community::factory()->forTenant($this->tenant)->create();
        $communityB = Community::factory()->forTenant($this->tenant)->create();
        $buildingFromCommunityB = Building::factory()
            ->forCommunity($communityB)
            ->forTenant($this->tenant)
            ->create();

        $categoryA = UnitCategory::factory()->create();
        $categoryB = UnitCategory::factory()->create();
        $typeFromCategoryB = UnitType::factory()->forCategory($categoryB)->create();

        $response = $this->actingAs($this->user)
            ->from(self::UNITS_CREATE_ROUTE)
            ->post(self::UNITS_ROUTE, [
                'name' => self::INVALID_RELATIONS_UNIT_NAME,
                'community_id' => $communityA->id,
                'building_id' => $buildingFromCommunityB->id,
                'unit_category_id' => $categoryA->id,
                'unit_type_id' => $typeFromCategoryB->id,
                'is_marketplace' => false,
                'is_off_plan_sale' => false,
            ]);

        $response->assertRedirect(self::UNITS_CREATE_ROUTE);
        $response->assertSessionHasErrors(['building_id', 'unit_type_id']);
        $this->assertDatabaseMissing('units', [
            'name' => self::INVALID_RELATIONS_UNIT_NAME,
        ]);
    }

    public function test_store_rejects_community_from_a_different_tenant(): void
    {
        $otherTenant = Tenant::factory()->create();
        $communityFromAnotherTenant = Community::factory()->forTenant($otherTenant)->create();
        $category = UnitCategory::factory()->create();
        $type = UnitType::factory()->forCategory($category)->create();

        $response = $this->actingAs($this->user)
            ->from(self::UNITS_CREATE_ROUTE)
            ->post(self::UNITS_ROUTE, [
                'name' => 'Cross Tenant Unit',
                'community_id' => $communityFromAnotherTenant->id,
                'unit_category_id' => $category->id,
                'unit_type_id' => $type->id,
                'is_marketplace' => false,
                'is_off_plan_sale' => false,
            ]);

        $response->assertRedirect(self::UNITS_CREATE_ROUTE);
        $response->assertSessionHasErrors(['community_id']);
        $this->assertDatabaseMissing('units', [
            'name' => 'Cross Tenant Unit',
        ]);
    }
}
