<?php

namespace Tests\Feature\Http\Contacts;

use App\Models\Building;
use App\Models\Community;
use App\Models\Dependent;
use App\Models\Owner;
use App\Models\Professional;
use App\Models\Resident;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\UnitOwnership;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ContactsDataModelTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create(['name' => 'Contact Test']);
        $this->tenant->makeCurrent();
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    public function test_contact_documents_attach_to_resident(): void
    {
        $resident = Resident::factory()->create(['account_tenant_id' => $this->tenant->id]);

        $doc = $resident->kycDocuments()->create([
            'account_tenant_id' => $this->tenant->id,
            'type' => 'national_id',
            'file_path' => 'kyc/resident-id-1.pdf',
            'original_name' => 'national_id.pdf',
        ]);

        $this->assertDatabaseHas('rf_contact_documents', [
            'contact_type' => Resident::class,
            'contact_id' => $resident->id,
            'type' => 'national_id',
        ]);

        $this->assertCount(1, $resident->kycDocuments);
    }

    public function test_contact_documents_morph_to_owner_and_professional(): void
    {
        $owner = Owner::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $professional = Professional::factory()->create(['account_tenant_id' => $this->tenant->id]);

        $owner->kycDocuments()->create(['account_tenant_id' => $this->tenant->id, 'type' => 'passport', 'file_path' => 'p1', 'original_name' => 'p.pdf']);
        $professional->kycDocuments()->create(['account_tenant_id' => $this->tenant->id, 'type' => 'license', 'file_path' => 'l1', 'original_name' => 'l.pdf']);

        $this->assertCount(1, $owner->kycDocuments);
        $this->assertCount(1, $professional->kycDocuments);
    }

    public function test_unit_ownership_links_owner_to_unit(): void
    {
        $owner = Owner::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $community = Community::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $building = Building::factory()->create(['rf_community_id' => $community->id, 'account_tenant_id' => $this->tenant->id]);
        $unit = Unit::factory()->create(['rf_community_id' => $community->id, 'rf_building_id' => $building->id, 'account_tenant_id' => $this->tenant->id]);

        $ownership = UnitOwnership::create([
            'account_tenant_id' => $this->tenant->id,
            'owner_id' => $owner->id,
            'unit_id' => $unit->id,
            'ownership_type' => 'full',
            'ownership_percentage' => 100,
            'start_date' => now(),
        ]);

        $this->assertDatabaseHas('rf_unit_ownerships', ['owner_id' => $owner->id, 'unit_id' => $unit->id]);
        $this->assertCount(1, $owner->unitOwnerships);
        $this->assertCount(1, $owner->ownedUnits);
    }

    public function test_unit_ownership_enforces_unique_owner_unit_pair(): void
    {
        $owner = Owner::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $community = Community::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $building = Building::factory()->create(['rf_community_id' => $community->id, 'account_tenant_id' => $this->tenant->id]);
        $unit = Unit::factory()->create(['rf_community_id' => $community->id, 'rf_building_id' => $building->id, 'account_tenant_id' => $this->tenant->id]);

        UnitOwnership::create(['account_tenant_id' => $this->tenant->id, 'owner_id' => $owner->id, 'unit_id' => $unit->id, 'ownership_type' => 'full']);

        $this->expectException(QueryException::class);

        UnitOwnership::create(['account_tenant_id' => $this->tenant->id, 'owner_id' => $owner->id, 'unit_id' => $unit->id, 'ownership_type' => 'partial']);
    }

    public function test_contact_activity_logs_events(): void
    {
        $resident = Resident::factory()->create(['account_tenant_id' => $this->tenant->id]);

        $resident->activities()->create([
            'account_tenant_id' => $this->tenant->id,
            'event_type' => 'lease_created',
            'metadata' => ['lease_id' => 123],
        ]);

        $resident->activities()->create([
            'account_tenant_id' => $this->tenant->id,
            'event_type' => 'payment_received',
            'metadata' => ['amount' => 5000],
        ]);

        $this->assertCount(2, $resident->activities);
        $this->assertSame('lease_created', $resident->activities->first()->event_type);
    }

    public function test_soft_deletes_archive_and_restore(): void
    {
        $owner = Owner::factory()->create(['account_tenant_id' => $this->tenant->id]);

        $this->assertNull($owner->deleted_at);

        $owner->delete();
        $this->assertNotNull($owner->fresh()->deleted_at);

        $owner->restore();
        $this->assertNull($owner->fresh()->deleted_at);
    }

    public function test_dependents_link_to_resident(): void
    {
        $resident = Resident::factory()->create(['account_tenant_id' => $this->tenant->id]);

        $dependent = $resident->dependents()->create([
            'account_tenant_id' => $this->tenant->id,
            'first_name' => 'Child',
            'last_name' => 'One',
            'relationship' => 'child',
        ]);

        $this->assertInstanceOf(Dependent::class, $dependent);
        $this->assertSame($resident->id, $dependent->dependable_id);
        $this->assertSame(Resident::class, $dependent->dependable_type);
    }

    public function test_professional_has_activities(): void
    {
        $professional = Professional::factory()->create(['account_tenant_id' => $this->tenant->id]);

        $professional->activities()->create([
            'account_tenant_id' => $this->tenant->id,
            'event_type' => 'assigned_to_request',
        ]);

        $this->assertCount(1, $professional->activities);
    }
}
