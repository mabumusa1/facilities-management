<?php

namespace Tests\Feature\ServiceRequests;

use App\Enums\RolesEnum;
use App\Http\Controllers\Services\AdminServiceRequestController;
use App\Models\AccountMembership;
use App\Models\Community;
use App\Models\Request as ServiceRequest;
use App\Models\ServiceCategory;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminTriageTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenant;

    private User $adminUser;

    private Status $assignedStatus;

    private ServiceCategory $category;

    private Community $community;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(RbacSeeder::class);

        $this->adminUser = User::factory()->create();
        $this->tenant = Tenant::create(['name' => 'Admin Triage Test Account']);

        AccountMembership::create([
            'user_id' => $this->adminUser->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);

        $this->adminUser->assignRole(RolesEnum::ACCOUNT_ADMINS->value);

        // Create the "Assigned" status with the reserved ID the controller uses.
        $this->assignedStatus = Status::factory()->create([
            'id' => AdminServiceRequestController::STATUS_ASSIGNED,
            'type' => 'request',
            'name' => 'Assigned',
            'name_en' => 'Assigned',
            'priority' => 2,
        ]);

        $this->community = Community::factory()->create([
            'account_tenant_id' => $this->tenant->id,
        ]);

        $this->category = ServiceCategory::factory()->create([
            'account_tenant_id' => $this->tenant->id,
        ]);

        $this->tenant->makeCurrent();
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------

    public function test_admin_can_view_triage_index(): void
    {
        ServiceRequest::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'service_category_id' => $this->category->id,
            'community_id' => $this->community->id,
            'requester_type' => User::class,
            'requester_id' => $this->adminUser->id,
            'status_id' => $this->assignedStatus->id,
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('services.requests.index'));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('services/requests/admin/Index')
                ->has('serviceRequests')
                ->has('tabCounts')
                ->has('statuses')
                ->has('serviceCategories')
                ->has('communities')
        );
    }

    public function test_unprivileged_user_cannot_access_triage_index(): void
    {
        $unprivileged = User::factory()->create();
        AccountMembership::create([
            'user_id' => $unprivileged->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'tenants',
        ]);

        $response = $this
            ->actingAs($unprivileged)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('services.requests.index'));

        $response->assertForbidden();
    }

    // -------------------------------------------------------------------------
    // Show
    // -------------------------------------------------------------------------

    public function test_admin_can_view_request_detail(): void
    {
        $unit = Unit::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'rf_community_id' => $this->community->id,
        ]);

        $serviceRequest = ServiceRequest::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'service_category_id' => $this->category->id,
            'community_id' => $this->community->id,
            'unit_id' => $unit->id,
            'requester_type' => User::class,
            'requester_id' => $this->adminUser->id,
            'status_id' => $this->assignedStatus->id,
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('services.requests.show', $serviceRequest));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('services/requests/admin/Show')
                ->has('serviceRequest')
                ->has('internalNotes')
                ->has('assignees')
        );
    }

    // -------------------------------------------------------------------------
    // Assign — happy path
    // -------------------------------------------------------------------------

    public function test_admin_can_assign_request_to_admin_in_same_tenant(): void
    {
        $serviceRequest = ServiceRequest::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'service_category_id' => $this->category->id,
            'community_id' => $this->community->id,
            'requester_type' => User::class,
            'requester_id' => $this->adminUser->id,
            'status_id' => $this->assignedStatus->id,
        ]);

        $assignee = User::factory()->create();
        AccountMembership::create([
            'user_id' => $assignee->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->patch(route('services.requests.assign', $serviceRequest), [
                'assigned_to_user_id' => $assignee->id,
                'priority' => 'high',
            ]);

        $response->assertRedirect();

        $serviceRequest->refresh();
        $this->assertEquals($assignee->id, $serviceRequest->assigned_to_user_id);
        $this->assertEquals('high', $serviceRequest->priority);
        $this->assertEquals(AdminServiceRequestController::STATUS_ASSIGNED, $serviceRequest->status_id);
        $this->assertNotNull($serviceRequest->assigned_at);
    }

    // -------------------------------------------------------------------------
    // Assign — security: cross-tenant user must be rejected with 422
    // -------------------------------------------------------------------------

    public function test_admin_cannot_assign_request_to_user_in_other_tenant_returns_422(): void
    {
        $serviceRequest = ServiceRequest::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'service_category_id' => $this->category->id,
            'community_id' => $this->community->id,
            'requester_type' => User::class,
            'requester_id' => $this->adminUser->id,
            'status_id' => $this->assignedStatus->id,
        ]);

        // Create a user in a completely different tenant — not a member of $this->tenant.
        $otherTenant = Tenant::create(['name' => 'Other Tenant']);
        $crossTenantUser = User::factory()->create();
        AccountMembership::create([
            'user_id' => $crossTenantUser->id,
            'account_tenant_id' => $otherTenant->id,
            'role' => 'account_admins',
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->patch(route('services.requests.assign', $serviceRequest), [
                'assigned_to_user_id' => $crossTenantUser->id,
            ]);

        $response->assertUnprocessable();

        // The request must not have been re-assigned.
        $serviceRequest->refresh();
        $this->assertNull($serviceRequest->assigned_to_user_id);
    }

    // -------------------------------------------------------------------------
    // Internal note — happy path
    // -------------------------------------------------------------------------

    public function test_admin_can_add_internal_note(): void
    {
        $serviceRequest = ServiceRequest::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'service_category_id' => $this->category->id,
            'community_id' => $this->community->id,
            'requester_type' => User::class,
            'requester_id' => $this->adminUser->id,
            'status_id' => $this->assignedStatus->id,
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('services.requests.notes.store', $serviceRequest), [
                'body' => 'This is an internal note for the technician.',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('rf_service_request_messages', [
            'service_request_id' => $serviceRequest->id,
            'sender_id' => $this->adminUser->id,
            'body' => 'This is an internal note for the technician.',
            'is_internal' => true,
        ]);
    }
}
