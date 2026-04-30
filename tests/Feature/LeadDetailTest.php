<?php

namespace Tests\Feature;

use App\Enums\RolesEnum;
use App\Models\AccountMembership;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadSource;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Database\Seeders\StatusSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class LeadDetailTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenant;

    private User $adminUser;

    private LeadSource $source;

    private Status $newStatus;

    private Status $contactedStatus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(RbacSeeder::class);
        $this->seed(StatusSeeder::class);

        $this->adminUser = User::factory()->create();
        $this->tenant = Tenant::create(['name' => 'Lead Detail Test Account']);

        AccountMembership::create([
            'user_id' => $this->adminUser->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);

        $this->adminUser->assignRole(RolesEnum::ACCOUNT_ADMINS->value);

        $this->source = LeadSource::factory()->create();
        $this->newStatus = Status::where('type', 'lead')->where('name_en', 'New')->firstOrFail();
        $this->contactedStatus = Status::where('type', 'lead')->where('name_en', 'Contacted')->firstOrFail();

        $this->tenant->makeCurrent();
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    // -------------------------------------------------------------------------
    // Scenario 1: Lead detail renders with correct data
    // -------------------------------------------------------------------------

    public function test_admin_can_view_lead_detail_page(): void
    {
        $lead = Lead::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'source_id' => $this->source->id,
            'status_id' => $this->newStatus->id,
            'name_en' => 'Ahmed Rashidi',
            'phone_number' => '555000001',
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('leads.show', $lead));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('leasing/leads/Show')
                ->has('lead')
                ->has('statuses')
                ->has('teamMembers')
                ->where('lead.id', $lead->id)
                ->where('lead.name_en', 'Ahmed Rashidi')
        );
    }

    // -------------------------------------------------------------------------
    // Scenario 2: Assign lead to a team member
    // -------------------------------------------------------------------------

    public function test_admin_can_assign_lead_to_team_member(): void
    {
        $lead = Lead::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'source_id' => $this->source->id,
            'status_id' => $this->newStatus->id,
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
            ->post(route('leads.assign', $lead), [
                'user_id' => $assignee->id,
            ]);

        $response->assertRedirect(route('leads.show', $lead));

        $this->assertDatabaseHas('rf_leads', [
            'id' => $lead->id,
            'assigned_to_user_id' => $assignee->id,
        ]);

        $this->assertDatabaseHas('lead_activities', [
            'lead_id' => $lead->id,
            'user_id' => $this->adminUser->id,
            'type' => LeadActivity::TYPE_ASSIGNED,
        ]);
    }

    // -------------------------------------------------------------------------
    // Scenario 3: Update lead status persists and logs activity
    // -------------------------------------------------------------------------

    public function test_admin_can_update_lead_status(): void
    {
        $lead = Lead::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'source_id' => $this->source->id,
            'status_id' => $this->newStatus->id,
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put(route('leads.update', $lead), [
                'status_id' => $this->contactedStatus->id,
                'lost_reason' => null,
            ]);

        $response->assertRedirect(route('leads.show', $lead));

        $this->assertDatabaseHas('rf_leads', [
            'id' => $lead->id,
            'status_id' => $this->contactedStatus->id,
        ]);

        $this->assertDatabaseHas('lead_activities', [
            'lead_id' => $lead->id,
            'type' => LeadActivity::TYPE_STATUS_CHANGE,
        ]);
    }

    // -------------------------------------------------------------------------
    // Scenario 4: Add a note to a lead
    // -------------------------------------------------------------------------

    public function test_admin_can_add_note_to_lead(): void
    {
        $lead = Lead::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'source_id' => $this->source->id,
            'status_id' => $this->newStatus->id,
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.notes.store', $lead), [
                'note' => 'This client is very interested in 2BR units.',
            ]);

        $response->assertRedirect(route('leads.show', $lead));

        $activity = LeadActivity::where('lead_id', $lead->id)
            ->where('type', LeadActivity::TYPE_NOTE)
            ->first();

        $this->assertNotNull($activity);
        $this->assertEquals('This client is very interested in 2BR units.', $activity->data['note']);
    }

    // -------------------------------------------------------------------------
    // Scenario 5: Delete lead redirects to index
    // -------------------------------------------------------------------------

    public function test_admin_can_delete_lead(): void
    {
        $lead = Lead::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'source_id' => $this->source->id,
            'status_id' => $this->newStatus->id,
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->delete(route('leads.destroy', $lead));

        $response->assertRedirect(route('leads.index'));

        $this->assertModelMissing($lead);
    }

    // -------------------------------------------------------------------------
    // Scenario 6: Unassign removes the assignee and logs activity
    // -------------------------------------------------------------------------

    public function test_admin_can_unassign_lead(): void
    {
        $assignee = User::factory()->create();
        AccountMembership::create([
            'user_id' => $assignee->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);

        $lead = Lead::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'source_id' => $this->source->id,
            'status_id' => $this->newStatus->id,
            'assigned_to_user_id' => $assignee->id,
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.unassign', $lead));

        $response->assertRedirect(route('leads.show', $lead));

        $this->assertDatabaseHas('rf_leads', [
            'id' => $lead->id,
            'assigned_to_user_id' => null,
        ]);

        $this->assertDatabaseHas('lead_activities', [
            'lead_id' => $lead->id,
            'type' => LeadActivity::TYPE_UNASSIGNED,
        ]);
    }

    // -------------------------------------------------------------------------
    // Authorization: user without leads.VIEW gets 403
    // -------------------------------------------------------------------------

    public function test_user_without_permission_cannot_view_lead(): void
    {
        $lead = Lead::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'source_id' => $this->source->id,
            'status_id' => $this->newStatus->id,
        ]);

        $noPermUser = User::factory()->create();
        $noPermTenant = Tenant::create(['name' => 'No Perm Tenant']);
        AccountMembership::create([
            'user_id' => $noPermUser->id,
            'account_tenant_id' => $noPermTenant->id,
            'role' => 'account_admins',
        ]);

        $response = $this
            ->actingAs($noPermUser)
            ->withSession(['tenant_id' => $noPermTenant->id])
            ->get(route('leads.show', $lead));

        $response->assertStatus(403);
    }

    // -------------------------------------------------------------------------
    // Cross-tenant: cannot assign user from another tenant
    // -------------------------------------------------------------------------

    public function test_cannot_assign_user_from_another_tenant(): void
    {
        $lead = Lead::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'source_id' => $this->source->id,
            'status_id' => $this->newStatus->id,
        ]);

        $otherTenant = Tenant::create(['name' => 'Other Tenant']);
        $outsiderUser = User::factory()->create();
        AccountMembership::create([
            'user_id' => $outsiderUser->id,
            'account_tenant_id' => $otherTenant->id,
            'role' => 'account_admins',
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.assign', $lead), [
                'user_id' => $outsiderUser->id,
            ]);

        $response->assertSessionHasErrors('user_id');

        $this->assertDatabaseHas('rf_leads', [
            'id' => $lead->id,
            'assigned_to_user_id' => null,
        ]);
    }

    // -------------------------------------------------------------------------
    // Validation: empty note is rejected
    // -------------------------------------------------------------------------

    public function test_empty_note_is_rejected(): void
    {
        $lead = Lead::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'source_id' => $this->source->id,
            'status_id' => $this->newStatus->id,
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.notes.store', $lead), [
                'note' => '',
            ]);

        $response->assertSessionHasErrors('note');

        $this->assertDatabaseMissing('lead_activities', [
            'lead_id' => $lead->id,
            'type' => LeadActivity::TYPE_NOTE,
        ]);
    }
}
