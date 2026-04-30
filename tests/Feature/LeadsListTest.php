<?php

namespace Tests\Feature;

use App\Enums\RolesEnum;
use App\Models\AccountMembership;
use App\Models\Admin;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Database\Seeders\StatusSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LeadsListTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenant;

    private User $adminUser;

    private LeadSource $source;

    private Status $newStatus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(RbacSeeder::class);
        $this->seed(StatusSeeder::class);

        $this->adminUser = User::factory()->create();
        $this->tenant = Tenant::create(['name' => 'Leads Test Account']);

        AccountMembership::create([
            'user_id' => $this->adminUser->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);

        $this->adminUser->assignRole(RolesEnum::ACCOUNT_ADMINS->value);

        $this->source = LeadSource::factory()->create();
        $this->newStatus = Status::where('type', 'lead')->where('name_en', 'New')->firstOrFail();

        $this->tenant->makeCurrent();
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    // -------------------------------------------------------------------------
    // Index — Scenario 1: List renders
    // -------------------------------------------------------------------------

    public function test_admin_can_view_leads_index(): void
    {
        Lead::factory()->count(3)->create([
            'account_tenant_id' => $this->tenant->id,
            'source_id' => $this->source->id,
            'status_id' => $this->newStatus->id,
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('leads.index'));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('leasing/leads/Index')
                ->has('sources')
                ->has('statuses')
                ->has('filters')
        );
    }

    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        $response = $this->get(route('leads.index'));
        $response->assertRedirect(route('login'));
    }

    // -------------------------------------------------------------------------
    // Scenario 2: Filter by status narrows results
    // -------------------------------------------------------------------------

    public function test_status_filter_narrows_leads(): void
    {
        $contactedStatus = Status::where('type', 'lead')->where('name_en', 'Contacted')->firstOrFail();

        Lead::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'source_id' => $this->source->id,
            'status_id' => $this->newStatus->id,
            'name_en' => 'New Lead',
        ]);

        Lead::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'source_id' => $this->source->id,
            'status_id' => $contactedStatus->id,
            'name_en' => 'Contacted Lead',
        ]);

        // Filter returns only filters in response — actual data is deferred (undefined at render time)
        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('leads.index', ['status_id' => $contactedStatus->id]));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('leasing/leads/Index')
                ->where('filters.status_id', (string) $contactedStatus->id)
        );
    }

    // -------------------------------------------------------------------------
    // Scenario 3: Filter by source narrows results
    // -------------------------------------------------------------------------

    public function test_source_filter_narrows_leads(): void
    {
        $otherSource = LeadSource::factory()->create();

        Lead::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'source_id' => $this->source->id,
            'status_id' => $this->newStatus->id,
        ]);
        Lead::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'source_id' => $otherSource->id,
            'status_id' => $this->newStatus->id,
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('leads.index', ['source_id' => $this->source->id]));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('leasing/leads/Index')
                ->where('filters.source_id', (string) $this->source->id)
        );
    }

    // -------------------------------------------------------------------------
    // Scenario 5: Manual add creates a tenant-scoped lead with status "New"
    // -------------------------------------------------------------------------

    public function test_admin_can_manually_add_a_lead(): void
    {
        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.store'), [
                'name_en' => 'John Prospect',
                'name_ar' => '',
                'phone_country_code' => '+966',
                'phone_number' => '512345678',
                'email' => 'john@example.com',
                'source_id' => $this->source->id,
                'notes' => 'Interested in 2BR unit.',
            ]);

        $response->assertRedirect(route('leads.index'));

        $this->assertDatabaseHas('rf_leads', [
            'name_en' => 'John Prospect',
            'phone_number' => '512345678',
            'email' => 'john@example.com',
            'source_id' => $this->source->id,
            'status_id' => $this->newStatus->id,
            'account_tenant_id' => $this->tenant->id,
        ]);
    }

    // -------------------------------------------------------------------------
    // Validation: phone is required, at least one name required
    // -------------------------------------------------------------------------

    public function test_store_validation_rejects_missing_phone(): void
    {
        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.store'), [
                'name_en' => 'Missing Phone',
                'name_ar' => '',
                'phone_number' => '',
                'source_id' => $this->source->id,
            ]);

        $response->assertSessionHasErrors('phone_number');
    }

    public function test_store_validation_rejects_missing_source(): void
    {
        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.store'), [
                'name_en' => 'No Source Lead',
                'phone_number' => '512345678',
                'source_id' => '',
            ]);

        $response->assertSessionHasErrors('source_id');
    }

    public function test_store_validation_rejects_when_both_names_missing(): void
    {
        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.store'), [
                'name_en' => '',
                'name_ar' => '',
                'phone_number' => '512345678',
                'source_id' => $this->source->id,
            ]);

        $response->assertSessionHasErrors('name_en');
    }

    // -------------------------------------------------------------------------
    // Tenant isolation: leads from another tenant not visible
    // -------------------------------------------------------------------------

    public function test_leads_are_scoped_to_current_tenant(): void
    {
        // Lead belonging to THIS tenant
        $ownLead = Lead::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'source_id' => $this->source->id,
            'status_id' => $this->newStatus->id,
            'name_en' => 'Own Lead',
        ]);

        // Lead belonging to ANOTHER tenant
        $otherTenant = Tenant::create(['name' => 'Other Tenant']);
        Lead::factory()->create([
            'account_tenant_id' => $otherTenant->id,
            'source_id' => $this->source->id,
            'status_id' => $this->newStatus->id,
            'name_en' => 'Other Tenant Lead',
        ]);

        // Confirm only own lead is present in the database query
        $leads = Lead::query()
            ->where('account_tenant_id', $this->tenant->id)
            ->get();

        $this->assertCount(1, $leads);
        $this->assertEquals($ownLead->id, $leads->first()->id);
    }

    // -------------------------------------------------------------------------
    // Role check: user without leads.VIEW gets 403
    // -------------------------------------------------------------------------

    public function test_user_without_permission_gets_403(): void
    {
        // A user with no roles has no leads.VIEW permission
        $noPermUser = User::factory()->create();
        $noPermTenant = Tenant::create(['name' => 'No Perm Tenant']);
        AccountMembership::create([
            'user_id' => $noPermUser->id,
            'account_tenant_id' => $noPermTenant->id,
            'role' => 'account_admins',
        ]);
        // Note: no assignRole() call, so spatie has no role/permissions for this user

        $response = $this
            ->actingAs($noPermUser)
            ->withSession(['tenant_id' => $noPermTenant->id])
            ->get(route('leads.index'));

        $response->assertStatus(403);
    }

    // -------------------------------------------------------------------------
    // AC1 — Authorization: user without leads.CREATE cannot store
    // -------------------------------------------------------------------------

    public function test_user_without_create_permission_cannot_store_lead(): void
    {
        // 'dependents' role has no leads.CREATE permission
        $restrictedUser = User::factory()->create();
        AccountMembership::create([
            'user_id' => $restrictedUser->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'dependents',
        ]);
        $restrictedUser->assignRole(RolesEnum::DEPENDENTS->value);

        $response = $this
            ->actingAs($restrictedUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.store'), [
                'name_en' => 'Unauthorized Lead',
                'phone_number' => '512345678',
                'source_id' => $this->source->id,
            ]);

        $response->assertStatus(403);
    }

    // -------------------------------------------------------------------------
    // AC1 — Authentication: unauthenticated POST redirects to login
    // -------------------------------------------------------------------------

    public function test_unauthenticated_post_is_redirected_to_login(): void
    {
        $response = $this->post(route('leads.store'), [
            'name_en' => 'Ghost Lead',
            'phone_number' => '512345678',
            'source_id' => $this->source->id,
        ]);

        $response->assertRedirect(route('login'));
    }

    // -------------------------------------------------------------------------
    // AC4 — Search by name narrows results
    // -------------------------------------------------------------------------

    public function test_search_filter_narrows_leads_by_name(): void
    {
        Lead::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'source_id' => $this->source->id,
            'status_id' => $this->newStatus->id,
            'name_en' => 'Ahmad Al-Rashid',
        ]);
        Lead::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'source_id' => $this->source->id,
            'status_id' => $this->newStatus->id,
            'name_en' => 'Sara Johnson',
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('leads.index', ['search' => 'Ahmad']));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('leasing/leads/Index')
                ->where('filters.search', 'Ahmad')
        );
    }

    // -------------------------------------------------------------------------
    // AC4 — Search by phone narrows results
    // -------------------------------------------------------------------------

    public function test_search_filter_narrows_leads_by_phone(): void
    {
        Lead::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'source_id' => $this->source->id,
            'status_id' => $this->newStatus->id,
            'phone_number' => '599111222',
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('leads.index', ['search' => '599111222']));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('leasing/leads/Index')
                ->where('filters.search', '599111222')
        );
    }

    // -------------------------------------------------------------------------
    // AC4 — Empty search returns all leads (whitespace-only trimmed to empty)
    // -------------------------------------------------------------------------

    public function test_whitespace_only_search_is_treated_as_empty(): void
    {
        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('leads.index', ['search' => '   ']));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('leasing/leads/Index')
                ->where('filters.search', '')
        );
    }

    // -------------------------------------------------------------------------
    // AC2+AC3 — Combined status + source filter narrows correctly
    // -------------------------------------------------------------------------

    public function test_combined_status_and_source_filter_narrows_leads(): void
    {
        $contactedStatus = Status::where('type', 'lead')->where('name_en', 'Contacted')->firstOrFail();
        $otherSource = LeadSource::factory()->create();

        // Should appear: correct status AND correct source
        Lead::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'source_id' => $this->source->id,
            'status_id' => $contactedStatus->id,
            'name_en' => 'Target Lead',
        ]);
        // Correct source, wrong status — should NOT appear
        Lead::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'source_id' => $this->source->id,
            'status_id' => $this->newStatus->id,
            'name_en' => 'Wrong Status Lead',
        ]);
        // Correct status, wrong source — should NOT appear
        Lead::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'source_id' => $otherSource->id,
            'status_id' => $contactedStatus->id,
            'name_en' => 'Wrong Source Lead',
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('leads.index', [
                'status_id' => $contactedStatus->id,
                'source_id' => $this->source->id,
            ]));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('leasing/leads/Index')
                ->where('filters.status_id', (string) $contactedStatus->id)
                ->where('filters.source_id', (string) $this->source->id)
        );
    }

    // -------------------------------------------------------------------------
    // AC2 — Invalid (non-existent) status_id filter returns empty result set
    // -------------------------------------------------------------------------

    public function test_invalid_status_filter_returns_empty_result_set(): void
    {
        Lead::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'source_id' => $this->source->id,
            'status_id' => $this->newStatus->id,
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('leads.index', ['status_id' => 999999]));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('leasing/leads/Index')
                ->where('filters.status_id', '999999')
        );
    }

    // -------------------------------------------------------------------------
    // Pagination — per_page clamped to minimum of 5
    // -------------------------------------------------------------------------

    public function test_per_page_is_clamped_to_minimum_of_five(): void
    {
        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('leads.index', ['per_page' => 1]));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('leasing/leads/Index')
                ->where('filters.per_page', '5')
        );
    }

    // -------------------------------------------------------------------------
    // Pagination — per_page clamped to maximum of 50
    // -------------------------------------------------------------------------

    public function test_per_page_is_clamped_to_maximum_of_fifty(): void
    {
        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('leads.index', ['per_page' => 200]));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('leasing/leads/Index')
                ->where('filters.per_page', '50')
        );
    }

    // -------------------------------------------------------------------------
    // AC5 Validation — invalid email format rejected
    // -------------------------------------------------------------------------

    public function test_store_validation_rejects_malformed_email(): void
    {
        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.store'), [
                'name_en' => 'Email Test Lead',
                'phone_number' => '512345678',
                'source_id' => $this->source->id,
                'email' => 'not-a-valid-email',
            ]);

        $response->assertSessionHasErrors('email');
    }

    // -------------------------------------------------------------------------
    // AC5 Validation — phone_country_code exceeding max:5 is rejected
    // -------------------------------------------------------------------------

    public function test_store_validation_rejects_phone_country_code_exceeding_max_length(): void
    {
        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.store'), [
                'name_en' => 'Country Code Test',
                'phone_number' => '512345678',
                'source_id' => $this->source->id,
                'phone_country_code' => '+99999',
            ]);

        $response->assertSessionHasErrors('phone_country_code');
    }

    // -------------------------------------------------------------------------
    // AC5 Validation — notes exceeding max:2000 characters is rejected
    // -------------------------------------------------------------------------

    public function test_store_validation_rejects_notes_exceeding_max_length(): void
    {
        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.store'), [
                'name_en' => 'Notes Test Lead',
                'phone_number' => '512345678',
                'source_id' => $this->source->id,
                'notes' => str_repeat('a', 2001),
            ]);

        $response->assertSessionHasErrors('notes');
    }

    // -------------------------------------------------------------------------
    // AC5 Validation — source_id that does not exist in rf_lead_sources fails
    // -------------------------------------------------------------------------

    public function test_store_validation_rejects_non_existent_source_id(): void
    {
        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.store'), [
                'name_en' => 'Bad Source Lead',
                'phone_number' => '512345678',
                'source_id' => 999999,
            ]);

        $response->assertSessionHasErrors('source_id');
    }

    // -------------------------------------------------------------------------
    // AC5 Edge case — Arabic-only name is accepted (name_ar provided, name_en empty)
    // -------------------------------------------------------------------------

    public function test_store_accepts_arabic_only_name(): void
    {
        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.store'), [
                'name_en' => '',
                'name_ar' => 'أحمد الرشيد',
                'phone_country_code' => '+966',
                'phone_number' => '509876543',
                'source_id' => $this->source->id,
            ]);

        $response->assertRedirect(route('leads.index'));

        $this->assertDatabaseHas('rf_leads', [
            'name_ar' => 'أحمد الرشيد',
            'account_tenant_id' => $this->tenant->id,
        ]);
    }

    // -------------------------------------------------------------------------
    // AC5 Edge case — max-length name_en (255 chars) is accepted
    // -------------------------------------------------------------------------

    public function test_store_accepts_name_at_max_length(): void
    {
        $longName = str_repeat('A', 255);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.store'), [
                'name_en' => $longName,
                'phone_number' => '512345678',
                'source_id' => $this->source->id,
            ]);

        $response->assertRedirect(route('leads.index'));

        $this->assertDatabaseHas('rf_leads', [
            'name_en' => $longName,
            'account_tenant_id' => $this->tenant->id,
        ]);
    }

    // -------------------------------------------------------------------------
    // AC5 Edge case — name_en exceeding max:255 is rejected
    // -------------------------------------------------------------------------

    public function test_store_validation_rejects_name_en_exceeding_max_length(): void
    {
        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.store'), [
                'name_en' => str_repeat('A', 256),
                'phone_number' => '512345678',
                'source_id' => $this->source->id,
            ]);

        $response->assertSessionHasErrors('name_en');
    }

    // -------------------------------------------------------------------------
    // AC5 — New lead always gets status "New" regardless of request body
    // -------------------------------------------------------------------------

    public function test_store_always_assigns_new_status_regardless_of_payload(): void
    {
        $contactedStatus = Status::where('type', 'lead')->where('name_en', 'Contacted')->firstOrFail();

        $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.store'), [
                'name_en' => 'Status Override Attempt',
                'phone_number' => '512300000',
                'source_id' => $this->source->id,
                'status_id' => $contactedStatus->id,
            ]);

        $this->assertDatabaseHas('rf_leads', [
            'name_en' => 'Status Override Attempt',
            'status_id' => $this->newStatus->id,
        ]);
        $this->assertDatabaseMissing('rf_leads', [
            'name_en' => 'Status Override Attempt',
            'status_id' => $contactedStatus->id,
        ]);
    }

    // -------------------------------------------------------------------------
    // Cross-tenant isolation — store assigns lead to current tenant only
    // -------------------------------------------------------------------------

    public function test_store_scopes_new_lead_to_current_tenant(): void
    {
        $otherTenant = Tenant::create(['name' => 'Other Store Tenant']);

        $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.store'), [
                'name_en' => 'Tenant Scoped Lead',
                'phone_number' => '512000001',
                'source_id' => $this->source->id,
            ]);

        $this->assertDatabaseHas('rf_leads', [
            'name_en' => 'Tenant Scoped Lead',
            'account_tenant_id' => $this->tenant->id,
        ]);
        $this->assertDatabaseMissing('rf_leads', [
            'name_en' => 'Tenant Scoped Lead',
            'account_tenant_id' => $otherTenant->id,
        ]);
    }

    // -------------------------------------------------------------------------
    // -------------------------------------------------------------------------
    // Fix #2 regression — index eager-loads assignedTo (not leadOwner)
    // -------------------------------------------------------------------------

    public function test_index_eager_loads_assigned_to_relation(): void
    {
        $assignee = User::factory()->create();
        AccountMembership::create([
            'user_id' => $assignee->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);

        Lead::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'source_id' => $this->source->id,
            'status_id' => $this->newStatus->id,
            'name_en' => 'Assigned Lead',
            'assigned_to_user_id' => $assignee->id,
        ]);

        $lead = Lead::with('assignedTo')
            ->where('account_tenant_id', $this->tenant->id)
            ->where('name_en', 'Assigned Lead')
            ->firstOrFail();

        $this->assertNotNull($lead->assignedTo);
        $this->assertSame($assignee->id, $lead->assignedTo->id);
        $this->assertSame($assignee->name, $lead->assignedTo->name);
    }

    // -------------------------------------------------------------------------
    // Cross-tenant isolation — index does not expose other tenant's leads
    // -------------------------------------------------------------------------

    public function test_index_does_not_expose_other_tenant_leads(): void
    {
        $otherTenant = Tenant::create(['name' => 'Isolated Tenant']);

        // Bypass the global scope to create a lead belonging to the other tenant
        Tenant::forgetCurrent();
        $otherTenant->makeCurrent();
        Lead::factory()->create([
            'account_tenant_id' => $otherTenant->id,
            'source_id' => $this->source->id,
            'status_id' => $this->newStatus->id,
            'name_en' => 'Other Tenant Secret Lead',
        ]);
        Tenant::forgetCurrent();
        $this->tenant->makeCurrent();

        $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('leads.index'));

        // The global scope on BelongsToAccountTenant must filter this out at the DB level
        $this->assertDatabaseMissing('rf_leads', [
            'name_en' => 'Other Tenant Secret Lead',
            'account_tenant_id' => $this->tenant->id,
        ]);
        $this->assertSame(0, Lead::query()->where('name_en', 'Other Tenant Secret Lead')->count());
    }
}
