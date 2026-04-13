<?php

namespace Tests\Feature;

use App\Events\ServiceRequest\ServiceRequestAccepted;
use App\Events\ServiceRequest\ServiceRequestAssigned;
use App\Events\ServiceRequest\ServiceRequestCanceled;
use App\Events\ServiceRequest\ServiceRequestCompleted;
use App\Events\ServiceRequest\ServiceRequestInProgress;
use App\Events\ServiceRequest\ServiceRequestRejected;
use App\Models\Contact;
use App\Models\ServiceRequest;
use App\Models\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ServiceRequestLifecycleTest extends TestCase
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

    public function test_mark_as_assigned_creates_state_history(): void
    {
        $request = ServiceRequest::factory()->newRequest()->create();
        $professional = Contact::factory()->create();
        $assignedBy = Contact::factory()->create();

        $this->assertCount(0, $request->stateHistory);

        $request->markAsAssigned($professional->id, $assignedBy->id);

        $request->refresh();
        $this->assertCount(1, $request->stateHistory);

        $history = $request->stateHistory->first();
        $this->assertEquals($request->id, $history->service_request_id);
        $this->assertEquals(1, $history->from_status_id); // New
        $this->assertEquals(2, $history->to_status_id); // Assigned
        $this->assertEquals($assignedBy->id, $history->changed_by);
        $this->assertEquals('Request assigned to professional', $history->notes);
    }

    public function test_mark_as_assigned_dispatches_event(): void
    {
        Event::fake();

        $request = ServiceRequest::factory()->newRequest()->create();
        $professional = Contact::factory()->create();

        $request->markAsAssigned($professional->id);

        Event::assertDispatched(ServiceRequestAssigned::class, function ($event) use ($request) {
            return $event->serviceRequest->id === $request->id;
        });
    }

    public function test_mark_as_accepted_creates_state_history(): void
    {
        $request = ServiceRequest::factory()->assigned()->create();

        $request->markAsAccepted();

        $request->refresh();
        $this->assertCount(1, $request->stateHistory);

        $history = $request->stateHistory->first();
        $this->assertEquals(2, $history->from_status_id); // Assigned
        $this->assertEquals(6, $history->to_status_id); // Accepted
        $this->assertEquals('Request accepted by professional', $history->notes);
    }

    public function test_mark_as_accepted_dispatches_event(): void
    {
        Event::fake();

        $request = ServiceRequest::factory()->assigned()->create();

        $request->markAsAccepted();

        Event::assertDispatched(ServiceRequestAccepted::class);
    }

    public function test_mark_as_in_progress_creates_state_history(): void
    {
        $request = ServiceRequest::factory()->accepted()->create();

        $request->markAsInProgress();

        $request->refresh();
        $this->assertCount(1, $request->stateHistory);

        $history = $request->stateHistory->first();
        $this->assertEquals(6, $history->from_status_id); // Accepted
        $this->assertEquals(5, $history->to_status_id); // In Progress
        $this->assertEquals('Work started on request', $history->notes);
    }

    public function test_mark_as_in_progress_dispatches_event(): void
    {
        Event::fake();

        $request = ServiceRequest::factory()->accepted()->create();

        $request->markAsInProgress();

        Event::assertDispatched(ServiceRequestInProgress::class);
    }

    public function test_mark_as_completed_creates_state_history(): void
    {
        $request = ServiceRequest::factory()->inProgress()->create();

        $request->markAsCompleted(1500.50);

        $request->refresh();
        $this->assertCount(1, $request->stateHistory);

        $history = $request->stateHistory->first();
        $this->assertEquals(5, $history->from_status_id); // In Progress
        $this->assertEquals(3, $history->to_status_id); // Completed
        $this->assertEquals('Request completed', $history->notes);
        $this->assertEquals(['actual_cost' => 1500.50], $history->metadata);
    }

    public function test_mark_as_completed_dispatches_event(): void
    {
        Event::fake();

        $request = ServiceRequest::factory()->inProgress()->create();

        $request->markAsCompleted();

        Event::assertDispatched(ServiceRequestCompleted::class);
    }

    public function test_mark_as_canceled_creates_state_history(): void
    {
        $request = ServiceRequest::factory()->newRequest()->create();

        $request->markAsCanceled('Customer requested cancellation');

        $request->refresh();
        $this->assertCount(1, $request->stateHistory);

        $history = $request->stateHistory->first();
        $this->assertEquals(1, $history->from_status_id); // New
        $this->assertEquals(4, $history->to_status_id); // Canceled
        $this->assertEquals('Customer requested cancellation', $history->notes);
    }

    public function test_mark_as_canceled_dispatches_event(): void
    {
        Event::fake();

        $request = ServiceRequest::factory()->newRequest()->create();

        $request->markAsCanceled('Test cancellation');

        Event::assertDispatched(ServiceRequestCanceled::class);
    }

    public function test_mark_as_rejected_creates_state_history(): void
    {
        $request = ServiceRequest::factory()->newRequest()->create();

        $request->markAsRejected('Insufficient information provided');

        $request->refresh();
        $this->assertCount(1, $request->stateHistory);

        $history = $request->stateHistory->first();
        $this->assertEquals(1, $history->from_status_id); // New
        $this->assertEquals(10, $history->to_status_id); // Rejected
        $this->assertEquals('Insufficient information provided', $history->notes);
    }

    public function test_mark_as_rejected_dispatches_event(): void
    {
        Event::fake();

        $request = ServiceRequest::factory()->newRequest()->create();

        $request->markAsRejected('Test rejection');

        Event::assertDispatched(ServiceRequestRejected::class);
    }

    public function test_complete_lifecycle_creates_multiple_history_entries(): void
    {
        $request = ServiceRequest::factory()->newRequest()->create();
        $professional = Contact::factory()->create();
        $assignedBy = Contact::factory()->create();

        $this->assertCount(0, $request->stateHistory);

        // New -> Assigned
        $request->markAsAssigned($professional->id, $assignedBy->id);
        $this->assertCount(1, $request->fresh()->stateHistory);

        // Assigned -> Accepted
        $request->markAsAccepted();
        $this->assertCount(2, $request->fresh()->stateHistory);

        // Accepted -> In Progress
        $request->markAsInProgress();
        $this->assertCount(3, $request->fresh()->stateHistory);

        // In Progress -> Completed
        $request->markAsCompleted(2000.00);
        $this->assertCount(4, $request->fresh()->stateHistory);

        $history = $request->fresh()->stateHistory->sortBy('created_at')->values();

        // Verify the complete lifecycle
        $this->assertEquals(1, $history[0]->from_status_id); // New
        $this->assertEquals(2, $history[0]->to_status_id); // Assigned

        $this->assertEquals(2, $history[1]->from_status_id); // Assigned
        $this->assertEquals(6, $history[1]->to_status_id); // Accepted

        $this->assertEquals(6, $history[2]->from_status_id); // Accepted
        $this->assertEquals(5, $history[2]->to_status_id); // In Progress

        $this->assertEquals(5, $history[3]->from_status_id); // In Progress
        $this->assertEquals(3, $history[3]->to_status_id); // Completed
    }

    public function test_state_history_has_relationships(): void
    {
        $request = ServiceRequest::factory()->newRequest()->create();
        $professional = Contact::factory()->create();
        $assignedBy = Contact::factory()->create();

        $request->markAsAssigned($professional->id, $assignedBy->id);

        $history = $request->fresh()->stateHistory->first();

        $this->assertInstanceOf(ServiceRequest::class, $history->serviceRequest);
        $this->assertInstanceOf(Status::class, $history->fromStatus);
        $this->assertInstanceOf(Status::class, $history->toStatus);
        $this->assertInstanceOf(Contact::class, $history->changedBy);
    }

    public function test_all_lifecycle_events_are_dispatched_in_order(): void
    {
        Event::fake();

        $request = ServiceRequest::factory()->newRequest()->create();
        $professional = Contact::factory()->create();
        $assignedBy = Contact::factory()->create();

        $request->markAsAssigned($professional->id, $assignedBy->id);
        $request->markAsAccepted();
        $request->markAsInProgress();
        $request->markAsCompleted();

        Event::assertDispatched(ServiceRequestAssigned::class);
        Event::assertDispatched(ServiceRequestAccepted::class);
        Event::assertDispatched(ServiceRequestInProgress::class);
        Event::assertDispatched(ServiceRequestCompleted::class);
    }

    public function test_cancellation_can_occur_from_any_state(): void
    {
        $newRequest = ServiceRequest::factory()->newRequest()->create();
        $assignedRequest = ServiceRequest::factory()->assigned()->create();
        $inProgressRequest = ServiceRequest::factory()->inProgress()->create();

        $newRequest->markAsCanceled('Canceled from new');
        $assignedRequest->markAsCanceled('Canceled from assigned');
        $inProgressRequest->markAsCanceled('Canceled from in progress');

        $this->assertTrue($newRequest->fresh()->isCanceled());
        $this->assertTrue($assignedRequest->fresh()->isCanceled());
        $this->assertTrue($inProgressRequest->fresh()->isCanceled());
    }
}
