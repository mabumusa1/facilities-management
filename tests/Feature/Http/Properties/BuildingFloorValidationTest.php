<?php

namespace Tests\Feature\Http\Properties;

use App\Models\AccountMembership;
use App\Models\Building;
use App\Models\Community;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use DB;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BuildingFloorValidationTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    private Building $building;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->tenant = Tenant::create(['name' => 'Bldg Floor Test']);
        $this->tenant->makeCurrent();

        AccountMembership::create([
            'user_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);

        $this->ensureAccountAdminsRoleExists();
        $this->user->assignRole('accountAdmins');

        $this->actingAs($this->user);
        $this->withSession(['tenant_id' => $this->tenant->id]);

        $community = Community::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $this->building = Building::factory()->create([
            'rf_community_id' => $community->id,
            'account_tenant_id' => $this->tenant->id,
        ]);
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

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    // -------------------------------------------------------------------------
    // Happy paths
    // -------------------------------------------------------------------------

    public function test_update_building_metadata_persists_floors_and_year(): void
    {
        $response = $this->putJson("/rf/buildings/{$this->building->id}", [
            'name' => 'Tower A Updated',
            'rf_community_id' => $this->building->rf_community_id,
            'no_floors' => 15,
            'year_build' => 2020,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.no_floors', 15);
        $response->assertJsonPath('data.year_build', 2020);

        $this->assertDatabaseHas('rf_buildings', [
            'id' => $this->building->id,
            'no_floors' => 15,
            'year_build' => 2020,
        ]);
    }

    public function test_upload_document_stores_file(): void
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('building_permit.pdf', 100);

        $response = $this->postJson("/rf/buildings/{$this->building->id}/documents", [
            'file' => $file,
            'name' => 'Building Permit',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.name', 'Building Permit');

        $this->assertDatabaseHas('media', [
            'mediable_type' => Building::class,
            'mediable_id' => $this->building->id,
            'collection' => 'documents',
        ]);
    }

    // -------------------------------------------------------------------------
    // Failure paths — floor validation
    // -------------------------------------------------------------------------

    public function test_cannot_set_floors_lower_than_highest_unit_floor(): void
    {
        Unit::factory()->create([
            'rf_community_id' => $this->building->rf_community_id,
            'rf_building_id' => $this->building->id,
            'floor_no' => 10,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $response = $this->putJson("/rf/buildings/{$this->building->id}", [
            'name' => 'Tower A',
            'rf_community_id' => $this->building->rf_community_id,
            'no_floors' => 5,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['no_floors']);
    }

    public function test_can_set_floors_equal_to_highest_unit_floor(): void
    {
        Unit::factory()->create([
            'rf_community_id' => $this->building->rf_community_id,
            'rf_building_id' => $this->building->id,
            'floor_no' => 10,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $response = $this->putJson("/rf/buildings/{$this->building->id}", [
            'name' => 'Tower A',
            'rf_community_id' => $this->building->rf_community_id,
            'no_floors' => 10,
        ]);

        $response->assertStatus(200);
    }

    public function test_can_set_floors_higher_than_highest_unit_floor(): void
    {
        Unit::factory()->create([
            'rf_community_id' => $this->building->rf_community_id,
            'rf_building_id' => $this->building->id,
            'floor_no' => 10,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $response = $this->putJson("/rf/buildings/{$this->building->id}", [
            'name' => 'Tower A',
            'rf_community_id' => $this->building->rf_community_id,
            'no_floors' => 15,
        ]);

        $response->assertStatus(200);
    }

    public function test_no_floors_validation_skipped_when_field_not_in_request(): void
    {
        Unit::factory()->create([
            'rf_community_id' => $this->building->rf_community_id,
            'rf_building_id' => $this->building->id,
            'floor_no' => 10,
            'account_tenant_id' => $this->tenant->id,
        ]);

        // Update without no_floors field
        $response = $this->putJson("/rf/buildings/{$this->building->id}", [
            'name' => 'Tower A Renamed',
            'rf_community_id' => $this->building->rf_community_id,
        ]);

        $response->assertStatus(200);
        $this->assertSame('Tower A Renamed', $this->building->fresh()->name);
    }

    // -------------------------------------------------------------------------
    // Edge cases
    // -------------------------------------------------------------------------

    public function test_floor_validation_with_no_units_passes(): void
    {
        $response = $this->putJson("/rf/buildings/{$this->building->id}", [
            'name' => 'Tower B',
            'rf_community_id' => $this->building->rf_community_id,
            'no_floors' => 1,
        ]);

        $response->assertStatus(200);
    }
}
