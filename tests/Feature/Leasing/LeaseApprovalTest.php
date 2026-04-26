<?php

namespace Tests\Feature\Leasing;

use App\Console\Commands\ExpireLeaseQuotes;
use App\Models\AccountMembership;
use App\Models\Lease;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\WorkflowStatusChangedNotification;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LeaseApprovalTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    private Lease $lease;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RbacSeeder::class);

        $this->tenant = Tenant::create(['name' => 'Approval Test Account']);

        // Create a manager user with the 'managers' role (which gets leases.APPROVE).
        $this->user = User::factory()->create();
        AccountMembership::create([
            'user_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);
        $this->user->assignRole('admins');

        // Seed the three lease-application status rows required by ApprovalController.
        Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_PENDING_APPLICATION,
            'type' => 'lease',
            'name_en' => 'pending_application',
        ]);
        Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_APPROVED_APPLICATION,
            'type' => 'lease',
            'name_en' => 'approved_application',
        ]);
        Status::factory()->create([
            'id' => ExpireLeaseQuotes::STATUS_REJECTED_APPLICATION,
            'type' => 'lease',
            'name_en' => 'rejected_application',
        ]);

        $this->lease = Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => ExpireLeaseQuotes::STATUS_PENDING_APPLICATION,
        ]);

        $this->actingAs($this->user);
    }

    private function withTenant(): array
    {
        return ['tenant_id' => $this->tenant->id];
    }

    // ── Approvals Index ────────────────────────────────────────────────────────

    public function test_approvals_index_renders_for_authorised_user(): void
    {
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('approvals.index'));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page->component('leasing/approvals/Index')
        );
    }

    public function test_approvals_index_returns_403_for_unprivileged_user(): void
    {
        $unprivilegedUser = User::factory()->create();
        $otherTenant = Tenant::create(['name' => 'Unpriv Tenant']);
        AccountMembership::create([
            'user_id' => $unprivilegedUser->id,
            'account_tenant_id' => $otherTenant->id,
            'role' => 'tenants',
        ]);

        $response = $this->actingAs($unprivilegedUser)
            ->withSession(['tenant_id' => $otherTenant->id])
            ->withoutVite()
            ->get(route('approvals.index'));

        $response->assertForbidden();
    }

    // ── Approve — Happy Path ───────────────────────────────────────────────────

    public function test_approve_transitions_lease_to_approved_and_records_actor(): void
    {
        Notification::fake();

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.approve', $this->lease));

        $response->assertRedirect(route('leases.show', $this->lease));

        $this->lease->refresh();

        $this->assertSame(ExpireLeaseQuotes::STATUS_APPROVED_APPLICATION, $this->lease->status_id);
        $this->assertSame($this->user->id, $this->lease->approved_by_id);
        $this->assertNotNull($this->lease->approved_at);
        $this->assertNull($this->lease->rejected_at);
    }

    public function test_approve_sends_workflow_notification(): void
    {
        Notification::fake();

        $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.approve', $this->lease));

        Notification::assertSentTo(
            $this->user,
            WorkflowStatusChangedNotification::class,
        );
    }

    public function test_approve_returns_403_for_unprivileged_user(): void
    {
        $unprivilegedUser = User::factory()->create();
        $otherTenant = Tenant::create(['name' => 'Unpriv Approve Tenant']);
        AccountMembership::create([
            'user_id' => $unprivilegedUser->id,
            'account_tenant_id' => $otherTenant->id,
            'role' => 'tenants',
        ]);

        $response = $this->actingAs($unprivilegedUser)
            ->withSession(['tenant_id' => $otherTenant->id])
            ->withoutVite()
            ->post(route('leases.approve', $this->lease));

        $response->assertForbidden();
        $this->assertSame(ExpireLeaseQuotes::STATUS_PENDING_APPLICATION, $this->lease->fresh()->status_id);
    }

    // ── Reject — Happy Path ────────────────────────────────────────────────────

    public function test_reject_transitions_lease_to_rejected_and_records_reason(): void
    {
        Notification::fake();

        $reason = 'Insufficient income documentation. Please provide 6 months of bank statements.';

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.reject', $this->lease), [
                'rejection_reason' => $reason,
            ]);

        $response->assertRedirect(route('leases.show', $this->lease));

        $this->lease->refresh();

        $this->assertSame(ExpireLeaseQuotes::STATUS_REJECTED_APPLICATION, $this->lease->status_id);
        $this->assertSame($this->user->id, $this->lease->rejected_by_id);
        $this->assertNotNull($this->lease->rejected_at);
        $this->assertSame($reason, $this->lease->rejection_reason);
        $this->assertNull($this->lease->approved_at);
    }

    public function test_reject_requires_rejection_reason(): void
    {
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.reject', $this->lease), [
                'rejection_reason' => '',
            ]);

        $response->assertSessionHasErrors('rejection_reason');
        $this->assertSame(ExpireLeaseQuotes::STATUS_PENDING_APPLICATION, $this->lease->fresh()->status_id);
    }

    public function test_reject_reason_must_be_at_least_10_characters(): void
    {
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->post(route('leases.reject', $this->lease), [
                'rejection_reason' => 'Short',
            ]);

        $response->assertSessionHasErrors('rejection_reason');
        $this->assertSame(ExpireLeaseQuotes::STATUS_PENDING_APPLICATION, $this->lease->fresh()->status_id);
    }

    public function test_reject_returns_403_for_unprivileged_user(): void
    {
        $unprivilegedUser = User::factory()->create();
        $otherTenant = Tenant::create(['name' => 'Unpriv Reject Tenant']);
        AccountMembership::create([
            'user_id' => $unprivilegedUser->id,
            'account_tenant_id' => $otherTenant->id,
            'role' => 'tenants',
        ]);

        $response = $this->actingAs($unprivilegedUser)
            ->withSession(['tenant_id' => $otherTenant->id])
            ->withoutVite()
            ->post(route('leases.reject', $this->lease), [
                'rejection_reason' => 'Insufficient documentation provided.',
            ]);

        $response->assertForbidden();
        $this->assertSame(ExpireLeaseQuotes::STATUS_PENDING_APPLICATION, $this->lease->fresh()->status_id);
    }

    // ── Cross-tenant isolation ─────────────────────────────────────────────────

    public function test_admin_from_other_tenant_cannot_approve_lease(): void
    {
        $tenantB = Tenant::create(['name' => 'Tenant B']);
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
            ->post(route('leases.approve', $this->lease)); // lease belongs to $this->tenant

        $response->assertForbidden();
        $this->assertSame(ExpireLeaseQuotes::STATUS_PENDING_APPLICATION, $this->lease->fresh()->status_id);
    }

    public function test_admin_from_other_tenant_cannot_reject_lease(): void
    {
        $tenantB = Tenant::create(['name' => 'Tenant B Reject']);
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
            ->post(route('leases.reject', $this->lease), [ // lease belongs to $this->tenant
                'rejection_reason' => 'Attempting cross-tenant rejection.',
            ]);

        $response->assertForbidden();
        $this->assertSame(ExpireLeaseQuotes::STATUS_PENDING_APPLICATION, $this->lease->fresh()->status_id);
    }

    // ── Lease Show renders canApprove ──────────────────────────────────────────

    public function test_lease_show_passes_can_approve_flag_to_vue(): void
    {
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leases.show', $this->lease));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page->component('leasing/leases/Show')
                ->where('canApprove', true)
                ->where('isPendingApplication', true)
        );
    }

    public function test_lease_show_can_approve_is_false_for_unprivileged_user(): void
    {
        $viewUser = User::factory()->create();
        AccountMembership::create([
            'user_id' => $viewUser->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);
        // tenants role has leases.VIEW but not leases.APPROVE.
        $viewUser->assignRole('tenants');

        $response = $this->actingAs($viewUser)
            ->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leases.show', $this->lease));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page->where('canApprove', false)
        );
    }
}
