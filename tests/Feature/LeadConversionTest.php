<?php

namespace Tests\Feature;

use App\Enums\RolesEnum;
use App\Models\AccountMembership;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadSource;
use App\Models\Owner;
use App\Models\Resident;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Database\Seeders\StatusSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class LeadConversionTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenant;

    private User $adminUser;

    private LeadSource $source;

    private Status $qualifiedStatus;

    private Status $convertedStatus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(RbacSeeder::class);
        $this->seed(StatusSeeder::class);

        $this->adminUser = User::factory()->create();
        $this->tenant = Tenant::create(['name' => 'Lead Conversion Test Account']);

        AccountMembership::create([
            'user_id' => $this->adminUser->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);

        $this->adminUser->assignRole(RolesEnum::ACCOUNT_ADMINS->value);

        $this->source = LeadSource::factory()->create();
        $this->qualifiedStatus = Status::where('type', 'lead')->where('name_en', 'Qualified')->firstOrFail();
        $this->convertedStatus = Status::where('type', 'lead')->where('name_en', 'Converted')->firstOrFail();

        $this->tenant->makeCurrent();
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function qualifiedLead(array $overrides = []): Lead
    {
        return Lead::factory()->create(array_merge([
            'account_tenant_id' => $this->tenant->id,
            'source_id' => $this->source->id,
            'status_id' => $this->qualifiedStatus->id,
            'name_en' => 'Ahmed Al-Rashidi',
            'phone_number' => '555000099',
            'email' => 'ahmed.rashidi.unique@example.com',
        ], $overrides));
    }

    /** @return TestResponse */
    private function asAdmin()
    {
        return $this->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id]);
    }

    // -------------------------------------------------------------------------
    // Scenario 1: Convert Qualified lead to Owner — creates new contact
    // -------------------------------------------------------------------------

    public function test_convert_qualified_lead_to_owner_creates_contact(): void
    {
        $lead = $this->qualifiedLead(['email' => 'owner-new@example.com', 'phone_number' => '500000001']);

        $response = $this->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->postJson(route('leads.convert', $lead), [
                'contact_type' => 'owner',
                'link_to_existing' => false,
            ]);

        $response->assertOk();
        $response->assertJsonFragment(['contact_type' => 'owner']);

        // Owner record created
        $this->assertDatabaseHas('rf_owners', [
            'email' => 'owner-new@example.com',
            'account_tenant_id' => $this->tenant->id,
        ]);

        // Lead status flipped to Converted
        $lead->refresh();
        $this->assertEquals($this->convertedStatus->id, $lead->status_id);
        $this->assertNotNull($lead->converted_at);
        $this->assertNotNull($lead->converted_contact_id);
        $this->assertEquals(Owner::class, $lead->converted_contact_type);
    }

    // -------------------------------------------------------------------------
    // Scenario 2: Convert Qualified lead to Resident — creates new contact
    // -------------------------------------------------------------------------

    public function test_convert_qualified_lead_to_resident_creates_contact(): void
    {
        $lead = $this->qualifiedLead(['email' => 'resident-new@example.com', 'phone_number' => '500000002']);

        $response = $this->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->postJson(route('leads.convert', $lead), [
                'contact_type' => 'resident',
                'link_to_existing' => false,
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('rf_tenants', [
            'email' => 'resident-new@example.com',
            'account_tenant_id' => $this->tenant->id,
        ]);

        $lead->refresh();
        $this->assertEquals($this->convertedStatus->id, $lead->status_id);
        $this->assertEquals(Resident::class, $lead->converted_contact_type);
    }

    // -------------------------------------------------------------------------
    // Scenario 3: Dedup — check-duplicate returns match
    // -------------------------------------------------------------------------

    public function test_check_duplicate_returns_match_when_email_matches_owner(): void
    {
        $sharedEmail = 'shared-dedup-owner@example.com';

        Owner::factory()->create([
            'email' => $sharedEmail,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $lead = $this->qualifiedLead(['email' => $sharedEmail, 'phone_number' => '500000003']);

        $response = $this->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->getJson(route('leads.check-duplicate', $lead));

        $response->assertOk();
        $response->assertJsonFragment(['duplicate' => true]);
        $response->assertJsonFragment(['type' => 'owner']);
    }

    public function test_check_duplicate_returns_match_when_phone_matches_resident(): void
    {
        $sharedPhone = '500000004';

        Resident::factory()->create([
            'phone_number' => $sharedPhone,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $lead = $this->qualifiedLead(['phone_number' => $sharedPhone, 'email' => 'unique-dedup-r@example.com']);

        $response = $this->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->getJson(route('leads.check-duplicate', $lead));

        $response->assertOk();
        $response->assertJsonFragment(['duplicate' => true]);
        $response->assertJsonFragment(['type' => 'resident']);
    }

    public function test_check_duplicate_returns_no_match_when_no_contact_found(): void
    {
        $lead = $this->qualifiedLead(['email' => 'totally-unique-xyz@example.com', 'phone_number' => '500000005']);

        $response = $this->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->getJson(route('leads.check-duplicate', $lead));

        $response->assertOk();
        $response->assertJsonFragment(['duplicate' => false]);
    }

    // -------------------------------------------------------------------------
    // Scenario 4: Link to existing Owner (no duplicate created)
    // -------------------------------------------------------------------------

    public function test_convert_links_to_existing_owner_when_requested(): void
    {
        $existingOwner = Owner::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'email' => 'existing-owner@example.com',
        ]);

        $lead = $this->qualifiedLead(['email' => 'existing-owner@example.com', 'phone_number' => '500000006']);

        $ownerCountBefore = Owner::withoutGlobalScopes()
            ->where('account_tenant_id', $this->tenant->id)
            ->count();

        $response = $this->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->postJson(route('leads.convert', $lead), [
                'contact_type' => 'owner',
                'link_to_existing' => true,
                'existing_contact_id' => $existingOwner->id,
            ]);

        $response->assertOk();

        // No new owner created
        $ownerCountAfter = Owner::withoutGlobalScopes()
            ->where('account_tenant_id', $this->tenant->id)
            ->count();
        $this->assertEquals($ownerCountBefore, $ownerCountAfter);

        // Lead linked to existing owner
        $lead->refresh();
        $this->assertEquals($existingOwner->id, $lead->converted_contact_id);
        $this->assertEquals(Owner::class, $lead->converted_contact_type);
        $this->assertEquals($this->convertedStatus->id, $lead->status_id);
    }

    // -------------------------------------------------------------------------
    // Scenario 5: Link to existing Resident (no duplicate created)
    // -------------------------------------------------------------------------

    public function test_convert_links_to_existing_resident_when_requested(): void
    {
        $existingResident = Resident::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'email' => 'existing-resident@example.com',
        ]);

        $lead = $this->qualifiedLead(['email' => 'existing-resident@example.com', 'phone_number' => '500000007']);

        $response = $this->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->postJson(route('leads.convert', $lead), [
                'contact_type' => 'resident',
                'link_to_existing' => true,
                'existing_contact_id' => $existingResident->id,
            ]);

        $response->assertOk();

        $lead->refresh();
        $this->assertEquals($existingResident->id, $lead->converted_contact_id);
        $this->assertEquals(Resident::class, $lead->converted_contact_type);
    }

    // -------------------------------------------------------------------------
    // Scenario 6: Activity log records the conversion
    // -------------------------------------------------------------------------

    public function test_conversion_records_activity_log_entry(): void
    {
        $lead = $this->qualifiedLead(['email' => 'activity-log@example.com', 'phone_number' => '500000008']);

        $this->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->postJson(route('leads.convert', $lead), [
                'contact_type' => 'owner',
                'link_to_existing' => false,
            ])
            ->assertOk();

        $this->assertDatabaseHas('lead_activities', [
            'lead_id' => $lead->id,
            'type' => LeadActivity::TYPE_CONVERTED,
        ]);
    }

    // -------------------------------------------------------------------------
    // Scenario 7: Already-converted lead cannot be re-converted
    // -------------------------------------------------------------------------

    public function test_already_converted_lead_returns_422(): void
    {
        $existingOwner = Owner::factory()->create([
            'account_tenant_id' => $this->tenant->id,
        ]);

        $lead = $this->qualifiedLead(['email' => 'already-converted@example.com', 'phone_number' => '500000009']);

        // Mark as already converted
        $lead->update([
            'status_id' => $this->convertedStatus->id,
            'converted_contact_id' => $existingOwner->id,
            'converted_contact_type' => Owner::class,
            'converted_at' => now(),
        ]);

        // Attempt second conversion
        $response = $this->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->postJson(route('leads.convert', $lead), [
                'contact_type' => 'owner',
                'link_to_existing' => false,
            ]);

        $response->assertStatus(422);
    }

    // -------------------------------------------------------------------------
    // Scenario 8: Cross-tenant rejection
    // -------------------------------------------------------------------------

    public function test_cross_tenant_cannot_convert_another_tenants_lead(): void
    {
        $otherTenant = Tenant::create(['name' => 'Other Tenant Cross']);
        $otherUser = User::factory()->create();

        AccountMembership::create([
            'user_id' => $otherUser->id,
            'account_tenant_id' => $otherTenant->id,
            'role' => 'account_admins',
        ]);
        $otherUser->assignRole(RolesEnum::ACCOUNT_ADMINS->value);

        // Lead belongs to $this->tenant, not otherTenant
        $lead = $this->qualifiedLead(['email' => 'cross-tenant@example.com', 'phone_number' => '500000010']);

        // Temporarily switch to otherTenant so the request runs under the correct tenant.
        // ResolveTenant middleware bails early if Tenant::current() is already set,
        // so we must manually swap tenants before the request rather than rely on middleware.
        $otherTenant->makeCurrent();

        // BelongsToAccountTenant global scope filters the Lead query with otherTenant's ID.
        // The lead belongs to $this->tenant, so route model binding will NOT find it → 404.
        $this->actingAs($otherUser)
            ->withSession(['tenant_id' => $otherTenant->id])
            ->postJson(route('leads.convert', $lead), [
                'contact_type' => 'owner',
                'link_to_existing' => false,
            ])
            ->assertNotFound();

        // Restore this tenant context for the assertion
        $this->tenant->makeCurrent();

        // Regardless of HTTP status, the lead must NOT have been converted
        $lead->refresh();
        $this->assertNull($lead->converted_contact_id, 'Lead must not be converted by a cross-tenant user');
    }

    // -------------------------------------------------------------------------
    // Scenario 9: Unauthenticated access rejected
    // -------------------------------------------------------------------------

    public function test_unauthenticated_user_cannot_convert_lead(): void
    {
        $lead = $this->qualifiedLead(['email' => 'unauth@example.com', 'phone_number' => '500000011']);

        $response = $this->postJson(route('leads.convert', $lead), [
            'contact_type' => 'owner',
        ]);

        $response->assertUnauthorized();
    }

    // -------------------------------------------------------------------------
    // Scenario 10: Convert with invalid contact_type returns validation error
    // -------------------------------------------------------------------------

    public function test_invalid_contact_type_returns_validation_error(): void
    {
        $lead = $this->qualifiedLead(['email' => 'invalid-type@example.com', 'phone_number' => '500000012']);

        $response = $this->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->postJson(route('leads.convert', $lead), [
                'contact_type' => 'landlord',
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['contact_type']);
    }

    // -------------------------------------------------------------------------
    // Scenario 11: Show page exposes canConvert=true for Qualified lead
    // -------------------------------------------------------------------------

    public function test_show_page_exposes_can_convert_for_qualified_lead(): void
    {
        $lead = $this->qualifiedLead(['email' => 'show-qualified@example.com', 'phone_number' => '500000013']);

        $response = $this->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('leads.show', $lead));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('leasing/leads/Show')
            ->where('canConvert', true)
        );
    }

    // -------------------------------------------------------------------------
    // Scenario 12: Show page exposes canConvert=false for converted lead
    // -------------------------------------------------------------------------

    public function test_show_page_exposes_can_convert_false_for_converted_lead(): void
    {
        $existingOwner = Owner::factory()->create([
            'account_tenant_id' => $this->tenant->id,
        ]);

        $lead = $this->qualifiedLead(['email' => 'show-converted@example.com', 'phone_number' => '500000014']);
        $lead->update([
            'status_id' => $this->convertedStatus->id,
            'converted_contact_id' => $existingOwner->id,
            'converted_contact_type' => Owner::class,
            'converted_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('leads.show', $lead));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('leasing/leads/Show')
            ->where('canConvert', false)
            ->where('lead.is_converted', true)
        );
    }

    // -------------------------------------------------------------------------
    // Scenario 13: Check-duplicate is cross-tenant isolated (no match across tenants)
    // -------------------------------------------------------------------------

    public function test_check_duplicate_does_not_match_contacts_in_other_tenant(): void
    {
        $otherTenant = Tenant::create(['name' => 'Other Tenant Dedup']);
        $sharedEmail = 'cross-dedup@example.com';

        Owner::factory()->create([
            'email' => $sharedEmail,
            'account_tenant_id' => $otherTenant->id,
        ]);

        $lead = $this->qualifiedLead(['email' => $sharedEmail, 'phone_number' => '500000015']);

        $response = $this->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->getJson(route('leads.check-duplicate', $lead));

        $response->assertOk();
        $response->assertJsonFragment(['duplicate' => false]);
    }
}
