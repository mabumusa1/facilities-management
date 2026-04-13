<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Community;
use App\Models\Contact;
use App\Models\LeaseApplication;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LeaseApplicationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Tenant $tenant;

    protected Community $community;

    protected Building $building;

    protected Unit $unit;

    protected Contact $tenantContact;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        // Create unit statuses FIRST (before units)
        Status::factory()->create([
            'id' => 26,
            'name' => 'Available',
            'domain' => 'unit',
            'slug' => 'unit_available',
        ]);
        Status::factory()->create([
            'id' => 27,
            'name' => 'Rented',
            'domain' => 'unit',
            'slug' => 'unit_rented',
        ]);

        // Create tenant and user
        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        // Create property hierarchy
        $this->community = Community::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);
        $this->building = Building::factory()->create([
            'tenant_id' => $this->tenant->id,
            'community_id' => $this->community->id,
        ]);
        $this->unit = Unit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => 26, // Available
        ]);

        // Create tenant contact
        $this->tenantContact = Contact::factory()->tenant()->create([
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_index_displays_applications_list(): void
    {
        LeaseApplication::factory()->count(3)->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/lease-applications');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('lease-applications/index')
            ->has('applications.data', 3)
            ->has('statistics')
            ->has('filters')
            ->has('statuses')
        );
    }

    public function test_index_can_filter_by_status(): void
    {
        LeaseApplication::factory()->count(2)->create([
            'tenant_id' => $this->tenant->id,
            'status' => LeaseApplication::STATUS_DRAFT,
        ]);
        LeaseApplication::factory()->create([
            'tenant_id' => $this->tenant->id,
            'status' => LeaseApplication::STATUS_APPROVED,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/lease-applications?status=draft');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('lease-applications/index')
            ->has('applications.data', 2)
        );
    }

    public function test_index_can_search_applications(): void
    {
        LeaseApplication::factory()->create([
            'tenant_id' => $this->tenant->id,
            'applicant_name' => 'John Doe',
        ]);
        LeaseApplication::factory()->create([
            'tenant_id' => $this->tenant->id,
            'applicant_name' => 'Jane Smith',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/lease-applications?search=John');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('lease-applications/index')
            ->has('applications.data', 1)
        );
    }

    public function test_create_displays_form(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/lease-applications/create');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('lease-applications/create')
            ->has('communities')
            ->has('buildings')
            ->has('availableUnits')
            ->has('contacts')
            ->has('sources')
        );
    }

    public function test_store_creates_new_application(): void
    {
        $applicationData = [
            'applicant_name' => 'Test Applicant',
            'applicant_email' => 'test@example.com',
            'applicant_phone' => '1234567890',
            'applicant_type' => 'individual',
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'quoted_rental_amount' => 50000,
            'security_deposit' => 10000,
            'proposed_start_date' => now()->addDays(30)->format('Y-m-d'),
            'proposed_end_date' => now()->addYear()->format('Y-m-d'),
            'proposed_duration_months' => 12,
            'source' => LeaseApplication::SOURCE_WALK_IN,
        ];

        $response = $this->actingAs($this->user)
            ->post('/lease-applications', $applicationData);

        $response->assertRedirect();
        $this->assertDatabaseHas('lease_applications', [
            'applicant_name' => 'Test Applicant',
            'applicant_email' => 'test@example.com',
            'status' => LeaseApplication::STATUS_DRAFT,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/lease-applications', []);

        $response->assertSessionHasErrors(['applicant_name', 'applicant_email', 'applicant_type']);
    }

    public function test_store_creates_application_with_units(): void
    {
        $unit2 = Unit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => 26,
        ]);

        $applicationData = [
            'applicant_name' => 'Test Applicant',
            'applicant_email' => 'test@example.com',
            'applicant_type' => 'individual',
            'units' => [
                ['id' => $this->unit->id, 'proposed_rental_amount' => 25000],
                ['id' => $unit2->id, 'proposed_rental_amount' => 30000],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post('/lease-applications', $applicationData);

        $response->assertRedirect();

        $application = LeaseApplication::where('applicant_email', 'test@example.com')->first();
        $this->assertNotNull($application);
        $this->assertCount(2, $application->units);
    }

    public function test_show_displays_application_details(): void
    {
        $application = LeaseApplication::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get("/lease-applications/{$application->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('lease-applications/show')
            ->has('application')
            ->has('allowedTransitions')
            ->has('stateHistory')
            ->has('canConvert')
        );
    }

    public function test_edit_displays_form(): void
    {
        $application = LeaseApplication::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get("/lease-applications/{$application->id}/edit");

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('lease-applications/edit')
            ->has('application')
            ->has('communities')
            ->has('buildings')
        );
    }

    public function test_update_modifies_application(): void
    {
        $application = LeaseApplication::factory()->create([
            'tenant_id' => $this->tenant->id,
            'applicant_name' => 'Original Name',
        ]);

        $response = $this->actingAs($this->user)
            ->put("/lease-applications/{$application->id}", [
                'applicant_name' => 'Updated Name',
                'applicant_email' => $application->applicant_email,
                'applicant_type' => $application->applicant_type,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lease_applications', [
            'id' => $application->id,
            'applicant_name' => 'Updated Name',
        ]);
    }

    public function test_destroy_deletes_application(): void
    {
        $application = LeaseApplication::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete("/lease-applications/{$application->id}");

        $response->assertRedirect('/lease-applications');
        $this->assertSoftDeleted('lease_applications', [
            'id' => $application->id,
        ]);
    }

    public function test_submit_for_review_transitions_draft_to_review(): void
    {
        $application = LeaseApplication::factory()->create([
            'tenant_id' => $this->tenant->id,
            'status' => LeaseApplication::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($this->user)
            ->post("/lease-applications/{$application->id}/submit-for-review");

        $response->assertRedirect();
        $application->refresh();
        $this->assertEquals(LeaseApplication::STATUS_REVIEW, $application->status);
    }

    public function test_approve_transitions_review_to_approved(): void
    {
        $application = LeaseApplication::factory()->underReview()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post("/lease-applications/{$application->id}/approve", [
                'notes' => 'Application meets all requirements.',
            ]);

        $response->assertRedirect();
        $application->refresh();
        $this->assertEquals(LeaseApplication::STATUS_APPROVED, $application->status);
        $this->assertNotNull($application->reviewed_at);
    }

    public function test_reject_transitions_review_to_rejected(): void
    {
        $application = LeaseApplication::factory()->underReview()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post("/lease-applications/{$application->id}/reject", [
                'reason' => 'Insufficient documentation provided.',
            ]);

        $response->assertRedirect();
        $application->refresh();
        $this->assertEquals(LeaseApplication::STATUS_REJECTED, $application->status);
        $this->assertEquals('Insufficient documentation provided.', $application->rejection_reason);
    }

    public function test_reject_requires_reason(): void
    {
        $application = LeaseApplication::factory()->underReview()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post("/lease-applications/{$application->id}/reject", []);

        $response->assertSessionHasErrors(['reason']);
    }

    public function test_cancel_transitions_to_cancelled(): void
    {
        $application = LeaseApplication::factory()->inProgress()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post("/lease-applications/{$application->id}/cancel", [
                'notes' => 'Applicant withdrew.',
            ]);

        $response->assertRedirect();
        $application->refresh();
        $this->assertEquals(LeaseApplication::STATUS_CANCELLED, $application->status);
    }

    public function test_hold_transitions_in_progress_to_on_hold(): void
    {
        $application = LeaseApplication::factory()->inProgress()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post("/lease-applications/{$application->id}/hold", [
                'notes' => 'Waiting for additional documents.',
            ]);

        $response->assertRedirect();
        $application->refresh();
        $this->assertEquals(LeaseApplication::STATUS_ON_HOLD, $application->status);
    }

    public function test_resume_transitions_on_hold_to_in_progress(): void
    {
        $application = LeaseApplication::factory()->onHold()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post("/lease-applications/{$application->id}/resume", [
                'notes' => 'Documents received.',
            ]);

        $response->assertRedirect();
        $application->refresh();
        $this->assertEquals(LeaseApplication::STATUS_IN_PROGRESS, $application->status);
    }

    public function test_send_quote_updates_quote_timestamps(): void
    {
        $application = LeaseApplication::factory()->create([
            'tenant_id' => $this->tenant->id,
            'quote_sent_at' => null,
            'quote_expires_at' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->post("/lease-applications/{$application->id}/send-quote", [
                'expiration_days' => 14,
            ]);

        $response->assertRedirect();
        $application->refresh();
        $this->assertNotNull($application->quote_sent_at);
        $this->assertNotNull($application->quote_expires_at);
    }

    public function test_convert_to_lease_creates_lease_from_approved_application(): void
    {
        // Create required lease status
        Status::factory()->create([
            'id' => 30,
            'name' => 'New',
            'domain' => 'lease',
            'slug' => 'lease_new',
        ]);

        // Create unit rented status (ID 25) as expected by LeaseService
        Status::factory()->create([
            'id' => 25,
            'name' => 'Rented',
            'domain' => 'unit',
            'slug' => 'unit_rented_lease',
        ]);

        $application = LeaseApplication::factory()->approved()->create([
            'tenant_id' => $this->tenant->id,
            'applicant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'proposed_start_date' => now()->addDays(30),
            'proposed_end_date' => now()->addYear(),
            'quoted_rental_amount' => 50000,
            'security_deposit' => 10000,
        ]);

        $application->units()->attach($this->unit->id, [
            'proposed_rental_amount' => 50000,
        ]);

        $response = $this->actingAs($this->user)
            ->post("/lease-applications/{$application->id}/convert-to-lease");

        $response->assertRedirect();
        $application->refresh();
        $this->assertNotNull($application->converted_lease_id);
        $this->assertNotNull($application->converted_at);
    }

    public function test_convert_to_lease_fails_for_non_approved_application(): void
    {
        $application = LeaseApplication::factory()->create([
            'tenant_id' => $this->tenant->id,
            'status' => LeaseApplication::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($this->user)
            ->post("/lease-applications/{$application->id}/convert-to-lease");

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_history_returns_state_history(): void
    {
        $application = LeaseApplication::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get("/lease-applications/{$application->id}/history");

        $response->assertStatus(200);
        $response->assertJsonStructure(['history']);
    }

    public function test_statistics_are_calculated_correctly(): void
    {
        LeaseApplication::factory()->count(2)->create([
            'tenant_id' => $this->tenant->id,
            'status' => LeaseApplication::STATUS_DRAFT,
        ]);
        LeaseApplication::factory()->create([
            'tenant_id' => $this->tenant->id,
            'status' => LeaseApplication::STATUS_IN_PROGRESS,
        ]);
        LeaseApplication::factory()->approved()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/lease-applications');

        $response->assertInertia(fn (Assert $page) => $page
            ->where('statistics.total', 4)
            ->where('statistics.draft', 2)
            ->where('statistics.in_progress', 1)
            ->where('statistics.approved', 1)
        );
    }

    public function test_company_applicant_validates_company_name(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/lease-applications', [
                'applicant_name' => 'Test Company Rep',
                'applicant_email' => 'test@company.com',
                'applicant_type' => 'company',
                // company_name is missing
            ]);

        $response->assertSessionHasErrors(['company_name']);
    }

    public function test_invalid_transition_returns_error(): void
    {
        $application = LeaseApplication::factory()->approved()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post("/lease-applications/{$application->id}/hold");

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
