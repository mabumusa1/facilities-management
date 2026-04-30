<?php

namespace Tests\Feature\Leasing;

use App\Models\AccountMembership;
use App\Models\Lease;
use App\Models\LeaseAmendment;
use App\Models\Setting;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class LeaseAmendmentTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    private Lease $lease;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RbacSeeder::class);

        $this->tenant = Tenant::create(['name' => 'Amendment Test Account']);

        $this->user = User::factory()->create();
        AccountMembership::create([
            'user_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);
        $this->user->assignRole('admins');

        $this->lease = Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'end_date' => '2027-05-31',
            'rental_total_amount' => '50000.00',
            'current_amendment_number' => 0,
        ]);

        $this->actingAs($this->user);
    }

    private function withTenant(): array
    {
        return ['tenant_id' => $this->tenant->id];
    }

    // ── GET amend page ─────────────────────────────────────────────────────────

    public function test_amend_page_renders_for_authorised_user(): void
    {
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leases.amend', $this->lease));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page->component('leasing/leases/Amend')
                ->has('lease')
                ->has('units')
                ->has('rentalContractTypes')
                ->has('paymentSchedules')
        );
    }

    public function test_amend_page_returns_403_for_unauthorised_user(): void
    {
        $viewUser = User::factory()->create();
        AccountMembership::create([
            'user_id' => $viewUser->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);
        // tenants role has leases.VIEW but not leases.UPDATE (required for amend).
        $viewUser->assignRole('tenants');

        $response = $this->actingAs($viewUser)
            ->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leases.amend', $this->lease));

        $response->assertForbidden();
    }

    // ── POST store amendment — happy path ──────────────────────────────────────

    public function test_store_amendment_creates_record_and_updates_lease(): void
    {
        $newEndDate = '2028-05-31';
        $reason = 'Tenant requested a 12-month extension.';

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.amend.store', $this->lease), [
                'end_date' => $newEndDate,
                'reason' => $reason,
            ]);

        $response->assertRedirect(route('leases.show', $this->lease));

        $this->lease->refresh();
        $this->assertSame($newEndDate, $this->lease->end_date->toDateString());
        $this->assertSame(1, $this->lease->current_amendment_number);

        $amendment = LeaseAmendment::where('lease_id', $this->lease->id)->first();
        $this->assertNotNull($amendment);
        $this->assertSame($reason, $amendment->reason);
        $this->assertSame(1, $amendment->amendment_number);
        $this->assertSame($this->user->id, $amendment->amended_by);
        $this->assertArrayHasKey('end_date', $amendment->changes);
        $this->assertSame('2027-05-31', $amendment->changes['end_date']['from']);
        $this->assertSame($newEndDate, $amendment->changes['end_date']['to']);
    }

    public function test_store_amendment_increments_amendment_number_on_each_call(): void
    {
        // First amendment.
        $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.amend.store', $this->lease), [
                'end_date' => '2028-01-01',
                'reason' => 'First amendment reason here.',
            ]);

        $this->assertSame(1, $this->lease->fresh()->current_amendment_number);

        // Second amendment.
        $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.amend.store', $this->lease), [
                'end_date' => '2029-01-01',
                'reason' => 'Second amendment reason here.',
            ]);

        $this->assertSame(2, $this->lease->fresh()->current_amendment_number);
        $this->assertCount(2, LeaseAmendment::where('lease_id', $this->lease->id)->get());
    }

    public function test_store_amendment_records_no_diff_when_values_are_unchanged(): void
    {
        $existingEndDate = $this->lease->end_date->toDateString();

        $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.amend.store', $this->lease), [
                'end_date' => $existingEndDate,
                'reason' => 'Administrative update with no actual changes.',
            ]);

        $amendment = LeaseAmendment::where('lease_id', $this->lease->id)->first();
        $this->assertNotNull($amendment);
        $this->assertEmpty($amendment->changes);
    }

    public function test_store_amendment_requires_reason(): void
    {
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.amend.store', $this->lease), [
                'end_date' => '2028-06-30',
                'reason' => '',
            ]);

        $response->assertSessionHasErrors('reason');
        $this->assertSame(0, $this->lease->fresh()->current_amendment_number);
    }

    public function test_store_amendment_reason_must_be_at_least_5_characters(): void
    {
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.amend.store', $this->lease), [
                'end_date' => '2028-06-30',
                'reason' => 'Hi',
            ]);

        $response->assertSessionHasErrors('reason');
    }

    // ── GET amendment history (JSON) ───────────────────────────────────────────

    public function test_amendment_history_returns_amendments_in_descending_order(): void
    {
        LeaseAmendment::factory()->create([
            'lease_id' => $this->lease->id,
            'amended_by' => $this->user->id,
            'amendment_number' => 1,
            'reason' => 'First',
        ]);
        LeaseAmendment::factory()->create([
            'lease_id' => $this->lease->id,
            'amended_by' => $this->user->id,
            'amendment_number' => 2,
            'reason' => 'Second',
        ]);

        $response = $this->withSession($this->withTenant())
            ->getJson(route('rf.leases.amendments', $this->lease));

        $response->assertOk();
        $response->assertJsonPath('data.0.amendment_number', 2);
        $response->assertJsonPath('data.1.amendment_number', 1);
    }

    // ── Cross-tenant isolation ─────────────────────────────────────────────────

    public function test_admin_from_other_tenant_cannot_amend_lease(): void
    {
        $tenantB = Tenant::create(['name' => 'Tenant B Amend']);
        $adminB = User::factory()->create();
        AccountMembership::create([
            'user_id' => $adminB->id,
            'account_tenant_id' => $tenantB->id,
            'role' => 'account_admins',
        ]);
        $adminB->assignRole('admins');

        $response = $this->actingAs($adminB)
            ->withSession(['tenant_id' => $tenantB->id])
            ->withoutVite()
            ->post(route('leases.amend.store', $this->lease), [
                'end_date' => '2030-01-01',
                'reason' => 'Cross-tenant amendment attempt.',
            ]);

        $response->assertForbidden();
        $this->assertSame(0, $this->lease->fresh()->current_amendment_number);
    }

    // ── Show page passes canAmend flag ─────────────────────────────────────────

    public function test_lease_show_passes_can_amend_true_for_authorised_user(): void
    {
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leases.show', $this->lease));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page->component('leasing/leases/Show')
                ->where('canAmend', true)
        );
    }

    public function test_lease_show_passes_amendments_to_vue(): void
    {
        LeaseAmendment::factory()->create([
            'lease_id' => $this->lease->id,
            'amended_by' => $this->user->id,
            'amendment_number' => 1,
        ]);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leases.show', $this->lease));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page->component('leasing/leases/Show')
                ->has('lease.amendments', 1)
        );
    }

    // ── Rental contract type amendment ────────────────────────────────────────

    public function test_store_amendment_updates_rental_contract_type(): void
    {
        $newContractType = Setting::factory()->create(['type' => 'rental_contract_type']);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.amend.store', $this->lease), [
                'rental_contract_type_id' => $newContractType->id,
                'reason' => 'Updated to new contract type.',
            ]);

        $response->assertRedirect(route('leases.show', $this->lease));

        $this->lease->refresh();
        $this->assertSame($newContractType->id, $this->lease->rental_contract_type_id);

        $amendment = LeaseAmendment::where('lease_id', $this->lease->id)->first();
        $this->assertArrayHasKey('rental_contract_type_id', $amendment->changes);
    }

    // ── AC1 gap: validation failures ──────────────────────────────────────────

    public function test_store_amendment_rejects_end_date_in_the_past(): void
    {
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.amend.store', $this->lease), [
                'end_date' => '2020-01-01',
                'reason' => 'Attempting to backdate the lease.',
            ]);

        $response->assertSessionHasErrors('end_date');
        $this->assertSame(0, $this->lease->fresh()->current_amendment_number);
    }

    public function test_store_amendment_rejects_end_date_of_today(): void
    {
        // 'after:today' requires strictly future — today itself must fail.
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.amend.store', $this->lease), [
                'end_date' => now()->toDateString(),
                'reason' => 'End date is today, not future.',
            ]);

        $response->assertSessionHasErrors('end_date');
        $this->assertSame(0, $this->lease->fresh()->current_amendment_number);
    }

    public function test_store_amendment_rejects_negative_rental_total_amount(): void
    {
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.amend.store', $this->lease), [
                'rental_total_amount' => '-100',
                'reason' => 'Trying a negative rental amount.',
            ]);

        $response->assertSessionHasErrors('rental_total_amount');
        $this->assertSame(0, $this->lease->fresh()->current_amendment_number);
    }

    public function test_store_amendment_rejects_negative_security_deposit_amount(): void
    {
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.amend.store', $this->lease), [
                'security_deposit_amount' => '-1',
                'reason' => 'Trying a negative deposit.',
            ]);

        $response->assertSessionHasErrors('security_deposit_amount');
        $this->assertSame(0, $this->lease->fresh()->current_amendment_number);
    }

    public function test_store_amendment_rejects_nonexistent_rental_contract_type(): void
    {
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.amend.store', $this->lease), [
                'rental_contract_type_id' => 999999,
                'reason' => 'Using a non-existent contract type.',
            ]);

        $response->assertSessionHasErrors('rental_contract_type_id');
        $this->assertSame(0, $this->lease->fresh()->current_amendment_number);
    }

    public function test_store_amendment_rejects_nonexistent_payment_schedule(): void
    {
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.amend.store', $this->lease), [
                'payment_schedule_id' => 999999,
                'reason' => 'Using a non-existent payment schedule.',
            ]);

        $response->assertSessionHasErrors('payment_schedule_id');
        $this->assertSame(0, $this->lease->fresh()->current_amendment_number);
    }

    public function test_store_amendment_reason_exactly_5_characters_passes(): void
    {
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.amend.store', $this->lease), [
                'end_date' => '2028-12-31',
                'reason' => 'Five!',
            ]);

        $response->assertRedirect(route('leases.show', $this->lease));
        $this->assertSame(1, $this->lease->fresh()->current_amendment_number);
    }

    public function test_store_amendment_reason_4_characters_fails(): void
    {
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.amend.store', $this->lease), [
                'end_date' => '2028-12-31',
                'reason' => 'Four',
            ]);

        $response->assertSessionHasErrors('reason');
        $this->assertSame(0, $this->lease->fresh()->current_amendment_number);
    }

    // ── AC1 gap: multi-field diff recorded correctly ───────────────────────────

    public function test_store_amendment_records_multi_field_diff(): void
    {
        $newEndDate = '2029-01-01';
        $newAmount = '75000.00';

        $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.amend.store', $this->lease), [
                'end_date' => $newEndDate,
                'rental_total_amount' => $newAmount,
                'reason' => 'Extension with revised rental amount.',
            ]);

        $amendment = LeaseAmendment::where('lease_id', $this->lease->id)->first();
        $this->assertNotNull($amendment);
        $this->assertArrayHasKey('end_date', $amendment->changes);
        $this->assertArrayHasKey('rental_total_amount', $amendment->changes);
        $this->assertSame('2027-05-31', $amendment->changes['end_date']['from']);
        $this->assertSame($newEndDate, $amendment->changes['end_date']['to']);
    }

    // ── AC1 gap: generate_addendum field is silently ignored ──────────────────

    public function test_store_amendment_ignores_generate_addendum_field(): void
    {
        // The generate_addendum checkbox is UI-only (Documents #176 deferred).
        // Passing it must not cause a validation error.
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.amend.store', $this->lease), [
                'end_date' => '2028-06-30',
                'reason' => 'With addendum checkbox ticked.',
                'generate_addendum' => true,
            ]);

        $response->assertRedirect(route('leases.show', $this->lease));

        $amendment = LeaseAmendment::where('lease_id', $this->lease->id)->first();
        $this->assertNotNull($amendment);
        $this->assertNull($amendment->addendum_media_id);
    }

    // ── AC1 gap: JSON (rf) endpoint for storeAmendment ────────────────────────

    public function test_store_amendment_returns_json_for_json_request(): void
    {
        $response = $this->withSession($this->withTenant())
            ->postJson(route('leases.amend.store', $this->lease), [
                'end_date' => '2028-09-30',
                'reason' => 'JSON API amendment call.',
            ]);

        $response->assertOk();
        $response->assertJsonPath('data.lease_id', $this->lease->id);
        $response->assertJsonPath('data.amendment_number', 1);
        $response->assertJsonStructure(['data' => ['lease_id', 'amendment_number', 'changes'], 'message']);
    }

    // ── AC2 gap: amendment history includes amended_by user data ─────────────

    public function test_amendment_history_includes_amended_by_user_data(): void
    {
        LeaseAmendment::factory()->create([
            'lease_id' => $this->lease->id,
            'amended_by' => $this->user->id,
            'amendment_number' => 1,
            'reason' => 'Check user data in history.',
        ]);

        $response = $this->withSession($this->withTenant())
            ->getJson(route('rf.leases.amendments', $this->lease));

        $response->assertOk();
        $response->assertJsonPath('data.0.amended_by.id', $this->user->id);
        $this->assertNotNull($response->json('data.0.amended_by.name'));
    }

    public function test_amendment_history_returns_empty_data_when_no_amendments(): void
    {
        $response = $this->withSession($this->withTenant())
            ->getJson(route('rf.leases.amendments', $this->lease));

        $response->assertOk();
        $response->assertJsonPath('data', []);
    }

    // ── AC4 gap: unauthenticated requests are rejected ────────────────────────

    public function test_unauthenticated_user_cannot_access_amend_page(): void
    {
        // Log out the user set up in setUp() to test unauthenticated access.
        auth()->logout();

        $response = $this->withoutVite()
            ->get(route('leases.amend', $this->lease));

        $response->assertRedirect(route('login'));
    }

    public function test_unauthenticated_user_cannot_post_amendment(): void
    {
        auth()->logout();

        $response = $this->withoutVite()
            ->post(route('leases.amend.store', $this->lease), [
                'end_date' => '2028-06-30',
                'reason' => 'Unauthenticated amendment attempt.',
            ]);

        $response->assertRedirect(route('login'));
        $this->assertSame(0, $this->lease->fresh()->current_amendment_number);
    }

    public function test_unauthenticated_user_cannot_access_amendment_history(): void
    {
        auth()->logout();

        // The rf amendments route is under the same auth middleware.
        // A JSON request without auth returns a 401 (unauthenticated) or redirect.
        $response = $this->getJson(route('rf.leases.amendments', $this->lease));

        $response->assertUnauthorized();
    }

    // ── AC2 gap: history 403 when user cannot view the lease ─────────────────

    public function test_amendment_history_returns_403_for_user_without_view_permission(): void
    {
        // 'dependents' role has no lease permissions — view policy will deny.
        $restrictedUser = User::factory()->create();
        AccountMembership::create([
            'user_id' => $restrictedUser->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);
        $restrictedUser->assignRole('dependents');

        $response = $this->actingAs($restrictedUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->getJson(route('rf.leases.amendments', $this->lease));

        $response->assertForbidden();
    }

    // ── AC4 gap: non-existent lease returns 404 ───────────────────────────────

    public function test_amend_page_returns_404_for_nonexistent_lease(): void
    {
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leases.amend', ['lease' => 999999]));

        $response->assertNotFound();
    }

    public function test_store_amendment_returns_404_for_nonexistent_lease(): void
    {
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.amend.store', ['lease' => 999999]), [
                'end_date' => '2028-06-30',
                'reason' => 'Amendment on ghost lease.',
            ]);

        $response->assertNotFound();
    }

    // ── AC1 gap: Arabic reason text is accepted (unicode edge case) ───────────

    public function test_store_amendment_accepts_arabic_reason_text(): void
    {
        $arabicReason = 'تمديد العقد بناءً على طلب المستأجر.';

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.amend.store', $this->lease), [
                'end_date' => '2028-12-31',
                'reason' => $arabicReason,
            ]);

        $response->assertRedirect(route('leases.show', $this->lease));

        $amendment = LeaseAmendment::where('lease_id', $this->lease->id)->first();
        $this->assertNotNull($amendment);
        $this->assertSame($arabicReason, $amendment->reason);
    }

    // ── AC4 gap: show page hides canAmend for unauthorised user ───────────────

    public function test_lease_show_passes_can_amend_false_for_unauthorised_user(): void
    {
        $viewUser = User::factory()->create();
        AccountMembership::create([
            'user_id' => $viewUser->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);
        $viewUser->assignRole('tenants');

        $response = $this->actingAs($viewUser)
            ->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leases.show', $this->lease));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page->component('leasing/leases/Show')
                ->where('canAmend', false)
        );
    }
}
