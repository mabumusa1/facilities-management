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
}
