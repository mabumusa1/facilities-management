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

    // ── AC1 gap: audit-trail field correctness ────────────────────────────────

    public function test_store_persists_sent_at_timestamp_and_sent_by(): void
    {
        $before = now()->subSecond();

        $this->withSession($this->withTenant())
            ->post(route('leases.notices.store', $this->lease), [
                'type' => LeaseNoticeType::RentIncrease->value,
                'subject_en' => 'Rent Increase Notice',
                'body_en' => 'Your rent will increase next month.',
                'subject_ar' => 'إشعار زيادة الإيجار',
                'body_ar' => 'سيزيد إيجارك الشهر القادم.',
            ]);

        $notice = LeaseNotice::where('lease_id', $this->lease->id)->first();

        $this->assertNotNull($notice);
        $this->assertSame($this->user->id, $notice->sent_by);
        $this->assertNotNull($notice->sent_at);
        $this->assertTrue($notice->sent_at->isAfter($before));
    }

    // ── AC1 gap: validation — invalid enum value ──────────────────────────────

    public function test_store_rejects_invalid_notice_type(): void
    {
        $response = $this->withSession($this->withTenant())
            ->post(route('leases.notices.store', $this->lease), [
                'type' => 'unknown_type',
                'subject_en' => 'Hello',
                'body_en' => 'Body text',
                'subject_ar' => 'مرحبا',
                'body_ar' => 'نص',
            ]);

        $response->assertSessionHasErrors('type');
        $this->assertDatabaseCount('lease_notices', 0);
    }

    // ── AC1 gap: validation — subject_en exceeds max:255 ─────────────────────

    public function test_store_rejects_subject_en_over_255_characters(): void
    {
        $response = $this->withSession($this->withTenant())
            ->post(route('leases.notices.store', $this->lease), [
                'type' => LeaseNoticeType::FreeForm->value,
                'subject_en' => str_repeat('a', 256),
                'body_en' => 'Body text',
                'subject_ar' => 'موضوع',
                'body_ar' => 'نص',
            ]);

        $response->assertSessionHasErrors('subject_en');
        $this->assertDatabaseCount('lease_notices', 0);
    }

    // ── AC1 gap: validation — subject_ar exceeds max:255 ─────────────────────

    public function test_store_rejects_subject_ar_over_255_characters(): void
    {
        $response = $this->withSession($this->withTenant())
            ->post(route('leases.notices.store', $this->lease), [
                'type' => LeaseNoticeType::FreeForm->value,
                'subject_en' => 'Subject',
                'body_en' => 'Body text',
                'subject_ar' => str_repeat('أ', 256),
                'body_ar' => 'نص',
            ]);

        $response->assertSessionHasErrors('subject_ar');
        $this->assertDatabaseCount('lease_notices', 0);
    }

    // ── AC1 gap: all four enum types are accepted ─────────────────────────────

    public function test_store_accepts_all_four_notice_types(): void
    {
        foreach (LeaseNoticeType::cases() as $type) {
            $response = $this->withSession($this->withTenant())
                ->post(route('leases.notices.store', $this->lease), [
                    'type' => $type->value,
                    'subject_en' => 'Subject for '.$type->value,
                    'body_en' => 'Body for '.$type->value,
                    'subject_ar' => 'موضوع',
                    'body_ar' => 'نص',
                ]);

            $response->assertRedirect(route('leases.notices.index', $this->lease));
        }

        $this->assertDatabaseCount('lease_notices', count(LeaseNoticeType::cases()));
    }

    // ── AC1 gap: idempotency — re-send creates a new row each time ────────────

    public function test_re_sending_a_notice_creates_a_new_row_not_overwrite(): void
    {
        $payload = [
            'type' => LeaseNoticeType::RenewalOffer->value,
            'subject_en' => 'Renewal Offer',
            'body_en' => 'We would like to offer you a lease renewal.',
            'subject_ar' => 'عرض تجديد',
            'body_ar' => 'نود تقديم عرض لتجديد عقد الإيجار.',
        ];

        $this->withSession($this->withTenant())
            ->post(route('leases.notices.store', $this->lease), $payload);

        $this->withSession($this->withTenant())
            ->post(route('leases.notices.store', $this->lease), $payload);

        $this->assertDatabaseCount('lease_notices', 2);
    }

    // ── AC1 gap: flash toast message on success ───────────────────────────────

    public function test_store_sets_toast_flash_with_tenant_email(): void
    {
        $response = $this->withSession($this->withTenant())
            ->post(route('leases.notices.store', $this->lease), [
                'type' => LeaseNoticeType::MoveOutReminder->value,
                'subject_en' => 'Move-Out Reminder',
                'body_en' => 'Please begin preparing for move-out.',
                'subject_ar' => 'تذكير بالإخلاء',
                'body_ar' => 'يرجى البدء بالتحضير للإخلاء.',
            ]);

        $response->assertSessionHas('toast');

        $toast = $response->getSession()->get('toast');
        $this->assertStringContainsString('tenant@example.com', $toast);
    }

    // ── AC1 gap: Arabic RTL content stores correctly ──────────────────────────

    public function test_store_persists_arabic_subject_and_body_correctly(): void
    {
        $arabicSubject = 'إشعار زيادة الإيجار — وحدة ب-301';
        $arabicBody = "السيد أحمد الفارسي،\n\nيقترب موعد انتهاء عقد الإيجار للوحدة ب-301 بتاريخ 2027-05-31.";

        $this->withSession($this->withTenant())
            ->post(route('leases.notices.store', $this->lease), [
                'type' => LeaseNoticeType::MoveOutReminder->value,
                'subject_en' => 'Move-Out Reminder — Unit B-301',
                'body_en' => 'Your lease for Unit B-301 is approaching its end date.',
                'subject_ar' => $arabicSubject,
                'body_ar' => $arabicBody,
            ]);

        $notice = LeaseNotice::where('lease_id', $this->lease->id)->first();

        $this->assertNotNull($notice);
        $this->assertSame($arabicSubject, $notice->subject_ar);
        $this->assertSame($arabicBody, $notice->body_ar);
    }

    // ── AC1 gap: empty-string email also triggers missing-email guard ─────────

    public function test_store_returns_error_when_tenant_email_is_empty_string(): void
    {
        $this->resident->update(['email' => '']);

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

    // ── AC2 gap: index page props ─────────────────────────────────────────────

    public function test_notices_index_passes_required_props_to_inertia(): void
    {
        LeaseNotice::factory()->create([
            'lease_id' => $this->lease->id,
            'tenant_id' => $this->resident->id,
            'sent_by' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leases.notices.index', $this->lease));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page->component('leasing/leases/Notices')
                ->has('lease')
                ->has('tenant')
                ->has('notices')
                ->has('noticeTypes')
        );
    }

    // ── AC2 gap: notices scoped to their own lease ────────────────────────────

    public function test_index_does_not_include_notices_from_other_leases(): void
    {
        $otherResident = Resident::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'email' => 'other@example.com',
        ]);
        $otherLease = Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'tenant_id' => $otherResident->id,
        ]);

        // Notice for the other lease — must not appear.
        LeaseNotice::factory()->create([
            'lease_id' => $otherLease->id,
            'tenant_id' => $otherResident->id,
            'sent_by' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        // Notice for the current lease — must appear.
        LeaseNotice::factory()->create([
            'lease_id' => $this->lease->id,
            'tenant_id' => $this->resident->id,
            'sent_by' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leases.notices.index', $this->lease));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page->component('leasing/leases/Notices')
                ->has('notices.data', 1)
        );
    }

    // ── AC2 gap: notices returned in descending sent_at order ─────────────────

    public function test_index_returns_notices_in_descending_sent_at_order(): void
    {
        $first = LeaseNotice::factory()->create([
            'lease_id' => $this->lease->id,
            'tenant_id' => $this->resident->id,
            'sent_by' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'sent_at' => now()->subDays(10),
        ]);

        $second = LeaseNotice::factory()->create([
            'lease_id' => $this->lease->id,
            'tenant_id' => $this->resident->id,
            'sent_by' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'sent_at' => now()->subDays(2),
        ]);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leases.notices.index', $this->lease));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page->component('leasing/leases/Notices')
                ->where('notices.data.0.id', $second->id)
                ->where('notices.data.1.id', $first->id)
        );
    }

    // ── AC2 gap: show returns all notice body fields ──────────────────────────

    public function test_show_returns_all_bilingual_fields(): void
    {
        $notice = LeaseNotice::factory()->create([
            'lease_id' => $this->lease->id,
            'tenant_id' => $this->resident->id,
            'sent_by' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'subject_en' => 'English Subject',
            'body_en' => 'English Body',
            'subject_ar' => 'الموضوع بالعربية',
            'body_ar' => 'المحتوى بالعربية',
        ]);

        $response = $this->withSession($this->withTenant())
            ->getJson(route('leases.notices.show', [$this->lease, $notice]));

        $response->assertOk();
        $response->assertJsonFragment([
            'id' => $notice->id,
            'subject_en' => 'English Subject',
            'body_en' => 'English Body',
            'subject_ar' => 'الموضوع بالعربية',
            'body_ar' => 'المحتوى بالعربية',
        ]);
    }

    // ── Auth failure paths ────────────────────────────────────────────────────

    public function test_unauthenticated_user_is_redirected_from_notices_index(): void
    {
        auth()->logout();

        $response = $this->withoutVite()
            ->get(route('leases.notices.index', $this->lease));

        $response->assertRedirect(route('login'));
    }

    public function test_unauthenticated_user_cannot_post_a_notice(): void
    {
        auth()->logout();

        $response = $this->post(route('leases.notices.store', $this->lease), [
            'type' => LeaseNoticeType::FreeForm->value,
            'subject_en' => 'Hello',
            'body_en' => 'Body',
            'subject_ar' => 'مرحبا',
            'body_ar' => 'نص',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('lease_notices', 0);
    }

    // ── RBAC: cross-tenant store denial ──────────────────────────────────────

    public function test_admin_from_other_tenant_cannot_post_notice_to_this_lease(): void
    {
        $tenantB = Tenant::create(['name' => 'Tenant B Notices']);
        $adminB = User::factory()->create();
        AccountMembership::create([
            'user_id' => $adminB->id,
            'account_tenant_id' => $tenantB->id,
            'role' => 'account_admins',
        ]);
        $adminB->assignRole('admins');

        $response = $this->actingAs($adminB)
            ->withSession(['tenant_id' => $tenantB->id])
            ->post(route('leases.notices.store', $this->lease), [
                'type' => LeaseNoticeType::FreeForm->value,
                'subject_en' => 'Cross-tenant hello',
                'body_en' => 'Cross-tenant body',
                'subject_ar' => 'مرحبا',
                'body_ar' => 'نص',
            ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('lease_notices', 0);
    }

    // ── RBAC: show denial for cross-tenant notice ─────────────────────────────

    public function test_show_returns_403_for_notice_in_other_tenant(): void
    {
        $tenantB = Tenant::create(['name' => 'Tenant B Show']);
        $residentB = Resident::factory()->create([
            'account_tenant_id' => $tenantB->id,
            'email' => 'b@example.com',
        ]);
        $leaseB = Lease::factory()->create([
            'account_tenant_id' => $tenantB->id,
            'tenant_id' => $residentB->id,
        ]);
        $adminB = User::factory()->create();
        AccountMembership::create([
            'user_id' => $adminB->id,
            'account_tenant_id' => $tenantB->id,
            'role' => 'account_admins',
        ]);
        $adminB->assignRole('admins');

        // Notice scoped to tenant B.
        $noticeB = LeaseNotice::factory()->create([
            'lease_id' => $leaseB->id,
            'tenant_id' => $residentB->id,
            'sent_by' => $adminB->id,
            'account_tenant_id' => $tenantB->id,
        ]);

        // Tenant A admin tries to read tenant B's notice.
        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->getJson(route('leases.notices.show', [$leaseB, $noticeB]));

        $response->assertForbidden();
    }

    // ── Show page: noticesCount prop ──────────────────────────────────────────

    public function test_lease_show_page_includes_notices_count_prop(): void
    {
        LeaseNotice::factory()->count(3)->create([
            'lease_id' => $this->lease->id,
            'tenant_id' => $this->resident->id,
            'sent_by' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leases.show', $this->lease));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page->component('leasing/leases/Show')
                ->where('noticesCount', 3)
        );
    }

    public function test_lease_show_page_notices_count_is_zero_when_no_notices_sent(): void
    {
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leases.show', $this->lease));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page->component('leasing/leases/Show')
                ->where('noticesCount', 0)
        );
    }
}
