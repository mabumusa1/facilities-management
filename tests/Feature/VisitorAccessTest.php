<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Community;
use App\Models\Contact;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\VisitorAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitorAccessTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;

    protected Community $community;

    protected Building $building;

    protected Unit $unit;

    protected Contact $contact;

    protected Status $pendingStatus;

    protected Status $approvedStatus;

    protected Status $deniedStatus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->community = Community::factory()->create(['tenant_id' => $this->tenant->id]);
        $this->building = Building::factory()->create([
            'tenant_id' => $this->tenant->id,
            'community_id' => $this->community->id,
        ]);
        $this->unit = Unit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'building_id' => $this->building->id,
        ]);
        $this->contact = Contact::factory()->create(['tenant_id' => $this->tenant->id]);

        $this->pendingStatus = Status::factory()->create([
            'domain' => 'visitor',
            'slug' => 'visitor_pending',
            'name' => 'Pending',
        ]);

        $this->approvedStatus = Status::factory()->create([
            'domain' => 'visitor',
            'slug' => 'visitor_approved',
            'name' => 'Approved',
        ]);

        $this->deniedStatus = Status::factory()->create([
            'domain' => 'visitor',
            'slug' => 'visitor_denied',
            'name' => 'Denied',
        ]);
    }

    public function test_can_create_visitor_access(): void
    {
        $visitorAccess = VisitorAccess::factory()->create([
            'tenant_id' => $this->tenant->id,
            'unit_id' => $this->unit->id,
            'building_id' => $this->building->id,
            'community_id' => $this->community->id,
            'requested_by' => $this->contact->id,
            'status_id' => $this->pendingStatus->id,
        ]);

        $this->assertDatabaseHas('visitor_accesses', [
            'id' => $visitorAccess->id,
            'tenant_id' => $this->tenant->id,
            'visitor_name' => $visitorAccess->visitor_name,
        ]);
    }

    public function test_belongs_to_tenant(): void
    {
        $visitorAccess = VisitorAccess::factory()->create([
            'tenant_id' => $this->tenant->id,
            'requested_by' => $this->contact->id,
            'status_id' => $this->pendingStatus->id,
        ]);

        $this->assertInstanceOf(Tenant::class, $visitorAccess->tenant);
        $this->assertEquals($this->tenant->id, $visitorAccess->tenant->id);
    }

    public function test_belongs_to_unit(): void
    {
        $visitorAccess = VisitorAccess::factory()->create([
            'tenant_id' => $this->tenant->id,
            'unit_id' => $this->unit->id,
            'requested_by' => $this->contact->id,
            'status_id' => $this->pendingStatus->id,
        ]);

        $this->assertInstanceOf(Unit::class, $visitorAccess->unit);
        $this->assertEquals($this->unit->id, $visitorAccess->unit->id);
    }

    public function test_belongs_to_status(): void
    {
        $visitorAccess = VisitorAccess::factory()->create([
            'tenant_id' => $this->tenant->id,
            'requested_by' => $this->contact->id,
            'status_id' => $this->pendingStatus->id,
        ]);

        $this->assertInstanceOf(Status::class, $visitorAccess->status);
        $this->assertEquals($this->pendingStatus->id, $visitorAccess->status->id);
    }

    public function test_belongs_to_requested_by_contact(): void
    {
        $visitorAccess = VisitorAccess::factory()->create([
            'tenant_id' => $this->tenant->id,
            'requested_by' => $this->contact->id,
            'status_id' => $this->pendingStatus->id,
        ]);

        $this->assertInstanceOf(Contact::class, $visitorAccess->requestedBy);
        $this->assertEquals($this->contact->id, $visitorAccess->requestedBy->id);
    }

    public function test_is_pending_returns_true_for_pending_status(): void
    {
        $visitorAccess = VisitorAccess::factory()->create([
            'tenant_id' => $this->tenant->id,
            'requested_by' => $this->contact->id,
            'status_id' => $this->pendingStatus->id,
        ]);

        $this->assertTrue($visitorAccess->isPending());
    }

    public function test_is_approved_returns_true_for_approved_status(): void
    {
        $visitorAccess = VisitorAccess::factory()->create([
            'tenant_id' => $this->tenant->id,
            'requested_by' => $this->contact->id,
            'status_id' => $this->approvedStatus->id,
        ]);

        $this->assertTrue($visitorAccess->isApproved());
    }

    public function test_is_denied_returns_true_for_denied_status(): void
    {
        $visitorAccess = VisitorAccess::factory()->create([
            'tenant_id' => $this->tenant->id,
            'requested_by' => $this->contact->id,
            'status_id' => $this->deniedStatus->id,
        ]);

        $this->assertTrue($visitorAccess->isDenied());
    }

    public function test_can_approve_visitor_access(): void
    {
        $visitorAccess = VisitorAccess::factory()->create([
            'tenant_id' => $this->tenant->id,
            'requested_by' => $this->contact->id,
            'status_id' => $this->pendingStatus->id,
        ]);

        $approver = Contact::factory()->create(['tenant_id' => $this->tenant->id]);
        $visitorAccess->approve($approver->id);

        $this->assertDatabaseHas('visitor_accesses', [
            'id' => $visitorAccess->id,
            'status_id' => $this->approvedStatus->id,
            'approved_by' => $approver->id,
        ]);

        $this->assertNotNull($visitorAccess->fresh()->approved_at);
    }

    public function test_can_deny_visitor_access(): void
    {
        $visitorAccess = VisitorAccess::factory()->create([
            'tenant_id' => $this->tenant->id,
            'requested_by' => $this->contact->id,
            'status_id' => $this->pendingStatus->id,
        ]);

        $denier = Contact::factory()->create(['tenant_id' => $this->tenant->id]);
        $reason = 'Invalid ID provided';
        $visitorAccess->deny($reason, $denier->id);

        $this->assertDatabaseHas('visitor_accesses', [
            'id' => $visitorAccess->id,
            'status_id' => $this->deniedStatus->id,
            'approved_by' => $denier->id,
            'rejection_reason' => $reason,
        ]);

        $this->assertNotNull($visitorAccess->fresh()->approved_at);
    }

    public function test_is_active_returns_true_for_current_approved_visit(): void
    {
        $visitorAccess = VisitorAccess::factory()->create([
            'tenant_id' => $this->tenant->id,
            'requested_by' => $this->contact->id,
            'status_id' => $this->approvedStatus->id,
            'visit_start_date' => now()->subDay(),
            'visit_end_date' => now()->addDay(),
        ]);

        $this->assertTrue($visitorAccess->isActive());
    }

    public function test_is_active_returns_false_for_past_visit(): void
    {
        $visitorAccess = VisitorAccess::factory()->create([
            'tenant_id' => $this->tenant->id,
            'requested_by' => $this->contact->id,
            'status_id' => $this->approvedStatus->id,
            'visit_start_date' => now()->subDays(5),
            'visit_end_date' => now()->subDays(3),
        ]);

        $this->assertFalse($visitorAccess->isActive());
    }

    public function test_is_active_returns_false_for_pending_visit(): void
    {
        $visitorAccess = VisitorAccess::factory()->create([
            'tenant_id' => $this->tenant->id,
            'requested_by' => $this->contact->id,
            'status_id' => $this->pendingStatus->id,
            'visit_start_date' => now()->subDay(),
            'visit_end_date' => now()->addDay(),
        ]);

        $this->assertFalse($visitorAccess->isActive());
    }

    public function test_soft_deletes_visitor_access(): void
    {
        $visitorAccess = VisitorAccess::factory()->create([
            'tenant_id' => $this->tenant->id,
            'requested_by' => $this->contact->id,
            'status_id' => $this->pendingStatus->id,
        ]);

        $visitorAccess->delete();

        $this->assertSoftDeleted('visitor_accesses', ['id' => $visitorAccess->id]);
    }
}
