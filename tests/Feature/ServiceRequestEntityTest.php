<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\Contact;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestCategory;
use App\Models\ServiceRequestSubcategory;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceRequestEntityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create request statuses
        Status::factory()->create([
            'id' => 1,
            'name' => 'New',
            'domain' => 'request',
            'slug' => 'request_new',
        ]);

        Status::factory()->create([
            'id' => 2,
            'name' => 'Assigned',
            'domain' => 'request',
            'slug' => 'request_assigned',
        ]);

        Status::factory()->create([
            'id' => 3,
            'name' => 'Completed',
            'domain' => 'request',
            'slug' => 'request_completed',
        ]);

        Status::factory()->create([
            'id' => 4,
            'name' => 'Canceled',
            'domain' => 'request',
            'slug' => 'request_canceled',
        ]);

        Status::factory()->create([
            'id' => 5,
            'name' => 'In Progress',
            'domain' => 'request',
            'slug' => 'request_in_progress',
        ]);

        Status::factory()->create([
            'id' => 6,
            'name' => 'Accepted',
            'domain' => 'request',
            'slug' => 'request_accepted',
        ]);

        Status::factory()->create([
            'id' => 10,
            'name' => 'Rejected',
            'domain' => 'request',
            'slug' => 'request_rejected',
        ]);
    }

    public function test_service_request_belongs_to_category(): void
    {
        $category = ServiceRequestCategory::factory()->create();
        $request = ServiceRequest::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(ServiceRequestCategory::class, $request->category);
        $this->assertEquals($category->id, $request->category->id);
    }

    public function test_service_request_belongs_to_subcategory(): void
    {
        $subcategory = ServiceRequestSubcategory::factory()->create();
        $request = ServiceRequest::factory()->create(['subcategory_id' => $subcategory->id]);

        $this->assertInstanceOf(ServiceRequestSubcategory::class, $request->subcategory);
        $this->assertEquals($subcategory->id, $request->subcategory->id);
    }

    public function test_service_request_belongs_to_status(): void
    {
        $status = Status::factory()->create(['domain' => 'request']);
        $request = ServiceRequest::factory()->create(['status_id' => $status->id]);

        $this->assertInstanceOf(Status::class, $request->status);
        $this->assertEquals($status->id, $request->status->id);
    }

    public function test_service_request_belongs_to_unit(): void
    {
        $unit = Unit::factory()->create();
        $request = ServiceRequest::factory()->create(['unit_id' => $unit->id]);

        $this->assertInstanceOf(Unit::class, $request->unit);
        $this->assertEquals($unit->id, $request->unit->id);
    }

    public function test_service_request_belongs_to_requester(): void
    {
        $requester = Contact::factory()->create();
        $request = ServiceRequest::factory()->create(['requester_id' => $requester->id]);

        $this->assertInstanceOf(Contact::class, $request->requester);
        $this->assertEquals($requester->id, $request->requester->id);
    }

    public function test_service_request_belongs_to_professional(): void
    {
        $professional = Contact::factory()->create();
        $request = ServiceRequest::factory()->create(['professional_id' => $professional->id]);

        $this->assertInstanceOf(Contact::class, $request->professional);
        $this->assertEquals($professional->id, $request->professional->id);
    }

    public function test_new_scope_returns_only_new_requests(): void
    {
        ServiceRequest::factory()->newRequest()->create();
        ServiceRequest::factory()->newRequest()->create();
        ServiceRequest::factory()->assigned()->create();

        $newRequests = ServiceRequest::new()->get();

        $this->assertCount(2, $newRequests);
        $this->assertTrue($newRequests->every(fn ($request) => $request->isNew()));
    }

    public function test_assigned_scope_returns_only_assigned_requests(): void
    {
        ServiceRequest::factory()->assigned()->create();
        ServiceRequest::factory()->assigned()->create();
        ServiceRequest::factory()->newRequest()->create();

        $assignedRequests = ServiceRequest::assigned()->get();

        $this->assertCount(2, $assignedRequests);
        $this->assertTrue($assignedRequests->every(fn ($request) => $request->isAssigned()));
    }

    public function test_in_progress_scope_returns_only_in_progress_requests(): void
    {
        ServiceRequest::factory()->inProgress()->create();
        ServiceRequest::factory()->inProgress()->create();
        ServiceRequest::factory()->completed()->create();

        $inProgressRequests = ServiceRequest::inProgress()->get();

        $this->assertCount(2, $inProgressRequests);
        $this->assertTrue($inProgressRequests->every(fn ($request) => $request->isInProgress()));
    }

    public function test_completed_scope_returns_only_completed_requests(): void
    {
        ServiceRequest::factory()->completed()->create();
        ServiceRequest::factory()->completed()->create();
        ServiceRequest::factory()->inProgress()->create();

        $completedRequests = ServiceRequest::completed()->get();

        $this->assertCount(2, $completedRequests);
        $this->assertTrue($completedRequests->every(fn ($request) => $request->isCompleted()));
    }

    public function test_canceled_scope_returns_only_canceled_requests(): void
    {
        ServiceRequest::factory()->canceled()->create();
        ServiceRequest::factory()->canceled()->create();
        ServiceRequest::factory()->newRequest()->create();

        $canceledRequests = ServiceRequest::canceled()->get();

        $this->assertCount(2, $canceledRequests);
        $this->assertTrue($canceledRequests->every(fn ($request) => $request->isCanceled()));
    }

    public function test_by_category_scope_filters_by_category(): void
    {
        $category = ServiceRequestCategory::factory()->create();
        ServiceRequest::factory()->forCategory($category->id)->create();
        ServiceRequest::factory()->forCategory($category->id)->create();
        ServiceRequest::factory()->create(); // Different category

        $requests = ServiceRequest::byCategory($category->id)->get();

        $this->assertCount(2, $requests);
        $this->assertTrue($requests->every(fn ($request) => $request->category_id === $category->id));
    }

    public function test_by_priority_scope_filters_by_priority(): void
    {
        ServiceRequest::factory()->highPriority()->create();
        ServiceRequest::factory()->highPriority()->create();
        ServiceRequest::factory()->urgent()->create();

        $highPriorityRequests = ServiceRequest::byPriority('high')->get();

        $this->assertCount(2, $highPriorityRequests);
        $this->assertTrue($highPriorityRequests->every(fn ($request) => $request->priority === 'high'));
    }

    public function test_for_tenant_scope_filters_through_related_property_entities(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();

        $communityA = Community::factory()->forTenant($tenantA)->create();
        $communityB = Community::factory()->forTenant($tenantB)->create();

        $category = ServiceRequestCategory::factory()->create();

        ServiceRequest::factory()->create([
            'category_id' => $category->id,
            'status_id' => 1,
            'community_id' => $communityA->id,
            'building_id' => null,
            'unit_id' => null,
            'requester_id' => Contact::factory()->forTenant($tenantA)->create()->id,
            'created_by' => Contact::factory()->forTenant($tenantA)->create()->id,
        ]);

        ServiceRequest::factory()->create([
            'category_id' => $category->id,
            'status_id' => 1,
            'community_id' => $communityB->id,
            'building_id' => null,
            'unit_id' => null,
            'requester_id' => Contact::factory()->forTenant($tenantB)->create()->id,
            'created_by' => Contact::factory()->forTenant($tenantB)->create()->id,
        ]);

        $tenantARequests = ServiceRequest::forTenant($tenantA->id)->count();
        $tenantBRequests = ServiceRequest::forTenant($tenantB->id)->count();

        $this->assertSame(1, $tenantARequests);
        $this->assertSame(1, $tenantBRequests);
    }

    public function test_overdue_scope_returns_only_overdue_requests(): void
    {
        ServiceRequest::factory()->overdue()->create();
        ServiceRequest::factory()->overdue()->create();
        ServiceRequest::factory()->create(['scheduled_date' => now()->addDays(5)]);

        $overdueRequests = ServiceRequest::overdue()->get();

        $this->assertCount(2, $overdueRequests);
        $this->assertTrue($overdueRequests->every(fn ($request) => $request->isOverdue()));
    }

    public function test_unassigned_scope_returns_only_unassigned_requests(): void
    {
        ServiceRequest::factory()->create(['professional_id' => null]);
        ServiceRequest::factory()->create(['professional_id' => null]);
        ServiceRequest::factory()->assigned()->create();

        $unassignedRequests = ServiceRequest::unassigned()->get();

        $this->assertCount(2, $unassignedRequests);
        $this->assertTrue($unassignedRequests->every(fn ($request) => $request->professional_id === null));
    }

    public function test_is_new_returns_true_for_new_requests(): void
    {
        $request = ServiceRequest::factory()->newRequest()->create();

        $this->assertTrue($request->isNew());
    }

    public function test_is_assigned_returns_true_for_assigned_requests(): void
    {
        $request = ServiceRequest::factory()->assigned()->create();

        $this->assertTrue($request->isAssigned());
    }

    public function test_is_completed_returns_true_for_completed_requests(): void
    {
        $request = ServiceRequest::factory()->completed()->create();

        $this->assertTrue($request->isCompleted());
    }

    public function test_is_canceled_returns_true_for_canceled_requests(): void
    {
        $request = ServiceRequest::factory()->canceled()->create();

        $this->assertTrue($request->isCanceled());
    }

    public function test_is_overdue_returns_true_for_overdue_requests(): void
    {
        $request = ServiceRequest::factory()->overdue()->create();

        $this->assertTrue($request->isOverdue());
    }

    public function test_has_attachments_returns_true_when_attachments_exist(): void
    {
        $request = ServiceRequest::factory()->withAttachments()->create();

        $this->assertTrue($request->hasAttachments());
    }

    public function test_has_rating_returns_true_when_rating_exists(): void
    {
        $request = ServiceRequest::factory()->rated()->create();

        $this->assertTrue($request->hasRating());
    }

    public function test_mark_as_assigned_updates_status_and_professional(): void
    {
        $request = ServiceRequest::factory()->newRequest()->create();
        $professional = Contact::factory()->create();
        $assignedBy = Contact::factory()->create();

        $request->markAsAssigned($professional->id, $assignedBy->id);

        $this->assertEquals($professional->id, $request->professional_id);
        $this->assertEquals($assignedBy->id, $request->assigned_by);
        $this->assertTrue($request->fresh()->isAssigned());
    }

    public function test_mark_as_accepted_updates_status_and_timestamp(): void
    {
        $request = ServiceRequest::factory()->assigned()->create();

        $this->assertNull($request->accepted_at);

        $request->markAsAccepted();

        $this->assertNotNull($request->fresh()->accepted_at);
        $this->assertTrue($request->fresh()->isAccepted());
    }

    public function test_mark_as_in_progress_updates_status_and_timestamp(): void
    {
        $request = ServiceRequest::factory()->accepted()->create();

        $this->assertNull($request->started_at);

        $request->markAsInProgress();

        $this->assertNotNull($request->fresh()->started_at);
        $this->assertTrue($request->fresh()->isInProgress());
    }

    public function test_mark_as_completed_updates_status_and_timestamp(): void
    {
        $request = ServiceRequest::factory()->inProgress()->create();

        $this->assertNull($request->completed_at);

        $request->markAsCompleted(1500.50);

        $freshRequest = $request->fresh();
        $this->assertNotNull($freshRequest->completed_at);
        $this->assertEquals(1500.50, $freshRequest->actual_cost);
        $this->assertTrue($freshRequest->isCompleted());
    }

    public function test_mark_as_canceled_updates_status_and_reason(): void
    {
        $request = ServiceRequest::factory()->newRequest()->create();

        $request->markAsCanceled('Customer request');

        $freshRequest = $request->fresh();
        $this->assertNotNull($freshRequest->canceled_at);
        $this->assertEquals('Customer request', $freshRequest->cancellation_reason);
        $this->assertTrue($freshRequest->isCanceled());
    }

    public function test_mark_as_rejected_updates_status_and_reason(): void
    {
        $request = ServiceRequest::factory()->newRequest()->create();

        $request->markAsRejected('Not enough information');

        $freshRequest = $request->fresh();
        $this->assertEquals('Not enough information', $freshRequest->rejection_reason);
        $this->assertTrue($freshRequest->isRejected());
    }

    public function test_add_rating_updates_rating_and_feedback(): void
    {
        $request = ServiceRequest::factory()->completed()->create();

        $request->addRating(5, 'Excellent service!');

        $freshRequest = $request->fresh();
        $this->assertEquals(5, $freshRequest->rating);
        $this->assertEquals('Excellent service!', $freshRequest->feedback);
    }

    public function test_add_rating_throws_exception_for_invalid_rating(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $request = ServiceRequest::factory()->completed()->create();
        $request->addRating(6);
    }

    public function test_generate_request_number_creates_unique_number(): void
    {
        $request = ServiceRequest::factory()->create();

        $this->assertNotEmpty($request->request_number);
        $this->assertStringStartsWith('SR-', $request->request_number);
    }

    public function test_request_number_is_auto_generated_on_create(): void
    {
        $request = ServiceRequest::factory()->create(['request_number' => null]);

        $this->assertNotNull($request->request_number);
        $this->assertStringStartsWith('SR-', $request->request_number);
    }

    public function test_soft_deletes_work_correctly(): void
    {
        $request = ServiceRequest::factory()->create();
        $requestId = $request->id;

        $request->delete();

        $this->assertSoftDeleted('service_requests', ['id' => $requestId]);
        $this->assertNotNull($request->fresh()->deleted_at);
    }
}
