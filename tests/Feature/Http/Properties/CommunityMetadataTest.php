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
}
