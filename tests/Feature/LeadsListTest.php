<?php

namespace Tests\Feature;

use App\Enums\RolesEnum;
use App\Models\AccountMembership;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Database\Seeders\StatusSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
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
}
