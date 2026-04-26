<?php

namespace Tests\Feature\ServiceRequests;

use App\Models\AccountMembership;
use App\Models\Community;
use App\Models\Request as ServiceRequest;
use App\Models\ServiceCategory;
use App\Models\ServiceSubcategory;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ResidentServiceRequestTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenant;

    private User $user;

    private Status $openStatus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->user = User::factory()->create();
        $this->tenant = Tenant::create(['name' => 'SR Resident Test Account']);

        AccountMembership::create([
            'user_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'tenants',
        ]);

        $this->openStatus = Status::factory()->create([
            'type' => 'request',
            'name' => 'Open',
            'name_en' => 'Open',
            'priority' => 1,
        ]);

        $this->tenant->makeCurrent();
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    // -------------------------------------------------------------------------
    // Guest redirect
    // -------------------------------------------------------------------------

    public function test_guests_are_redirected_to_login_on_create_page(): void
    {
        $response = $this->get(route('service-requests.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_to_login_on_index_page(): void
    {
        $response = $this->get(route('service-requests.index'));

        $response->assertRedirect(route('login'));
    }

    // -------------------------------------------------------------------------
    // Create page (GET)
    // -------------------------------------------------------------------------

    public function test_authenticated_user_can_view_create_form(): void
    {
        ServiceCategory::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'name_en' => 'Plumbing',
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('service-requests.create'));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('services/requests/Create')
                ->has('categories')
                ->has('communities')
                ->has('units')
                ->has('roomOptions')
        );
    }

    // -------------------------------------------------------------------------
    // Store (POST) — happy path (Scenario 1 + Scenario 4)
    // -------------------------------------------------------------------------

    public function test_resident_can_submit_service_request_and_sla_is_calculated(): void
    {
        $community = Community::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $unit = Unit::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'rf_community_id' => $community->id,
        ]);
        $category = ServiceCategory::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'name_en' => 'Plumbing',
            'response_sla_hours' => 4,
            'resolution_sla_hours' => 24,
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('service-requests.store'), [
                'service_category_id' => $category->id,
                'community_id' => $community->id,
                'unit_id' => $unit->id,
                'urgency' => 'normal',
                'description' => 'Water is dripping from the kitchen ceiling.',
            ]);

        $response->assertRedirectToRoute('service-requests.created', ['serviceRequest' => 1]);

        $serviceRequest = ServiceRequest::first();
        $this->assertNotNull($serviceRequest);
        $this->assertEquals($category->id, $serviceRequest->service_category_id);
        $this->assertEquals($community->id, $serviceRequest->community_id);
        $this->assertEquals($unit->id, $serviceRequest->unit_id);
        $this->assertEquals('normal', $serviceRequest->urgency);
        $this->assertEquals('Water is dripping from the kitchen ceiling.', $serviceRequest->description);
        $this->assertEquals($this->openStatus->id, $serviceRequest->status_id);
        $this->assertNotNull($serviceRequest->request_code);
        $this->assertMatchesRegularExpression('/^SR-\d{4}-\d{5}$/', $serviceRequest->request_code);
        $this->assertNotNull($serviceRequest->sla_response_due_at);
        $this->assertNotNull($serviceRequest->sla_resolution_due_at);
    }

    public function test_subcategory_sla_overrides_category_sla(): void
    {
        $community = Community::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $unit = Unit::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'rf_community_id' => $community->id,
        ]);
        $category = ServiceCategory::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'response_sla_hours' => 8,
            'resolution_sla_hours' => 48,
            'status' => 'active',
        ]);
        $subcategory = ServiceSubcategory::factory()->create([
            'service_category_id' => $category->id,
            'response_sla_hours' => 2,
            'resolution_sla_hours' => 12,
            'status' => 'active',
        ]);

        $this
            ->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('service-requests.store'), [
                'service_category_id' => $category->id,
                'service_subcategory_id' => $subcategory->id,
                'community_id' => $community->id,
                'unit_id' => $unit->id,
                'urgency' => 'normal',
                'description' => 'Power outlet stopped working.',
            ]);

        $serviceRequest = ServiceRequest::first();
        $this->assertNotNull($serviceRequest);
        $this->assertEquals($subcategory->id, $serviceRequest->service_subcategory_id);

        // Subcategory SLA (2h response, 12h resolution) should override category (8h, 48h)
        $this->assertEqualsWithDelta(2, $serviceRequest->sla_response_due_at->diffInHours(now()), 1);
        $this->assertEqualsWithDelta(12, $serviceRequest->sla_resolution_due_at->diffInHours(now()), 1);
    }

    // -------------------------------------------------------------------------
    // Validation failures
    // -------------------------------------------------------------------------

    public function test_store_rejects_missing_required_fields(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('service-requests.store'), []);

        $response->assertSessionHasErrors(['service_category_id', 'unit_id', 'community_id', 'description']);
    }

    public function test_store_rejects_description_shorter_than_10_chars(): void
    {
        $community = Community::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $unit = Unit::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'rf_community_id' => $community->id,
        ]);
        $category = ServiceCategory::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('service-requests.store'), [
                'service_category_id' => $category->id,
                'community_id' => $community->id,
                'unit_id' => $unit->id,
                'urgency' => 'normal',
                'description' => 'Too short',
            ]);

        $response->assertSessionHasErrors(['description']);
    }

    public function test_store_rejects_invalid_urgency(): void
    {
        $community = Community::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $unit = Unit::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'rf_community_id' => $community->id,
        ]);
        $category = ServiceCategory::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('service-requests.store'), [
                'service_category_id' => $category->id,
                'community_id' => $community->id,
                'unit_id' => $unit->id,
                'urgency' => 'critical',
                'description' => 'Water is dripping from the kitchen ceiling.',
            ]);

        $response->assertSessionHasErrors(['urgency']);
    }

    // -------------------------------------------------------------------------
    // Confirmation page (GET)
    // -------------------------------------------------------------------------

    public function test_created_page_is_accessible_by_the_requester(): void
    {
        $community = Community::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $unit = Unit::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'rf_community_id' => $community->id,
        ]);
        $category = ServiceCategory::factory()->create(['account_tenant_id' => $this->tenant->id, 'status' => 'active']);

        $serviceRequest = ServiceRequest::factory()->create([
            'requester_type' => User::class,
            'requester_id' => $this->user->id,
            'service_category_id' => $category->id,
            'unit_id' => $unit->id,
            'community_id' => $community->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $response = $this
            ->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('service-requests.created', $serviceRequest));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('services/requests/Created')
                ->where('serviceRequest.id', $serviceRequest->id)
                ->where('serviceRequest.request_code', $serviceRequest->request_code)
        );
    }

    public function test_created_page_is_not_accessible_by_another_user(): void
    {
        $otherUser = User::factory()->create();
        AccountMembership::create([
            'user_id' => $otherUser->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'tenants',
        ]);

        $serviceRequest = ServiceRequest::factory()->create([
            'requester_type' => User::class,
            'requester_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $response = $this
            ->actingAs($otherUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('service-requests.created', $serviceRequest));

        $response->assertForbidden();
    }

    // -------------------------------------------------------------------------
    // My Requests index (Scenario 5)
    // -------------------------------------------------------------------------

    public function test_resident_can_view_own_requests_list(): void
    {
        $category = ServiceCategory::factory()->create(['account_tenant_id' => $this->tenant->id, 'status' => 'active']);
        $community = Community::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $unit = Unit::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'rf_community_id' => $community->id,
        ]);

        ServiceRequest::factory()->create([
            'requester_type' => User::class,
            'requester_id' => $this->user->id,
            'service_category_id' => $category->id,
            'unit_id' => $unit->id,
            'community_id' => $community->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        // Another user's request — should not appear
        $anotherUser = User::factory()->create();
        ServiceRequest::factory()->create([
            'requester_type' => User::class,
            'requester_id' => $anotherUser->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $response = $this
            ->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('service-requests.index'));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('services/requests/Index')
                ->has('serviceRequests.data', 1)
        );
    }
}
