<?php

namespace Tests\Feature\Leasing;

use App\Console\Commands\ExpireLeaseQuotes;
use App\Models\AccountMembership;
use App\Models\Admin;
use App\Models\Lease;
use App\Models\LeaseKycDocument;
use App\Models\LeaseQuote;
use App\Models\Resident;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\UnitCategory;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ConvertQuoteToLeaseTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesSeeder::class);

        $this->user = User::factory()->create();
        $this->tenant = Tenant::create(['name' => 'Convert Test Account']);

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

    /**
     * Create a fully-seeded accepted quote ready for conversion.
     */
    private function makeAcceptedQuote(): LeaseQuote
    {
        $unit = Unit::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $contact = Resident::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $paymentFrequency = Setting::factory()->create([
            'type' => 'payment_frequency',
            'name_en' => 'Monthly',
        ]);
        $admin = Admin::factory()->create(['account_tenant_id' => $this->tenant->id]);

        Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_ACCEPTED,
            'type' => 'lease_quote',
            'name_en' => 'accepted',
        ]);

        return LeaseQuote::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'unit_id' => $unit->id,
            'contact_id' => $contact->id,
            'status_id' => ExpireLeaseQuotes::STATUS_ACCEPTED,
            'payment_frequency_id' => $paymentFrequency->id,
            'created_by_id' => $admin->id,
            'rent_amount' => 85000.00,
            'duration_months' => 12,
        ]);
    }

    private function makeLeaseRequiredData(LeaseQuote $quote): array
    {
        $unitCategory = UnitCategory::factory()->create();
        $rentalContractType = Setting::factory()->create([
            'type' => 'rental_contract_type',
            'name_en' => 'Yearly Rental',
        ]);
        $paymentSchedule = Setting::factory()->create([
            'type' => 'payment_schedule',
            'name_en' => 'Annual',
        ]);

        // Seed the pending_application status row.
        Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_PENDING_APPLICATION,
            'type' => 'lease',
            'name_en' => 'pending_application',
        ]);

        return [
            'autoGenerateLeaseNumber' => true,
            'tenant_id' => $quote->contact_id,
            'lease_unit_type_id' => $unitCategory->id,
            'rental_contract_type_id' => $rentalContractType->id,
            'payment_schedule_id' => $paymentSchedule->id,
            'start_date' => now()->addDay()->format('Y-m-d'),
            'end_date' => now()->addYear()->format('Y-m-d'),
            'handover_date' => now()->addDay()->format('Y-m-d'),
            'tenant_type' => 'individual',
            'rental_type' => 'total',
            'rental_total_amount' => 85000.00,
            'number_of_months' => 12,
            'unit_id' => $quote->unit_id,
        ];
    }

    // -----------------------------------------------------------------------
    // GET /leases/quotes/{quote}/convert — convert form renders
    // -----------------------------------------------------------------------

    public function test_convert_page_renders_for_accepted_quote(): void
    {
        $quote = $this->makeAcceptedQuote();

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('quotes.convert', $quote));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page->component('leasing/quotes/Convert')
        );
    }

    public function test_convert_page_redirects_to_kyc_if_already_converted(): void
    {
        $quote = $this->makeAcceptedQuote();

        Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_PENDING_APPLICATION,
            'type' => 'lease',
            'name_en' => 'pending_application',
        ]);

        // Create an existing lease linked to this quote.
        $existingLease = Lease::factory()->create([
            'quote_id' => $quote->id,
            'account_tenant_id' => $this->tenant->id,
            'status_id' => ExpireLeaseQuotes::STATUS_PENDING_APPLICATION,
        ]);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('quotes.convert', $quote));

        $response->assertRedirect(route('leases.kyc', $existingLease));
    }

    // -----------------------------------------------------------------------
    // POST /leases/quotes/{quote}/convert — happy path: creates Lease
    // -----------------------------------------------------------------------

    public function test_store_conversion_creates_lease_with_pending_application_status(): void
    {
        $quote = $this->makeAcceptedQuote();
        $payload = $this->makeLeaseRequiredData($quote);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('quotes.convert.store', $quote), $payload);

        // Should redirect to KYC page.
        $response->assertRedirect();

        $lease = Lease::query()->where('quote_id', $quote->id)->first();

        $this->assertNotNull($lease, 'A Lease record should have been created.');
        $this->assertEquals(ExpireLeaseQuotes::STATUS_PENDING_APPLICATION, $lease->status_id);
        $this->assertEquals($quote->id, $lease->quote_id);
        $this->assertEquals($quote->account_tenant_id, $lease->account_tenant_id);
        $this->assertEquals(85000.00, (float) $lease->rental_total_amount);
    }

    public function test_store_conversion_attaches_unit_to_lease(): void
    {
        $quote = $this->makeAcceptedQuote();
        $payload = $this->makeLeaseRequiredData($quote);

        $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('quotes.convert.store', $quote), $payload);

        $lease = Lease::query()->where('quote_id', $quote->id)->first();

        $this->assertNotNull($lease);
        $this->assertCount(1, $lease->units, 'The unit from the quote should be attached to the lease.');
        $this->assertEquals($quote->unit_id, $lease->units->first()->id);
    }

    // -----------------------------------------------------------------------
    // Authorization guard: non-accepted quote returns error
    // -----------------------------------------------------------------------

    public function test_store_conversion_rejects_non_accepted_quote(): void
    {
        $unit = Unit::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $contact = Resident::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $paymentFrequency = Setting::factory()->create(['type' => 'payment_frequency']);
        $admin = Admin::factory()->create(['account_tenant_id' => $this->tenant->id]);

        Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_DRAFT,
            'type' => 'lease_quote',
            'name_en' => 'draft',
        ]);

        $draftQuote = LeaseQuote::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'unit_id' => $unit->id,
            'contact_id' => $contact->id,
            'status_id' => ExpireLeaseQuotes::STATUS_DRAFT,
            'payment_frequency_id' => $paymentFrequency->id,
            'created_by_id' => $admin->id,
        ]);

        $unitCategory = UnitCategory::factory()->create();
        $rentalContractType = Setting::factory()->create(['type' => 'rental_contract_type']);
        $paymentSchedule = Setting::factory()->create(['type' => 'payment_schedule']);

        Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_PENDING_APPLICATION,
            'type' => 'lease',
            'name_en' => 'pending_application',
        ]);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('quotes.convert.store', $draftQuote), [
                'autoGenerateLeaseNumber' => true,
                'tenant_id' => $contact->id,
                'lease_unit_type_id' => $unitCategory->id,
                'rental_contract_type_id' => $rentalContractType->id,
                'payment_schedule_id' => $paymentSchedule->id,
                'start_date' => now()->addDay()->format('Y-m-d'),
                'end_date' => now()->addYear()->format('Y-m-d'),
                'handover_date' => now()->addDay()->format('Y-m-d'),
                'tenant_type' => 'individual',
                'rental_type' => 'total',
                'rental_total_amount' => 5000.00,
                'unit_id' => $unit->id,
            ]);

        $response->assertSessionHasErrors('quote');
        $this->assertDatabaseMissing('rf_leases', ['quote_id' => $draftQuote->id]);
    }

    // -----------------------------------------------------------------------
    // Authorization: 403 for user without leases.CREATE permission
    // -----------------------------------------------------------------------

    public function test_convert_page_returns_403_for_user_without_create_permission(): void
    {
        $quote = $this->makeAcceptedQuote();

        $unprivilegedUser = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Unpriv Convert Tenant']);
        AccountMembership::create([
            'user_id' => $unprivilegedUser->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'tenants',
        ]);

        $response = $this->actingAs($unprivilegedUser)
            ->withSession(['tenant_id' => $tenant->id])
            ->withoutVite()
            ->get(route('quotes.convert', $quote));

        $response->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Authorization: 403 for KYC abilities without leases.UPDATE permission
    // -----------------------------------------------------------------------

    public function test_upload_kyc_returns_403_for_unprivileged_user(): void
    {
        Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_PENDING_APPLICATION,
            'type' => 'lease',
            'name_en' => 'pending_application',
        ]);

        $lease = Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => ExpireLeaseQuotes::STATUS_PENDING_APPLICATION,
        ]);

        $unprivilegedUser = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Unpriv KYC Upload Tenant']);
        AccountMembership::create([
            'user_id' => $unprivilegedUser->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'tenants',
        ]);

        $response = $this->actingAs($unprivilegedUser)
            ->withSession(['tenant_id' => $tenant->id])
            ->withoutVite()
            ->post(route('leases.kyc.upload', $lease), []);

        $response->assertForbidden();
    }

    public function test_remove_kyc_document_returns_403_for_unprivileged_user(): void
    {
        Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_PENDING_APPLICATION,
            'type' => 'lease',
            'name_en' => 'pending_application',
        ]);

        $lease = Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => ExpireLeaseQuotes::STATUS_PENDING_APPLICATION,
        ]);

        $document = LeaseKycDocument::create([
            'lease_id' => $lease->id,
            'document_type_id' => 1,
            'is_required' => false,
            'original_file_name' => 'test.pdf',
            'stored_path' => 'leases/1/kyc/test.pdf',
            'account_tenant_id' => $this->tenant->id,
        ]);

        $unprivilegedUser = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Unpriv KYC Remove Tenant']);
        AccountMembership::create([
            'user_id' => $unprivilegedUser->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'tenants',
        ]);

        $response = $this->actingAs($unprivilegedUser)
            ->withSession(['tenant_id' => $tenant->id])
            ->withoutVite()
            ->delete(route('leases.kyc.destroy', [$lease, $document]));

        $response->assertForbidden();
    }

    public function test_submit_for_approval_returns_403_for_unprivileged_user(): void
    {
        Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_PENDING_APPLICATION,
            'type' => 'lease',
            'name_en' => 'pending_application',
        ]);

        $lease = Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => ExpireLeaseQuotes::STATUS_PENDING_APPLICATION,
        ]);

        $unprivilegedUser = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Unpriv Submit Tenant']);
        AccountMembership::create([
            'user_id' => $unprivilegedUser->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'tenants',
        ]);

        $response = $this->actingAs($unprivilegedUser)
            ->withSession(['tenant_id' => $tenant->id])
            ->withoutVite()
            ->post(route('leases.submit', $lease));

        $response->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Multi-tenant isolation: convert form dropdowns are tenant-scoped
    // -----------------------------------------------------------------------

    public function test_convert_form_does_not_expose_other_tenant_units_residents_admins(): void
    {
        $quote = $this->makeAcceptedQuote();

        // Create data in a different tenant.
        $otherTenant = Tenant::create(['name' => 'Other Tenant']);
        $otherUnit = Unit::factory()->create(['account_tenant_id' => $otherTenant->id]);
        $otherResident = Resident::factory()->create(['account_tenant_id' => $otherTenant->id]);
        $otherAdmin = Admin::factory()->create(['account_tenant_id' => $otherTenant->id]);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('quotes.convert', $quote));

        $response->assertOk();
        $response->assertInertia(function ($page) use ($otherUnit, $otherResident, $otherAdmin) {
            $units = collect($page->toArray()['props']['units']);
            $residents = collect($page->toArray()['props']['residents']);
            $admins = collect($page->toArray()['props']['admins']);

            $this->assertFalse($units->pluck('id')->contains($otherUnit->id), 'Other tenant unit must not appear');
            $this->assertFalse($residents->pluck('id')->contains($otherResident->id), 'Other tenant resident must not appear');
            $this->assertFalse($admins->pluck('id')->contains($otherAdmin->id), 'Other tenant admin must not appear');
        });
    }

    // -----------------------------------------------------------------------
    // KYC page renders
    // -----------------------------------------------------------------------

    public function test_kyc_page_renders_for_lease_with_pending_application_status(): void
    {
        Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_PENDING_APPLICATION,
            'type' => 'lease',
            'name_en' => 'pending_application',
        ]);

        $lease = Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => ExpireLeaseQuotes::STATUS_PENDING_APPLICATION,
        ]);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leases.kyc', $lease));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page->component('leasing/leases/Kyc')
        );
    }

    // -----------------------------------------------------------------------
    // Idempotency: duplicate convert redirects to existing lease KYC
    // -----------------------------------------------------------------------

    public function test_duplicate_convert_redirects_to_existing_lease_kyc(): void
    {
        $quote = $this->makeAcceptedQuote();
        $payload = $this->makeLeaseRequiredData($quote);

        // First conversion.
        $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('quotes.convert.store', $quote), $payload);

        $leaseAfterFirst = Lease::query()->where('quote_id', $quote->id)->first();
        $this->assertNotNull($leaseAfterFirst);

        // Second conversion attempt.
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('quotes.convert.store', $quote), $payload);

        $response->assertRedirect(route('leases.kyc', $leaseAfterFirst));

        // Should still only have one Lease for this quote.
        $this->assertSame(1, Lease::query()->where('quote_id', $quote->id)->count());
    }

    // -----------------------------------------------------------------------
    // KYC submit: blocked when required documents are missing
    // -----------------------------------------------------------------------

    public function test_submit_for_approval_fails_when_required_docs_are_missing(): void
    {
        Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_PENDING_APPLICATION,
            'type' => 'lease',
            'name_en' => 'pending_application',
        ]);

        $lease = Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => ExpireLeaseQuotes::STATUS_PENDING_APPLICATION,
        ]);

        // Create a required KYC document type without uploading any document.
        Setting::factory()->create([
            'type' => 'kyc_document_type',
            'name_en' => 'National ID',
            'metadata' => ['is_required' => true],
        ]);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.submit', $lease));

        $response->assertSessionHasErrors('kyc_documents');
        $this->assertFalse((bool) $lease->fresh()?->kyc_complete);
    }

    // -----------------------------------------------------------------------
    // KYC submit: success when all required docs are present
    // -----------------------------------------------------------------------

    public function test_submit_for_approval_succeeds_when_all_required_docs_are_uploaded(): void
    {
        Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_PENDING_APPLICATION,
            'type' => 'lease',
            'name_en' => 'pending_application',
        ]);

        $lease = Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => ExpireLeaseQuotes::STATUS_PENDING_APPLICATION,
        ]);

        $docType = Setting::factory()->create([
            'type' => 'kyc_document_type',
            'name_en' => 'National ID',
            'metadata' => ['is_required' => true],
        ]);

        LeaseKycDocument::create([
            'lease_id' => $lease->id,
            'document_type_id' => $docType->id,
            'is_required' => true,
            'original_file_name' => 'id.pdf',
            'stored_path' => 'leases/1/kyc/id.pdf',
            'account_tenant_id' => $this->tenant->id,
        ]);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.submit', $lease));

        $response->assertRedirect(route('leases.show', $lease));

        $this->assertTrue((bool) $lease->fresh()?->kyc_complete);
        $this->assertNotNull($lease->fresh()?->kyc_submitted_at);
    }
}
