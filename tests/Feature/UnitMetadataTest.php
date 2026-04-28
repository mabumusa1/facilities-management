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

    // =========================================================================
    // QA-added: failure paths
    // =========================================================================

    /**
     * Unauthenticated requests to the edit page must redirect to login (401/302).
     */
    public function test_edit_page_requires_authentication(): void
    {
        $response = $this->withoutVite()
            ->get("/units/{$this->unit->id}/edit");

        $response->assertRedirect('/login');
    }

    /**
     * Unauthenticated PUT to update must redirect to login.
     */
    public function test_update_requires_authentication(): void
    {
        $response = $this->put("/units/{$this->unit->id}", [
            'name' => 'Should Not Save',
            'rf_community_id' => $this->community->id,
            'category_id' => $this->unit->category_id,
            'type_id' => $this->unit->type_id,
            'status_id' => $this->unit->status_id,
        ]);

        $response->assertRedirect('/login');
    }

    /**
     * Unauthenticated GET to show page must redirect to login.
     */
    public function test_show_page_requires_authentication(): void
    {
        $response = $this->withoutVite()
            ->get("/units/{$this->unit->id}");

        $response->assertRedirect('/login');
    }

    /**
     * A user without properties.UPDATE permission receives 403 on PUT.
     */
    public function test_update_forbidden_without_update_permission(): void
    {
        $restricted = User::factory()->create();
        $viewOnlyRole = Role::firstOrCreate(['name' => 'viewOnlyRole', 'guard_name' => 'web']);
        $viewPerm = Permission::firstOrCreate(['name' => 'properties.VIEW', 'guard_name' => 'web']);
        $viewOnlyRole->syncPermissions([$viewPerm]);
        $restricted->assignRole($viewOnlyRole);

        AccountMembership::create([
            'user_id' => $restricted->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'view_only',
        ]);

        $response = $this->actingAs($restricted)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/units/{$this->unit->id}", [
                'name' => $this->unit->name,
                'rf_community_id' => $this->community->id,
                'category_id' => $this->unit->category_id,
                'type_id' => $this->unit->type_id,
                'status_id' => $this->unit->status_id,
            ]);

        $response->assertForbidden();
    }

    /**
     * A user without properties.VIEW permission receives 403 on show.
     */
    public function test_show_forbidden_without_view_permission(): void
    {
        $restricted = User::factory()->create();
        $noPermRole = Role::firstOrCreate(['name' => 'noPermRole', 'guard_name' => 'web']);
        $noPermRole->syncPermissions([]);
        $restricted->assignRole($noPermRole);

        AccountMembership::create([
            'user_id' => $restricted->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'no_perm',
        ]);

        $response = $this->actingAs($restricted)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->withoutVite()
            ->get("/units/{$this->unit->id}");

        $response->assertForbidden();
    }

    /**
     * A user from tenant B cannot update a unit belonging to tenant A.
     * AC: "Tenant boundary: Unit records are tenant-scoped."
     */
    public function test_update_blocked_across_tenant_boundary(): void
    {
        $tenantB = Tenant::create(['name' => 'Tenant B']);
        $userB = User::factory()->create();

        $roleB = Role::firstOrCreate(['name' => 'accountAdminsB', 'guard_name' => 'web']);
        $viewPerm = Permission::firstOrCreate(['name' => 'properties.VIEW', 'guard_name' => 'web']);
        $updatePerm = Permission::firstOrCreate(['name' => 'properties.UPDATE', 'guard_name' => 'web']);
        $roleB->syncPermissions([$viewPerm, $updatePerm]);
        $userB->assignRole($roleB);

        AccountMembership::create([
            'user_id' => $userB->id,
            'account_tenant_id' => $tenantB->id,
            'role' => 'account_admins',
        ]);

        // $this->unit belongs to tenant A; userB operates under tenant B's session
        $response = $this->actingAs($userB)
            ->withSession(['tenant_id' => $tenantB->id])
            ->put("/units/{$this->unit->id}", [
                'name' => $this->unit->name,
                'rf_community_id' => $this->community->id,
                'category_id' => $this->unit->category_id,
                'type_id' => $this->unit->type_id,
                'status_id' => $this->unit->status_id,
            ]);

        $response->assertForbidden();
    }

    /**
     * A user from tenant B cannot view a unit belonging to tenant A.
     */
    public function test_show_blocked_across_tenant_boundary(): void
    {
        $tenantB = Tenant::create(['name' => 'Tenant B Show']);
        $userB = User::factory()->create();

        $roleC = Role::firstOrCreate(['name' => 'accountAdminsC', 'guard_name' => 'web']);
        $viewPerm = Permission::firstOrCreate(['name' => 'properties.VIEW', 'guard_name' => 'web']);
        $roleC->syncPermissions([$viewPerm]);
        $userB->assignRole($roleC);

        AccountMembership::create([
            'user_id' => $userB->id,
            'account_tenant_id' => $tenantB->id,
            'role' => 'account_admins',
        ]);

        $response = $this->actingAs($userB)
            ->withSession(['tenant_id' => $tenantB->id])
            ->withoutVite()
            ->get("/units/{$this->unit->id}");

        $response->assertForbidden();
    }

    /**
     * Requesting a non-existent unit returns 404.
     */
    public function test_show_returns_404_for_nonexistent_unit(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->withoutVite()
            ->get('/units/999999999');

        $response->assertNotFound();
    }

    // =========================================================================
    // QA-added: validation failure paths
    // =========================================================================

    /**
     * AC2: Submitting net_area = 0 should fail validation ("Area must be greater than 0").
     *
     * BUG NOTE: The current validation rule is `min:0` (allows zero) but the AC
     * requires `min:1` / `gt:0`. This test documents the expected behaviour per
     * the acceptance criterion and FAILS against the current implementation.
     * Engineer must change the rule to `['nullable', 'numeric', 'min:0.01']` (or
     * `'gt:0'`) and update the error message.
     */
    public function test_update_rejects_net_area_of_zero(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/units/{$this->unit->id}", [
                'name' => $this->unit->name,
                'rf_community_id' => $this->community->id,
                'category_id' => $this->unit->category_id,
                'type_id' => $this->unit->type_id,
                'status_id' => $this->unit->status_id,
                'net_area' => 0,
            ]);

        $response->assertSessionHasErrors(['net_area']);
    }

    /**
     * Negative asking_rent_amount must be rejected (rule: min:0 means 0 allowed but negative is not).
     */
    public function test_update_rejects_negative_asking_rent_amount(): void
    {
        $currency = Currency::factory()->create();

        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/units/{$this->unit->id}", [
                'name' => $this->unit->name,
                'rf_community_id' => $this->community->id,
                'category_id' => $this->unit->category_id,
                'type_id' => $this->unit->type_id,
                'status_id' => $this->unit->status_id,
                'currency_id' => $currency->id,
                'asking_rent_amount' => -1,
                'rent_period' => 'year',
            ]);

        $response->assertSessionHasErrors(['asking_rent_amount']);
    }

    /**
     * An invalid rent_period value must be rejected.
     */
    public function test_update_rejects_invalid_rent_period(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/units/{$this->unit->id}", [
                'name' => $this->unit->name,
                'rf_community_id' => $this->community->id,
                'category_id' => $this->unit->category_id,
                'type_id' => $this->unit->type_id,
                'status_id' => $this->unit->status_id,
                'rent_period' => 'quarterly',
            ]);

        $response->assertSessionHasErrors(['rent_period']);
    }

    /**
     * Submitting a non-existent currency_id must be rejected.
     */
    public function test_update_rejects_nonexistent_currency_id(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/units/{$this->unit->id}", [
                'name' => $this->unit->name,
                'rf_community_id' => $this->community->id,
                'category_id' => $this->unit->category_id,
                'type_id' => $this->unit->type_id,
                'status_id' => $this->unit->status_id,
                'currency_id' => 999999,
            ]);

        $response->assertSessionHasErrors(['currency_id']);
    }

    /**
     * Submitting a non-existent amenity_id must be rejected.
     */
    public function test_update_rejects_nonexistent_amenity_id(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/units/{$this->unit->id}", [
                'name' => $this->unit->name,
                'rf_community_id' => $this->community->id,
                'category_id' => $this->unit->category_id,
                'type_id' => $this->unit->type_id,
                'status_id' => $this->unit->status_id,
                'amenity_ids' => [999999],
            ]);

        $response->assertSessionHasErrors(['amenity_ids.0']);
    }

    /**
     * Room count exceeding the max of 99 must be rejected.
     */
    public function test_update_rejects_room_count_exceeding_max(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/units/{$this->unit->id}", [
                'name' => $this->unit->name,
                'rf_community_id' => $this->community->id,
                'category_id' => $this->unit->category_id,
                'type_id' => $this->unit->type_id,
                'status_id' => $this->unit->status_id,
                'rooms' => [
                    ['name' => 'bedroom', 'count' => 100],
                ],
            ]);

        $response->assertSessionHasErrors(['rooms.0.count']);
    }

    /**
     * Room count of 0 is the boundary minimum and must be accepted.
     */
    public function test_update_accepts_room_count_of_zero(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/units/{$this->unit->id}", [
                'name' => $this->unit->name,
                'rf_community_id' => $this->community->id,
                'category_id' => $this->unit->category_id,
                'type_id' => $this->unit->type_id,
                'status_id' => $this->unit->status_id,
                'rooms' => [
                    ['name' => 'studio', 'count' => 0],
                ],
            ]);

        $response->assertRedirect("/units/{$this->unit->id}");
    }

    /**
     * Specification value exceeding max:255 must be rejected.
     */
    public function test_update_rejects_specification_value_exceeding_max_length(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/units/{$this->unit->id}", [
                'name' => $this->unit->name,
                'rf_community_id' => $this->community->id,
                'category_id' => $this->unit->category_id,
                'type_id' => $this->unit->type_id,
                'status_id' => $this->unit->status_id,
                'specifications' => [
                    ['key' => 'view', 'value' => str_repeat('a', 256)],
                ],
            ]);

        $response->assertSessionHasErrors(['specifications.0.value']);
    }

    /**
     * Specification key exceeding max:100 must be rejected.
     */
    public function test_update_rejects_specification_key_exceeding_max_length(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/units/{$this->unit->id}", [
                'name' => $this->unit->name,
                'rf_community_id' => $this->community->id,
                'category_id' => $this->unit->category_id,
                'type_id' => $this->unit->type_id,
                'status_id' => $this->unit->status_id,
                'specifications' => [
                    ['key' => str_repeat('k', 101), 'value' => 'valid'],
                ],
            ]);

        $response->assertSessionHasErrors(['specifications.0.key']);
    }

    /**
     * Missing required name field must be rejected with a validation error.
     */
    public function test_update_rejects_missing_name(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/units/{$this->unit->id}", [
                'rf_community_id' => $this->community->id,
                'category_id' => $this->unit->category_id,
                'type_id' => $this->unit->type_id,
                'status_id' => $this->unit->status_id,
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    // =========================================================================
    // QA-added: edge cases
    // =========================================================================

    /**
     * AC3 (edge case): show page renders correctly when unit has no amenity tags.
     * The page must load without error (empty amenities, not a 500).
     */
    public function test_show_page_renders_with_no_amenities(): void
    {
        // Unit has no features attached
        $this->assertCount(0, $this->unit->features()->get());

        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->withoutVite()
            ->get("/units/{$this->unit->id}");

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('properties/units/Show')
            ->has('unit.features')
        );
    }

    /**
     * Arabic/bilingual: a specification with Arabic Unicode value round-trips correctly.
     */
    public function test_update_persists_arabic_specification_value(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/units/{$this->unit->id}", [
                'name' => $this->unit->name,
                'rf_community_id' => $this->community->id,
                'category_id' => $this->unit->category_id,
                'type_id' => $this->unit->type_id,
                'status_id' => $this->unit->status_id,
                'specifications' => [
                    ['key' => 'view', 'value' => 'إطلالة بحرية'],
                ],
            ]);

        $response->assertRedirect("/units/{$this->unit->id}");

        $spec = $this->unit->specifications()->where('key', 'view')->first();
        $this->assertNotNull($spec);
        $this->assertEquals('إطلالة بحرية', $spec->value);
    }

    /**
     * Re-saving the same amenity list twice (idempotent sync) does not duplicate rows.
     */
    public function test_update_syncing_same_amenities_twice_is_idempotent(): void
    {
        $pool = Feature::factory()->create(['type' => 'amenity', 'name' => 'Pool', 'name_en' => 'Pool']);

        $payload = [
            'name' => $this->unit->name,
            'rf_community_id' => $this->community->id,
            'category_id' => $this->unit->category_id,
            'type_id' => $this->unit->type_id,
            'status_id' => $this->unit->status_id,
            'amenity_ids' => [$pool->id],
        ];

        $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/units/{$this->unit->id}", $payload);

        $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/units/{$this->unit->id}", $payload);

        $this->assertCount(1, $this->unit->features()->get());
    }

    /**
     * Edit page filters amenityOptions to only type=amenity features (not type=facility).
     * Verifies Feature::scopeAmenities() is correctly applied.
     */
    public function test_edit_page_excludes_non_amenity_features_from_options(): void
    {
        Feature::factory()->create(['type' => 'amenity', 'name' => 'Pool', 'name_en' => 'Pool']);
        Feature::factory()->create(['type' => 'facility', 'name' => 'Tennis Court', 'name_en' => 'Tennis Court']);
        Feature::factory()->create(['type' => 'other', 'name' => 'Lobby', 'name_en' => 'Lobby']);

        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->withoutVite()
            ->get("/units/{$this->unit->id}/edit");

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('properties/units/Edit')
            ->has('amenityOptions', 1)  // only the 1 amenity-type feature
        );
    }

    /**
     * Submitting specifications without rooms leaves existing rooms untouched.
     */
    public function test_update_specifications_without_rooms_does_not_delete_existing_rooms(): void
    {
        $this->unit->rooms()->create(['name' => 'bedroom', 'count' => 3]);

        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/units/{$this->unit->id}", [
                'name' => $this->unit->name,
                'rf_community_id' => $this->community->id,
                'category_id' => $this->unit->category_id,
                'type_id' => $this->unit->type_id,
                'status_id' => $this->unit->status_id,
                'specifications' => [
                    ['key' => 'furnished', 'value' => 'true'],
                ],
                // no rooms key — existing rooms should be untouched
            ]);

        $response->assertRedirect("/units/{$this->unit->id}");

        // Room still exists because rooms key was absent from payload
        $this->assertCount(1, $this->unit->rooms()->get());
    }

    /**
     * Pricing: valid rent_period values are 'month' and 'year'.
     * Verify both are accepted.
     */
    public function test_update_accepts_both_rent_period_values(): void
    {
        $currency = Currency::factory()->create();

        foreach (['month', 'year'] as $period) {
            $response = $this->actingAs($this->user)
                ->withSession(['tenant_id' => $this->tenant->id])
                ->put("/units/{$this->unit->id}", [
                    'name' => $this->unit->name,
                    'rf_community_id' => $this->community->id,
                    'category_id' => $this->unit->category_id,
                    'type_id' => $this->unit->type_id,
                    'status_id' => $this->unit->status_id,
                    'currency_id' => $currency->id,
                    'asking_rent_amount' => 5000,
                    'rent_period' => $period,
                ]);

            $response->assertRedirect("/units/{$this->unit->id}");
            $this->assertEquals($period, $this->unit->fresh()->rent_period);
        }
    }
}
