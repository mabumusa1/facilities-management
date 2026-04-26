<?php

namespace Tests\Feature\Leasing;

use App\Console\Commands\ExpireLeaseQuotes;
use App\Models\AccountMembership;
use App\Models\Admin;
use App\Models\LeaseQuote;
use App\Models\Resident;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class QuoteControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesSeeder::class);

        $this->user = User::factory()->create();
        $this->tenant = Tenant::create(['name' => 'Leasing Test Account']);

        AccountMembership::create([
            'user_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($this->user);
    }

    private function withTenant(): array
    {
        return ['tenant_id' => $this->tenant->id];
    }

    // -----------------------------------------------------------------------
    // Happy-path: store a draft quote
    // -----------------------------------------------------------------------

    public function test_store_creates_draft_lease_quote_and_redirects_to_show(): void
    {
        $unit = Unit::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $contact = Resident::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $paymentFrequency = Setting::factory()->create([
            'type' => 'payment_frequency',
            'name_en' => 'Monthly',
        ]);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('quotes.store'), [
                'unit_id' => $unit->id,
                'contact_id' => $contact->id,
                'contract_type_id' => null,
                'duration_months' => 12,
                'start_date' => now()->addDay()->format('Y-m-d'),
                'rent_amount' => 5000.00,
                'payment_frequency_id' => $paymentFrequency->id,
                'security_deposit' => 2500.00,
                'valid_until' => now()->addDays(30)->format('Y-m-d'),
                'additional_charges' => [],
                'special_conditions' => ['en' => 'No pets', 'ar' => 'لا حيوانات'],
                'action' => 'save_draft',
            ]);

        $response->assertRedirect();

        $quote = LeaseQuote::query()
            ->where('unit_id', $unit->id)
            ->where('contact_id', $contact->id)
            ->first();

        $this->assertNotNull($quote, 'LeaseQuote should have been created.');
        $this->assertEquals(ExpireLeaseQuotes::STATUS_DRAFT, $quote->status_id);
        $this->assertEquals(12, $quote->duration_months);
        $this->assertEquals('5000.00', $quote->rent_amount);
        $this->assertNotNull($quote->public_token, 'public_token should be auto-generated on create.');
        $this->assertEquals(['en' => 'No pets', 'ar' => 'لا حيوانات'], $quote->special_conditions);
    }

    // -----------------------------------------------------------------------
    // Happy-path: store and send quote
    // -----------------------------------------------------------------------

    public function test_store_with_send_action_transitions_to_sent_status(): void
    {
        // Create statuses with the reserved IDs so StatusWorkflow can validate the transition.
        Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_DRAFT,
            'type' => 'lease_quote',
            'name_en' => 'draft',
        ]);
        Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_SENT,
            'type' => 'lease_quote',
            'name_en' => 'sent',
        ]);

        $unit = Unit::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $contact = Resident::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $paymentFrequency = Setting::factory()->create([
            'type' => 'payment_frequency',
            'name_en' => 'Quarterly',
        ]);

        $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('quotes.store'), [
                'unit_id' => $unit->id,
                'contact_id' => $contact->id,
                'duration_months' => 6,
                'start_date' => now()->addDay()->format('Y-m-d'),
                'rent_amount' => 10000.00,
                'payment_frequency_id' => $paymentFrequency->id,
                'valid_until' => now()->addDays(14)->format('Y-m-d'),
                'action' => 'send',
            ]);

        $quote = LeaseQuote::query()
            ->where('unit_id', $unit->id)
            ->where('contact_id', $contact->id)
            ->first();

        $this->assertNotNull($quote);
        $this->assertEquals(ExpireLeaseQuotes::STATUS_SENT, $quote->status_id);
    }

    // -----------------------------------------------------------------------
    // Validation: required fields
    // -----------------------------------------------------------------------

    public function test_store_returns_validation_errors_for_missing_required_fields(): void
    {
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('quotes.store'), []);

        $response->assertSessionHasErrors([
            'unit_id',
            'contact_id',
            'duration_months',
            'start_date',
            'rent_amount',
            'payment_frequency_id',
            'valid_until',
            'action',
        ]);
    }

    // -----------------------------------------------------------------------
    // Authorization: guest is redirected
    // -----------------------------------------------------------------------

    public function test_guests_are_redirected_from_create_page(): void
    {
        $response = $this->withoutVite()->get(route('quotes.create'));

        $response->assertRedirect(route('login'));
    }

    // -----------------------------------------------------------------------
    // Authorization: authenticated user without leasing permissions gets 403
    // -----------------------------------------------------------------------

    public function test_user_without_leasing_permissions_gets_403_on_index(): void
    {
        $unprivilegedUser = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Unprivileged Tenant']);

        AccountMembership::create([
            'user_id' => $unprivilegedUser->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'tenants',
        ]);

        $response = $this->actingAs($unprivilegedUser)
            ->withSession(['tenant_id' => $tenant->id])
            ->withoutVite()
            ->get(route('quotes.index'));

        $response->assertForbidden();
    }

    public function test_user_without_leasing_permissions_gets_403_on_create(): void
    {
        $unprivilegedUser = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Unprivileged Tenant Create']);

        AccountMembership::create([
            'user_id' => $unprivilegedUser->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'tenants',
        ]);

        $response = $this->actingAs($unprivilegedUser)
            ->withSession(['tenant_id' => $tenant->id])
            ->withoutVite()
            ->get(route('quotes.create'));

        $response->assertForbidden();
    }

    public function test_user_without_leasing_permissions_gets_403_on_store(): void
    {
        $unprivilegedUser = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Unprivileged Tenant Store']);

        AccountMembership::create([
            'user_id' => $unprivilegedUser->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'tenants',
        ]);

        $response = $this->actingAs($unprivilegedUser)
            ->withSession(['tenant_id' => $tenant->id])
            ->withoutVite()
            ->post(route('quotes.store'), []);

        $response->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Index renders correctly
    // -----------------------------------------------------------------------

    public function test_index_renders_inertia_page_for_authenticated_user(): void
    {
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('quotes.index'));

        $response->assertOk();
    }

    // -----------------------------------------------------------------------
    // Create page renders
    // -----------------------------------------------------------------------

    public function test_create_page_renders_for_authenticated_user(): void
    {
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('quotes.create'));

        $response->assertOk();
    }

    // -----------------------------------------------------------------------
    // Public preview: token-based lookup
    // -----------------------------------------------------------------------

    public function test_preview_returns_ok_for_valid_token(): void
    {
        $draftStatus = Status::factory()->create([
            'type' => 'lease_quote',
            'name_en' => 'sent',
        ]);

        $unit = Unit::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $contact = Resident::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $paymentFrequency = Setting::factory()->create(['type' => 'payment_frequency']);
        $admin = Admin::factory()->create(['account_tenant_id' => $this->tenant->id]);

        $quote = LeaseQuote::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'unit_id' => $unit->id,
            'contact_id' => $contact->id,
            'status_id' => $draftStatus->id,
            'payment_frequency_id' => $paymentFrequency->id,
            'created_by_id' => $admin->id,
        ]);

        $response = $this->withoutVite()
            ->get(route('quotes.preview', ['token' => $quote->public_token]));

        $response->assertOk();
    }

    public function test_preview_returns_404_for_invalid_token(): void
    {
        $response = $this->withoutVite()
            ->get(route('quotes.preview', ['token' => 'invalid-token']));

        $response->assertNotFound();
    }

    // -----------------------------------------------------------------------
    // Revise: GET revise page renders for sent quote
    // -----------------------------------------------------------------------

    public function test_revise_page_renders_for_sent_quote(): void
    {
        $sentStatus = Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_SENT,
            'type' => 'lease_quote',
            'name_en' => 'sent',
        ]);

        $unit = Unit::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $contact = Resident::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $paymentFrequency = Setting::factory()->create(['type' => 'payment_frequency']);
        $admin = Admin::factory()->create(['account_tenant_id' => $this->tenant->id]);

        $quote = LeaseQuote::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'unit_id' => $unit->id,
            'contact_id' => $contact->id,
            'status_id' => $sentStatus->id,
            'payment_frequency_id' => $paymentFrequency->id,
            'created_by_id' => $admin->id,
        ]);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('quotes.revise', $quote));

        $response->assertOk();
    }

    // -----------------------------------------------------------------------
    // Revise: POST storeRevision creates a new child quote at version+1
    // -----------------------------------------------------------------------

    public function test_store_revision_creates_new_quote_and_redirects_to_it(): void
    {
        Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_DRAFT,
            'type' => 'lease_quote',
            'name_en' => 'draft',
        ]);
        Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_SENT,
            'type' => 'lease_quote',
            'name_en' => 'sent',
        ]);

        $unit = Unit::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $contact = Resident::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $paymentFrequency = Setting::factory()->create(['type' => 'payment_frequency']);
        $admin = Admin::factory()->create(['account_tenant_id' => $this->tenant->id]);

        $original = LeaseQuote::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'unit_id' => $unit->id,
            'contact_id' => $contact->id,
            'status_id' => ExpireLeaseQuotes::STATUS_SENT,
            'payment_frequency_id' => $paymentFrequency->id,
            'created_by_id' => $admin->id,
            'version' => 1,
            'rent_amount' => 5000.00,
        ]);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('quotes.revise.store', $original), [
                'unit_id' => $unit->id,
                'contact_id' => $contact->id,
                'contract_type_id' => null,
                'duration_months' => 12,
                'start_date' => now()->addDay()->format('Y-m-d'),
                'rent_amount' => 6000.00,
                'payment_frequency_id' => $paymentFrequency->id,
                'security_deposit' => 0,
                'valid_until' => now()->addDays(30)->format('Y-m-d'),
                'revision_note' => 'Increased rent per prospect request.',
                'email_subject_prefix' => 'Updated Quote',
            ]);

        $response->assertRedirect();

        $revision = LeaseQuote::query()
            ->where('parent_quote_id', $original->id)
            ->first();

        $this->assertNotNull($revision, 'A child LeaseQuote revision should have been created.');
        $this->assertEquals(2, $revision->version);
        $this->assertEquals('6000.00', $revision->rent_amount);
        $this->assertEquals(ExpireLeaseQuotes::STATUS_SENT, $revision->status_id);
        $this->assertNotNull($revision->public_token);
        $this->assertEquals('Increased rent per prospect request.', $revision->revision_note);
    }

    // -----------------------------------------------------------------------
    // Revise: 403 when revising an accepted (terminal) quote
    // -----------------------------------------------------------------------

    public function test_revise_page_returns_403_for_accepted_quote(): void
    {
        $acceptedStatus = Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_ACCEPTED,
            'type' => 'lease_quote',
            'name_en' => 'accepted',
        ]);

        $unit = Unit::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $contact = Resident::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $paymentFrequency = Setting::factory()->create(['type' => 'payment_frequency']);
        $admin = Admin::factory()->create(['account_tenant_id' => $this->tenant->id]);

        $quote = LeaseQuote::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'unit_id' => $unit->id,
            'contact_id' => $contact->id,
            'status_id' => $acceptedStatus->id,
            'payment_frequency_id' => $paymentFrequency->id,
            'created_by_id' => $admin->id,
        ]);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('quotes.revise', $quote));

        $response->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Reject: POST rejects a viewed quote
    // -----------------------------------------------------------------------

    public function test_reject_transitions_viewed_quote_to_rejected(): void
    {
        $viewedStatus = Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_VIEWED,
            'type' => 'lease_quote',
            'name_en' => 'viewed',
        ]);

        Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_REJECTED,
            'type' => 'lease_quote',
            'name_en' => 'rejected',
        ]);

        $unit = Unit::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $contact = Resident::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $paymentFrequency = Setting::factory()->create(['type' => 'payment_frequency']);
        $admin = Admin::factory()->create(['account_tenant_id' => $this->tenant->id]);

        $quote = LeaseQuote::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'unit_id' => $unit->id,
            'contact_id' => $contact->id,
            'status_id' => $viewedStatus->id,
            'payment_frequency_id' => $paymentFrequency->id,
            'created_by_id' => $admin->id,
        ]);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('quotes.reject', $quote), [
                'rejection_reason' => 'Prospect found a cheaper option.',
            ]);

        $response->assertRedirect();

        $this->assertEquals(
            ExpireLeaseQuotes::STATUS_REJECTED,
            $quote->fresh()->status_id,
        );
        $this->assertEquals('Prospect found a cheaper option.', $quote->fresh()->rejection_reason);
    }

    // -----------------------------------------------------------------------
    // Reject: 403 when rejecting a draft (not viewed)
    // -----------------------------------------------------------------------

    public function test_reject_returns_403_for_draft_quote(): void
    {
        $draftStatus = Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_DRAFT,
            'type' => 'lease_quote',
            'name_en' => 'draft',
        ]);

        $unit = Unit::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $contact = Resident::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $paymentFrequency = Setting::factory()->create(['type' => 'payment_frequency']);
        $admin = Admin::factory()->create(['account_tenant_id' => $this->tenant->id]);

        $quote = LeaseQuote::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'unit_id' => $unit->id,
            'contact_id' => $contact->id,
            'status_id' => $draftStatus->id,
            'payment_frequency_id' => $paymentFrequency->id,
            'created_by_id' => $admin->id,
        ]);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('quotes.reject', $quote));

        $response->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Expire: PATCH manually expires a sent quote
    // -----------------------------------------------------------------------

    public function test_expire_transitions_sent_quote_to_expired(): void
    {
        Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_SENT,
            'type' => 'lease_quote',
            'name_en' => 'sent',
        ]);
        Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_EXPIRED,
            'type' => 'lease_quote',
            'name_en' => 'expired',
        ]);

        $unit = Unit::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $contact = Resident::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $paymentFrequency = Setting::factory()->create(['type' => 'payment_frequency']);
        $admin = Admin::factory()->create(['account_tenant_id' => $this->tenant->id]);

        $quote = LeaseQuote::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'unit_id' => $unit->id,
            'contact_id' => $contact->id,
            'status_id' => ExpireLeaseQuotes::STATUS_SENT,
            'payment_frequency_id' => $paymentFrequency->id,
            'created_by_id' => $admin->id,
        ]);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->patch(route('quotes.expire', $quote));

        $response->assertRedirect();

        $this->assertEquals(
            ExpireLeaseQuotes::STATUS_EXPIRED,
            $quote->fresh()->status_id,
        );
    }

    // -----------------------------------------------------------------------
    // Non-admin user gets 403 on revise
    // -----------------------------------------------------------------------

    public function test_user_without_leasing_permissions_gets_403_on_revise(): void
    {
        $sentStatus = Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_SENT,
            'type' => 'lease_quote',
            'name_en' => 'sent',
        ]);

        $unit = Unit::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $contact = Resident::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $paymentFrequency = Setting::factory()->create(['type' => 'payment_frequency']);
        $admin = Admin::factory()->create(['account_tenant_id' => $this->tenant->id]);

        $quote = LeaseQuote::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'unit_id' => $unit->id,
            'contact_id' => $contact->id,
            'status_id' => $sentStatus->id,
            'payment_frequency_id' => $paymentFrequency->id,
            'created_by_id' => $admin->id,
        ]);

        $unprivilegedUser = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Unprivileged Tenant Revise']);
        AccountMembership::create([
            'user_id' => $unprivilegedUser->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'tenants',
        ]);

        $response = $this->actingAs($unprivilegedUser)
            ->withSession(['tenant_id' => $tenant->id])
            ->withoutVite()
            ->get(route('quotes.revise', $quote));

        $response->assertForbidden();
    }
}
