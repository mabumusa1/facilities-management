<?php

namespace Tests\Feature;

use App\Models\AccountMembership;
use App\Models\Community;
use App\Models\Currency;
use App\Models\Feature;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UnitMetadataTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    private Community $community;

    private Unit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // Create only the needed permissions and a role — avoids running the full RolesSeeder
        $viewPerm = Permission::firstOrCreate(['name' => 'properties.VIEW', 'guard_name' => 'web']);
        $updatePerm = Permission::firstOrCreate(['name' => 'properties.UPDATE', 'guard_name' => 'web']);
        $role = Role::firstOrCreate(['name' => 'accountAdmins', 'guard_name' => 'web']);
        $role->syncPermissions([$viewPerm, $updatePerm]);
        $this->user->assignRole($role);

        $this->tenant = Tenant::create(['name' => 'Unit Metadata Test Account']);

        AccountMembership::create([
            'user_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);

        $status = Status::factory()->create(['type' => 'unit']);

        $this->community = Community::factory()->create([
            'account_tenant_id' => $this->tenant->id,
        ]);

        $this->unit = Unit::factory()->create([
            'rf_community_id' => $this->community->id,
            'account_tenant_id' => $this->tenant->id,
            'status_id' => $status->id,
        ]);
    }

    /**
     * Save rooms, specs, amenities, and pricing in a single PUT request
     * and assert all are persisted correctly.
     */
    public function test_update_saves_rooms_specifications_amenities_and_pricing(): void
    {
        $pool = Feature::factory()->create(['type' => 'amenity', 'name' => 'Pool', 'name_en' => 'Pool']);
        $gym = Feature::factory()->create(['type' => 'amenity', 'name' => 'Gym', 'name_en' => 'Gym']);
        $currency = Currency::factory()->create(['code' => 'SAR', 'symbol' => '﷼']);

        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/units/{$this->unit->id}", [
                'name' => $this->unit->name,
                'rf_community_id' => $this->community->id,
                'category_id' => $this->unit->category_id,
                'type_id' => $this->unit->type_id,
                'status_id' => $this->unit->status_id,
                'rooms' => [
                    ['name' => 'bedroom', 'count' => 2],
                    ['name' => 'bathroom', 'count' => 2],
                    ['name' => 'living_room', 'count' => 1],
                ],
                'specifications' => [
                    ['key' => 'furnished', 'value' => 'true'],
                    ['key' => 'parking_bays', 'value' => '1'],
                    ['key' => 'view', 'value' => 'sea_view'],
                ],
                'amenity_ids' => [$pool->id, $gym->id],
                'currency_id' => $currency->id,
                'asking_rent_amount' => 60000,
                'rent_period' => 'year',
            ]);

        $response->assertRedirect("/units/{$this->unit->id}");

        $this->unit->refresh();

        // Pricing columns persisted on the unit
        $this->assertEquals($currency->id, $this->unit->currency_id);
        $this->assertEquals('60000.00', $this->unit->asking_rent_amount);
        $this->assertEquals('year', $this->unit->rent_period);

        // Rooms persisted
        $rooms = $this->unit->rooms()->orderBy('name')->get()->keyBy('name');
        $this->assertEquals(2, $rooms['bathroom']->count);
        $this->assertEquals(2, $rooms['bedroom']->count);
        $this->assertEquals(1, $rooms['living_room']->count);

        // Specifications persisted
        $specs = $this->unit->specifications()->get()->keyBy('key');
        $this->assertEquals('true', $specs['furnished']->value);
        $this->assertEquals('1', $specs['parking_bays']->value);
        $this->assertEquals('sea_view', $specs['view']->value);

        // Amenities pivot synced
        $amenityIds = $this->unit->features()->pluck('rf_features.id')->sort()->values()->toArray();
        $this->assertEquals(
            collect([$pool->id, $gym->id])->sort()->values()->toArray(),
            $amenityIds
        );
    }

    /**
     * Submitting an empty amenity_ids array is valid (no amenities).
     */
    public function test_update_with_empty_amenities_is_valid(): void
    {
        $pool = Feature::factory()->create(['type' => 'amenity', 'name' => 'Pool']);
        $this->unit->features()->sync([$pool->id]);

        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/units/{$this->unit->id}", [
                'name' => $this->unit->name,
                'rf_community_id' => $this->community->id,
                'category_id' => $this->unit->category_id,
                'type_id' => $this->unit->type_id,
                'status_id' => $this->unit->status_id,
                'amenity_ids' => [],
            ]);

        $response->assertRedirect("/units/{$this->unit->id}");
        $this->assertCount(0, $this->unit->features()->get());
    }

    /**
     * Unit show page includes specifications, amenities, and currency relation.
     */
    public function test_show_page_loads_with_new_metadata(): void
    {
        $pool = Feature::factory()->create(['type' => 'amenity', 'name' => 'Pool', 'name_en' => 'Pool']);
        $currency = Currency::factory()->create(['code' => 'SAR', 'symbol' => '﷼']);

        $this->unit->features()->sync([$pool->id]);
        $this->unit->update([
            'currency_id' => $currency->id,
            'asking_rent_amount' => 60000,
            'rent_period' => 'year',
        ]);

        $this->unit->specifications()->create(['key' => 'furnished', 'value' => 'true']);
        $this->unit->rooms()->create(['name' => 'bedroom', 'count' => 2]);

        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->withoutVite()
            ->get("/units/{$this->unit->id}");

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('properties/units/Show')
            ->has('unit.features')
            ->has('unit.specifications')
            ->has('unit.rooms')
        );
    }

    /**
     * Edit page provides amenityOptions and currencies to the frontend.
     */
    public function test_edit_page_provides_amenity_options_and_currencies(): void
    {
        Feature::factory()->create(['type' => 'amenity', 'name' => 'Pool', 'name_en' => 'Pool']);
        Feature::factory()->create(['type' => 'facility', 'name' => 'Tennis Court']);
        Currency::factory()->create(['code' => 'SAR']);

        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->withoutVite()
            ->get("/units/{$this->unit->id}/edit");

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('properties/units/Edit')
            ->has('amenityOptions', 1) // only 1 amenity-type feature
            ->has('currencies')
        );
    }

    /**
     * Pricing fields are stored as nullable — submitting without them is valid.
     */
    public function test_update_without_pricing_is_valid(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/units/{$this->unit->id}", [
                'name' => $this->unit->name,
                'rf_community_id' => $this->community->id,
                'category_id' => $this->unit->category_id,
                'type_id' => $this->unit->type_id,
                'status_id' => $this->unit->status_id,
            ]);

        $response->assertRedirect("/units/{$this->unit->id}");

        $this->unit->refresh();
        $this->assertNull($this->unit->currency_id);
        $this->assertNull($this->unit->asking_rent_amount);
        $this->assertNull($this->unit->rent_period);
    }
}
