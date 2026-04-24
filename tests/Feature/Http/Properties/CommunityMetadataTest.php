<?php

namespace Tests\Feature\Http\Properties;

use App\Models\AccountMembership;
use App\Models\Amenity;
use App\Models\City;
use App\Models\Community;
use App\Models\Country;
use App\Models\Currency;
use App\Models\District;
use App\Models\Tenant;
use App\Models\User;
use DB;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CommunityMetadataTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    private Community $community;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        [$this->user, $this->tenant] = $this->authenticateUser();
        $this->community = $this->createCommunityWithDeps($this->tenant);
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    /**
     * @return array{0: User, 1: Tenant}
     */
    private function authenticateUser(): array
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Test Account']);
        $tenant->makeCurrent();

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);
        $this->ensureAccountAdminsRoleExists();
        $user->assignRole('accountAdmins');
        $this->actingAs($user);

        return [$user, $tenant];
    }

    private function ensureAccountAdminsRoleExists(): void
    {
        $exists = DB::table('roles')
            ->where('name', 'accountAdmins')
            ->where('guard_name', 'web')
            ->exists();

        if (! $exists) {
            DB::table('roles')->insert([
                'name' => 'accountAdmins',
                'guard_name' => 'web',
                'name_en' => 'Account Admins',
                'name_ar' => 'مدراء الحسابات',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function createCommunityWithDeps(Tenant $tenant): Community
    {
        $country = Country::factory()->create();
        $currency = Currency::factory()->create();
        $city = City::factory()->recycle($country)->create();
        $district = District::factory()->recycle($city)->create();

        return Community::factory()
            ->recycle([$country, $currency, $city, $district])
            ->create(['account_tenant_id' => $tenant->id]);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => $this->community->name,
            'country_id' => $this->community->country_id,
            'currency_id' => $this->community->currency_id,
            'city_id' => $this->community->city_id,
            'district_id' => $this->community->district_id,
        ], $overrides);
    }

    // -------------------------------------------------------------------------
    // Happy paths
    // -------------------------------------------------------------------------

    public function test_update_amenities_syncs_pivot(): void
    {
        $amenity1 = Amenity::factory()->create();
        $amenity2 = Amenity::factory()->create();
        $amenity3 = Amenity::factory()->create();

        // Pre-attach amenity3 to verify sync removes it
        $this->community->amenities()->attach($amenity3->id);

        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/communities/{$this->community->id}", $this->validPayload([
                'amenity_ids' => [$amenity1->id, $amenity2->id],
            ]));

        $response->assertRedirect();

        $this->assertDatabaseHas('community_amenities', [
            'community_id' => $this->community->id,
            'amenity_id' => $amenity1->id,
        ]);
        $this->assertDatabaseHas('community_amenities', [
            'community_id' => $this->community->id,
            'amenity_id' => $amenity2->id,
        ]);
        $this->assertDatabaseMissing('community_amenities', [
            'community_id' => $this->community->id,
            'amenity_id' => $amenity3->id,
        ]);
    }

    public function test_update_working_days_stores_json(): void
    {
        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/communities/{$this->community->id}", $this->validPayload([
                'working_days' => ['sat', 'sun', 'mon'],
            ]));

        $response->assertRedirect();

        $this->community->refresh();
        $this->assertIsArray($this->community->working_days);
        $this->assertEqualsCanonicalizing(['sat', 'sun', 'mon'], $this->community->working_days);
    }

    public function test_update_coordinates_stores_decimals(): void
    {
        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/communities/{$this->community->id}", $this->validPayload([
                'latitude' => 24.774265,
                'longitude' => 46.738586,
            ]));

        $response->assertRedirect("/communities/{$this->community->id}");

        $this->community->refresh();
        $this->assertEquals('24.7742650', $this->community->latitude);
        $this->assertEquals('46.7385860', $this->community->longitude);
    }

    public function test_clear_amenities(): void
    {
        $amenity = Amenity::factory()->create();
        $this->community->amenities()->attach($amenity->id);

        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/communities/{$this->community->id}", $this->validPayload([
                'amenity_ids' => [],
            ]));

        $response->assertRedirect();

        $this->assertDatabaseMissing('community_amenities', [
            'community_id' => $this->community->id,
        ]);
    }

    public function test_edit_page_includes_amenities_and_all_amenities_props(): void
    {
        $amenity1 = Amenity::factory()->create();
        $amenity2 = Amenity::factory()->create();
        $this->community->amenities()->attach($amenity1->id);

        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get("/communities/{$this->community->id}/edit");

        $response->assertStatus(200);
        $response->assertInertia(function ($page) use ($amenity1, $amenity2) {
            $page->has('community.amenities')
                ->has('all_amenities')
                ->where('community.amenities', function ($amenities) use ($amenity1) {
                    $ids = collect($amenities)->pluck('id')->toArray();

                    return in_array($amenity1->id, $ids);
                })
                ->where('all_amenities', function ($allAmenities) use ($amenity1, $amenity2) {
                    $ids = collect($allAmenities)->pluck('id')->toArray();

                    return in_array($amenity1->id, $ids) && in_array($amenity2->id, $ids);
                });
        });
    }

    public function test_show_page_includes_metadata(): void
    {
        $this->community->update([
            'working_days' => ['sat', 'mon'],
            'latitude' => 24.774265,
            'longitude' => 46.738586,
        ]);

        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get("/communities/{$this->community->id}");

        $response->assertStatus(200);
        $response->assertInertia(function ($page) {
            $page->has('community.working_days')
                ->has('community.latitude')
                ->has('community.longitude');
        });
    }

    // -------------------------------------------------------------------------
    // Failure paths
    // -------------------------------------------------------------------------

    public function test_invalid_latitude_returns_422(): void
    {
        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->putJson("/communities/{$this->community->id}", $this->validPayload([
                'latitude' => 999,
                'longitude' => 46.738586,
            ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['latitude']);
    }

    public function test_invalid_longitude_returns_422(): void
    {
        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->putJson("/communities/{$this->community->id}", $this->validPayload([
                'latitude' => 24.774265,
                'longitude' => -999,
            ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['longitude']);
    }

    public function test_latitude_without_longitude_returns_422(): void
    {
        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->putJson("/communities/{$this->community->id}", $this->validPayload([
                'latitude' => 24.774265,
                'longitude' => null,
            ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['longitude']);
    }

    public function test_longitude_without_latitude_returns_422(): void
    {
        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->putJson("/communities/{$this->community->id}", $this->validPayload([
                'latitude' => null,
                'longitude' => 46.738586,
            ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['latitude']);
    }

    public function test_invalid_amenity_id_returns_422(): void
    {
        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->putJson("/communities/{$this->community->id}", $this->validPayload([
                'amenity_ids' => [99999],
            ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['amenity_ids.0']);
    }

    public function test_invalid_working_day_value_returns_422(): void
    {
        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->putJson("/communities/{$this->community->id}", $this->validPayload([
                'working_days' => ['monday'],
            ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['working_days.0']);
    }

    // -------------------------------------------------------------------------
    // Edge cases
    // -------------------------------------------------------------------------

    public function test_null_coordinates_are_stored_as_null(): void
    {
        // First set coordinates
        $this->community->update(['latitude' => 24.7, 'longitude' => 46.7]);

        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/communities/{$this->community->id}", $this->validPayload([
                'latitude' => null,
                'longitude' => null,
            ]));

        $response->assertRedirect();

        $this->community->refresh();
        $this->assertNull($this->community->latitude);
        $this->assertNull($this->community->longitude);
    }

    public function test_omitting_metadata_fields_leaves_existing_data_unchanged(): void
    {
        $amenity = Amenity::factory()->create();
        $this->community->amenities()->attach($amenity->id);
        $this->community->update([
            'working_days' => ['sat', 'sun'],
            'latitude' => 24.774265,
            'longitude' => 46.738586,
        ]);

        // PUT without amenity_ids, working_days, latitude, longitude
        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/communities/{$this->community->id}", [
                'name' => 'Updated Name',
                'country_id' => $this->community->country_id,
                'currency_id' => $this->community->currency_id,
                'city_id' => $this->community->city_id,
                'district_id' => $this->community->district_id,
            ]);

        $response->assertRedirect();

        // Amenity pivot must be untouched
        $this->assertDatabaseHas('community_amenities', [
            'community_id' => $this->community->id,
            'amenity_id' => $amenity->id,
        ]);

        // Working days and coordinates must be unchanged
        $this->community->refresh();
        $this->assertEquals('Updated Name', $this->community->name);
        $this->assertEqualsCanonicalizing(['sat', 'sun'], $this->community->working_days);
        $this->assertNotNull($this->community->latitude);
        $this->assertNotNull($this->community->longitude);
    }

    public function test_duplicate_amenity_ids_are_deduplicated(): void
    {
        $amenity = Amenity::factory()->create();

        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/communities/{$this->community->id}", $this->validPayload([
                'amenity_ids' => [$amenity->id, $amenity->id],
            ]));

        $response->assertRedirect();

        $count = DB::table('community_amenities')
            ->where('community_id', $this->community->id)
            ->where('amenity_id', $amenity->id)
            ->count();

        $this->assertEquals(1, $count);
    }

    public function test_working_days_empty_array_stores_empty_json(): void
    {
        // First set some working days
        $this->community->update(['working_days' => ['sat', 'sun']]);

        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/communities/{$this->community->id}", $this->validPayload([
                'working_days' => [],
            ]));

        $response->assertRedirect();

        $this->community->refresh();
        $this->assertIsArray($this->community->working_days);
        $this->assertEmpty($this->community->working_days);
    }

    // -------------------------------------------------------------------------
    // QA gap tests — failure paths
    // -------------------------------------------------------------------------

    public function test_unauthenticated_request_is_redirected_to_login(): void
    {
        // Log out the user that setUp() authenticated, then send the PUT as a guest
        auth()->logout();

        $response = $this
            ->put("/communities/{$this->community->id}", $this->validPayload());

        // The 'auth' middleware redirects unauthenticated web requests to the login page
        $response->assertRedirect(route('login'));
    }

    public function test_user_without_update_permission_receives_403(): void
    {
        // Create a user with no role (no permissions at all)
        $unprivileged = User::factory()->create();
        AccountMembership::create([
            'user_id' => $unprivileged->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);

        // Do NOT assign any role — user has zero permissions
        $response = $this
            ->actingAs($unprivileged)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/communities/{$this->community->id}", $this->validPayload());

        $response->assertForbidden();
    }

    public function test_user_from_different_tenant_cannot_update_community(): void
    {
        // Create a separate tenant and a fully-privileged admin that belongs to it
        $otherTenant = Tenant::create(['name' => 'Other Account']);

        $otherUser = User::factory()->create();
        AccountMembership::create([
            'user_id' => $otherUser->id,
            'account_tenant_id' => $otherTenant->id,
            'role' => 'account_admins',
        ]);
        $this->ensureAccountAdminsRoleExists();
        $otherUser->assignRole('accountAdmins');

        // The community belongs to $this->tenant. Even an admin on $otherTenant
        // must be refused because belongsToCurrentTenant() will return false.
        $otherTenant->makeCurrent();

        $response = $this
            ->actingAs($otherUser)
            ->withSession(['tenant_id' => $otherTenant->id])
            ->put("/communities/{$this->community->id}", $this->validPayload());

        // Community belongs to a different tenant — policy must deny (403 or 404)
        $this->assertTrue(
            $response->status() === 403 || $response->status() === 404,
            "Expected 403 or 404 for cross-tenant update, got {$response->status()}"
        );

        // Restore original tenant for teardown
        $this->tenant->makeCurrent();
    }

    // -------------------------------------------------------------------------
    // QA gap tests — coordinate boundary values
    // -------------------------------------------------------------------------

    public function test_latitude_at_exact_upper_boundary_is_accepted(): void
    {
        // Use put() (not putJson()) so success is a redirect, not a JSON 200
        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/communities/{$this->community->id}", $this->validPayload([
                'latitude' => 90,
                'longitude' => 0,
            ]));

        $response->assertRedirect();
    }

    public function test_latitude_at_exact_lower_boundary_is_accepted(): void
    {
        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/communities/{$this->community->id}", $this->validPayload([
                'latitude' => -90,
                'longitude' => 0,
            ]));

        $response->assertRedirect();
    }

    public function test_longitude_at_exact_upper_boundary_is_accepted(): void
    {
        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/communities/{$this->community->id}", $this->validPayload([
                'latitude' => 0,
                'longitude' => 180,
            ]));

        $response->assertRedirect();
    }

    public function test_longitude_at_exact_lower_boundary_is_accepted(): void
    {
        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/communities/{$this->community->id}", $this->validPayload([
                'latitude' => 0,
                'longitude' => -180,
            ]));

        $response->assertRedirect();
    }

    public function test_latitude_just_above_upper_boundary_is_rejected(): void
    {
        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->putJson("/communities/{$this->community->id}", $this->validPayload([
                'latitude' => 90.000001,
                'longitude' => 0,
            ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['latitude']);
    }

    public function test_longitude_just_above_upper_boundary_is_rejected(): void
    {
        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->putJson("/communities/{$this->community->id}", $this->validPayload([
                'latitude' => 0,
                'longitude' => 180.000001,
            ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['longitude']);
    }

    // -------------------------------------------------------------------------
    // QA gap tests — amenity pivot regression (3 amenities, update other field)
    // -------------------------------------------------------------------------

    public function test_updating_name_only_preserves_all_three_attached_amenities(): void
    {
        $amenity1 = Amenity::factory()->create();
        $amenity2 = Amenity::factory()->create();
        $amenity3 = Amenity::factory()->create();

        $this->community->amenities()->sync([$amenity1->id, $amenity2->id, $amenity3->id]);

        // PUT without amenity_ids key — only name changes
        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put("/communities/{$this->community->id}", [
                'name' => 'New Name',
                'country_id' => $this->community->country_id,
                'currency_id' => $this->community->currency_id,
                'city_id' => $this->community->city_id,
                'district_id' => $this->community->district_id,
            ]);

        $response->assertRedirect();

        foreach ([$amenity1, $amenity2, $amenity3] as $amenity) {
            $this->assertDatabaseHas('community_amenities', [
                'community_id' => $this->community->id,
                'amenity_id' => $amenity->id,
            ]);
        }

        $this->community->refresh();
        $this->assertEquals('New Name', $this->community->name);
    }
}
