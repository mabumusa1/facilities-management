<?php

namespace Tests\Feature\ServiceRequests;

use App\Models\AccountMembership;
use App\Models\Request as ServiceRequest;
use App\Models\Resident;
use App\Models\ServiceRequestMessage;
use App\Models\ServiceRequestTimelineEvent;
use App\Models\Tenant;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ServiceRequestDataModelTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenant;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->tenant = Tenant::create(['name' => 'SR Test Tenant']);

        AccountMembership::create([
            'user_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);

        $this->tenant->makeCurrent();
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    // -------------------------------------------------------------------------
    // request_code auto-generation
    // -------------------------------------------------------------------------

    public function test_request_code_is_auto_generated_on_create(): void
    {
        $request = ServiceRequest::factory()->create();

        $this->assertNotNull($request->request_code);
        $this->assertMatchesRegularExpression('/^SR-\d{4}-\d{5}$/', $request->request_code);
    }

    public function test_request_code_includes_current_year(): void
    {
        $request = ServiceRequest::factory()->create();

        $year = now()->year;
        $this->assertStringStartsWith("SR-{$year}-", $request->request_code);
    }

    public function test_request_codes_are_unique_per_tenant(): void
    {
        $first = ServiceRequest::factory()->create();
        $second = ServiceRequest::factory()->create();

        $this->assertNotEquals($first->request_code, $second->request_code);
        $this->assertEquals('SR-'.now()->year.'-00001', $first->request_code);
        $this->assertEquals('SR-'.now()->year.'-00002', $second->request_code);
    }

    public function test_request_code_sequences_are_scoped_per_tenant(): void
    {
        $otherTenant = Tenant::create(['name' => 'SR Other Tenant']);

        // First request in primary tenant
        $firstRequest = ServiceRequest::factory()->create();
        $this->assertEquals('SR-'.now()->year.'-00001', $firstRequest->request_code);

        // Switch to other tenant — sequence resets
        Tenant::forgetCurrent();
        $otherTenant->makeCurrent();

        $otherRequest = ServiceRequest::factory()->create();
        $this->assertEquals('SR-'.now()->year.'-00001', $otherRequest->request_code);

        // Restore primary tenant
        Tenant::forgetCurrent();
        $this->tenant->makeCurrent();
    }

    public function test_existing_request_code_is_not_overwritten(): void
    {
        $request = ServiceRequest::factory()->create(['request_code' => 'SR-2024-00999']);

        $this->assertEquals('SR-2024-00999', $request->request_code);
    }

    // -------------------------------------------------------------------------
    // New rf_requests columns
    // -------------------------------------------------------------------------

    public function test_scheduled_date_and_completed_date_are_castable(): void
    {
        $request = ServiceRequest::factory()->create([
            'scheduled_date' => '2026-05-01',
            'completed_date' => '2026-05-15',
        ]);

        $request->refresh();

        $this->assertInstanceOf(CarbonInterface::class, $request->scheduled_date);
        $this->assertInstanceOf(CarbonInterface::class, $request->completed_date);
        $this->assertEquals('2026-05-01', $request->scheduled_date->toDateString());
        $this->assertEquals('2026-05-15', $request->completed_date->toDateString());
    }

    // -------------------------------------------------------------------------
    // ServiceRequestMessage relationships
    // -------------------------------------------------------------------------

    public function test_request_has_many_messages(): void
    {
        $request = ServiceRequest::factory()->create();

        ServiceRequestMessage::factory()->count(2)->create([
            'service_request_id' => $request->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $this->assertCount(2, $request->messages);
    }

    public function test_message_belongs_to_service_request(): void
    {
        $request = ServiceRequest::factory()->create();
        $message = ServiceRequestMessage::factory()->create([
            'service_request_id' => $request->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $this->assertTrue($message->serviceRequest->is($request));
    }

    public function test_message_polymorphic_sender(): void
    {
        $resident = Resident::factory()->create();
        $request = ServiceRequest::factory()->create();

        $message = ServiceRequestMessage::factory()->create([
            'service_request_id' => $request->id,
            'sender_type' => Resident::class,
            'sender_id' => $resident->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $this->assertInstanceOf(Resident::class, $message->sender);
        $this->assertTrue($message->sender->is($resident));
    }

    public function test_message_internal_state(): void
    {
        $request = ServiceRequest::factory()->create();

        $public = ServiceRequestMessage::factory()->create([
            'service_request_id' => $request->id,
            'account_tenant_id' => $this->tenant->id,
            'is_internal' => false,
        ]);

        $internal = ServiceRequestMessage::factory()->internal()->create([
            'service_request_id' => $request->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $this->assertFalse($public->is_internal);
        $this->assertTrue($internal->is_internal);
    }

    public function test_message_tenant_scope_filters_cross_tenant(): void
    {
        $otherTenant = Tenant::create(['name' => 'SR Cross-Tenant Test']);

        $request = ServiceRequest::factory()->create();

        ServiceRequestMessage::factory()->create([
            'service_request_id' => $request->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        Tenant::forgetCurrent();
        $otherTenant->makeCurrent();

        // Other tenant sees 0 messages
        $this->assertEquals(0, ServiceRequestMessage::count());

        Tenant::forgetCurrent();
        $this->tenant->makeCurrent();
    }

    // -------------------------------------------------------------------------
    // ServiceRequestTimelineEvent relationships
    // -------------------------------------------------------------------------

    public function test_request_has_many_timeline_events(): void
    {
        $request = ServiceRequest::factory()->create();

        ServiceRequestTimelineEvent::factory()->count(3)->create([
            'service_request_id' => $request->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $this->assertCount(3, $request->timelineEvents);
    }

    public function test_timeline_event_belongs_to_service_request(): void
    {
        $request = ServiceRequest::factory()->create();
        $event = ServiceRequestTimelineEvent::factory()->create([
            'service_request_id' => $request->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $this->assertTrue($event->serviceRequest->is($request));
    }

    public function test_timeline_event_polymorphic_actor(): void
    {
        $resident = Resident::factory()->create();
        $request = ServiceRequest::factory()->create();

        $event = ServiceRequestTimelineEvent::factory()->create([
            'service_request_id' => $request->id,
            'actor_type' => Resident::class,
            'actor_id' => $resident->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $this->assertInstanceOf(Resident::class, $event->actor);
        $this->assertTrue($event->actor->is($resident));
    }

    public function test_timeline_event_factory_states(): void
    {
        $request = ServiceRequest::factory()->create();

        $submitted = ServiceRequestTimelineEvent::factory()->submitted()->create([
            'service_request_id' => $request->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $resolved = ServiceRequestTimelineEvent::factory()->resolved()->create([
            'service_request_id' => $request->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $this->assertEquals('submitted', $submitted->event_type);
        $this->assertEquals('resolved', $resolved->event_type);
    }

    public function test_timeline_event_metadata_is_cast_to_array(): void
    {
        $request = ServiceRequest::factory()->create();

        $event = ServiceRequestTimelineEvent::factory()->create([
            'service_request_id' => $request->id,
            'account_tenant_id' => $this->tenant->id,
            'metadata' => ['note' => 'Parts ordered', 'estimated_days' => 3],
        ]);

        $event->refresh();

        $this->assertIsArray($event->metadata);
        $this->assertEquals('Parts ordered', $event->metadata['note']);
    }

    public function test_timeline_event_tenant_scope_filters_cross_tenant(): void
    {
        $otherTenant = Tenant::create(['name' => 'SR Cross-Tenant Timeline Test']);

        $request = ServiceRequest::factory()->create();

        ServiceRequestTimelineEvent::factory()->create([
            'service_request_id' => $request->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        Tenant::forgetCurrent();
        $otherTenant->makeCurrent();

        // Other tenant sees 0 timeline events
        $this->assertEquals(0, ServiceRequestTimelineEvent::count());

        Tenant::forgetCurrent();
        $this->tenant->makeCurrent();
    }
}
