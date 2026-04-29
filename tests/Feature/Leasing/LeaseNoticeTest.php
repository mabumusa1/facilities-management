<?php

namespace Tests\Feature\Leasing;

use App\Enums\LeaseNoticeType;
use App\Models\AccountMembership;
use App\Models\Lease;
use App\Models\LeaseNotice;
use App\Models\Resident;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class LeaseNoticeTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    private Lease $lease;

    private Resident $resident;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RbacSeeder::class);

        $this->tenant = Tenant::create(['name' => 'Notice Test Account']);

        $this->user = User::factory()->create();
        AccountMembership::create([
            'user_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);
        $this->user->assignRole('admins');

        $this->resident = Resident::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'email' => 'tenant@example.com',
        ]);

        $this->lease = Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'tenant_id' => $this->resident->id,
        ]);

        $this->actingAs($this->user);
    }

    private function withTenant(): array
    {
        return ['tenant_id' => $this->tenant->id];
    }

    // ── Index ──────────────────────────────────────────────────────────────────

    public function test_notices_index_renders_for_authorised_user(): void
    {
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leases.notices.index', $this->lease));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('leasing/leases/Notices'));
    }

    public function test_notices_index_returns_403_for_unauthorised_user(): void
    {
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leases.notices.index', $this->lease));

        $response->assertForbidden();
    }

    // ── Store ──────────────────────────────────────────────────────────────────

    public function test_authorised_user_can_send_a_notice(): void
    {
        $response = $this->withSession($this->withTenant())
            ->post(route('leases.notices.store', $this->lease), [
                'type' => LeaseNoticeType::MoveOutReminder->value,
                'subject_en' => 'Lease Expiry Reminder',
                'body_en' => 'Your lease is expiring soon.',
                'subject_ar' => 'تذكير بانتهاء العقد',
                'body_ar' => 'عقدك على وشك الانتهاء.',
            ]);

        $response->assertRedirect(route('leases.notices.index', $this->lease));

        $this->assertDatabaseHas('lease_notices', [
            'lease_id' => $this->lease->id,
            'tenant_id' => $this->resident->id,
            'sent_by' => $this->user->id,
            'type' => LeaseNoticeType::MoveOutReminder->value,
            'subject_en' => 'Lease Expiry Reminder',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->withSession($this->withTenant())
            ->post(route('leases.notices.store', $this->lease), []);

        $response->assertSessionHasErrors(['type', 'subject_en', 'body_en', 'subject_ar', 'body_ar']);
    }

    public function test_store_returns_error_when_tenant_has_no_email(): void
    {
        $this->resident->update(['email' => null]);

        $response = $this->withSession($this->withTenant())
            ->post(route('leases.notices.store', $this->lease), [
                'type' => LeaseNoticeType::FreeForm->value,
                'subject_en' => 'Hello',
                'body_en' => 'Message body',
                'subject_ar' => 'مرحبا',
                'body_ar' => 'نص الرسالة',
            ]);

        $response->assertSessionHasErrors('tenant_email');
        $this->assertDatabaseCount('lease_notices', 0);
    }

    public function test_store_returns_403_for_user_without_update_permission(): void
    {
        $tenantUser = User::factory()->create();
        AccountMembership::create([
            'user_id' => $tenantUser->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_tenants',
        ]);
        $tenantUser->assignRole('tenants');

        $this->actingAs($tenantUser);

        $response = $this->withSession($this->withTenant())
            ->post(route('leases.notices.store', $this->lease), [
                'type' => LeaseNoticeType::FreeForm->value,
                'subject_en' => 'Hello',
                'body_en' => 'Message body',
                'subject_ar' => 'مرحبا',
                'body_ar' => 'نص الرسالة',
            ]);

        $response->assertForbidden();
    }

    // ── Show ───────────────────────────────────────────────────────────────────

    public function test_show_returns_notice_json_for_authorised_user(): void
    {
        $notice = LeaseNotice::factory()->create([
            'lease_id' => $this->lease->id,
            'tenant_id' => $this->resident->id,
            'sent_by' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $response = $this->withSession($this->withTenant())
            ->getJson(route('leases.notices.show', [$this->lease, $notice]));

        $response->assertOk();
        $response->assertJsonFragment(['id' => $notice->id]);
    }

    // ── Cross-tenant isolation ─────────────────────────────────────────────────

    public function test_user_cannot_access_notices_for_lease_in_other_tenant(): void
    {
        $otherTenant = Tenant::create(['name' => 'Other Tenant']);
        $otherLease = Lease::factory()->create(['account_tenant_id' => $otherTenant->id]);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leases.notices.index', $otherLease));

        $response->assertForbidden();
    }
}
