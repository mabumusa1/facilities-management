<?php

namespace Tests\Feature\Leasing;

use App\Models\AccountMembership;
use App\Models\Lease;
use App\Models\LeaseRenewalOffer;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LeaseRenewalTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    private Lease $lease;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RbacSeeder::class);

        $this->tenant = Tenant::create(['name' => 'Renewal Test Account']);

        $this->user = User::factory()->create();
        AccountMembership::create([
            'user_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);
        $this->user->assignRole('admins');

        $this->seedRenewalStatuses();

        $this->lease = Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'end_date' => now()->addDays(60),
        ]);

        $this->actingAs($this->user);
    }

    /** @return array{tenant_id: int} */
    private function withTenant(): array
    {
        return ['tenant_id' => $this->tenant->id];
    }

    private function seedRenewalStatuses(): void
    {
        $statuses = [
            ['id' => LeaseRenewalOffer::STATUS_DRAFT,    'type' => 'renewal', 'name' => 'draft',    'name_en' => 'Draft',    'name_ar' => 'مسودة'],
            ['id' => LeaseRenewalOffer::STATUS_SENT,     'type' => 'renewal', 'name' => 'sent',     'name_en' => 'Sent',     'name_ar' => 'مرسل'],
            ['id' => LeaseRenewalOffer::STATUS_VIEWED,   'type' => 'renewal', 'name' => 'viewed',   'name_en' => 'Viewed',   'name_ar' => 'تم الاطلاع'],
            ['id' => LeaseRenewalOffer::STATUS_ACCEPTED, 'type' => 'renewal', 'name' => 'accepted', 'name_en' => 'Accepted', 'name_ar' => 'مقبول'],
            ['id' => LeaseRenewalOffer::STATUS_REJECTED, 'type' => 'renewal', 'name' => 'rejected', 'name_en' => 'Rejected', 'name_ar' => 'مرفوض'],
            ['id' => LeaseRenewalOffer::STATUS_EXPIRED,  'type' => 'renewal', 'name' => 'expired',  'name_en' => 'Expired',  'name_ar' => 'منتهي'],
        ];

        foreach ($statuses as $status) {
            DB::table('rf_statuses')->insertOrIgnore($status);
        }
    }

    // ── Renewals index ─────────────────────────────────────────────────────────

    public function test_renewals_index_renders_for_authorised_user(): void
    {
        $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leases.renewals.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('leasing/renewals/Index'));
    }

    // ── Create form ────────────────────────────────────────────────────────────

    public function test_create_renewal_form_renders_with_pre_filled_defaults(): void
    {
        $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leases.renewal.create', $this->lease))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('leasing/renewals/Create')
                ->has('defaults')
                ->has('defaults.new_start_date')
                ->has('defaults.duration_months')
            );
    }

    // ── Store (create offer) ───────────────────────────────────────────────────

    public function test_store_creates_draft_renewal_offer(): void
    {
        $payload = [
            'new_start_date' => now()->addYear()->toDateString(),
            'duration_months' => 12,
            'new_rent_amount' => 90000,
            'valid_until' => now()->addMonths(3)->toDateString(),
            'message_en' => 'We are pleased to offer a renewal.',
            'message_ar' => 'يسعدنا تقديم عرض تجديد.',
        ];

        $this->withSession($this->withTenant())
            ->post(route('leases.renewal.store', $this->lease), $payload)
            ->assertRedirectToRoute('leases.show', $this->lease);

        $this->assertDatabaseHas('lease_renewal_offers', [
            'lease_id' => $this->lease->id,
            'status_id' => LeaseRenewalOffer::STATUS_DRAFT,
            'new_rent_amount' => '90000.00',
            'created_by' => $this->user->id,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->withSession($this->withTenant())
            ->post(route('leases.renewal.store', $this->lease), [])
            ->assertSessionHasErrors(['new_start_date', 'duration_months', 'new_rent_amount', 'valid_until']);
    }

    // ── Send ───────────────────────────────────────────────────────────────────

    public function test_send_transitions_draft_offer_to_sent(): void
    {
        $offer = LeaseRenewalOffer::factory()->create([
            'lease_id' => $this->lease->id,
            'status_id' => LeaseRenewalOffer::STATUS_DRAFT,
            'created_by' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $this->withSession($this->withTenant())
            ->post(route('leases.renewal.send', [$this->lease, $offer]))
            ->assertRedirectToRoute('leases.show', $this->lease);

        $this->assertDatabaseHas('lease_renewal_offers', [
            'id' => $offer->id,
            'status_id' => LeaseRenewalOffer::STATUS_SENT,
        ]);
    }

    public function test_send_returns_403_when_offer_already_sent(): void
    {
        $offer = LeaseRenewalOffer::factory()->sent()->create([
            'lease_id' => $this->lease->id,
            'created_by' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $this->withSession($this->withTenant())
            ->post(route('leases.renewal.send', [$this->lease, $offer]))
            ->assertForbidden();
    }

    // ── Record Decision ────────────────────────────────────────────────────────

    public function test_record_decision_accepted_transitions_status(): void
    {
        $offer = LeaseRenewalOffer::factory()->sent()->create([
            'lease_id' => $this->lease->id,
            'created_by' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $this->withSession($this->withTenant())
            ->post(route('leases.renewal.decision', [$this->lease, $offer]), [
                'decision' => 'accepted',
            ])
            ->assertRedirectToRoute('leases.show', $this->lease);

        $this->assertDatabaseHas('lease_renewal_offers', [
            'id' => $offer->id,
            'status_id' => LeaseRenewalOffer::STATUS_ACCEPTED,
            'decided_by' => $this->user->id,
        ]);
        $this->assertNotNull($offer->fresh()->decided_at);
    }

    public function test_record_decision_declined_transitions_to_rejected_status(): void
    {
        $offer = LeaseRenewalOffer::factory()->sent()->create([
            'lease_id' => $this->lease->id,
            'created_by' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $this->withSession($this->withTenant())
            ->post(route('leases.renewal.decision', [$this->lease, $offer]), [
                'decision' => 'declined',
            ])
            ->assertRedirectToRoute('leases.show', $this->lease);

        $this->assertDatabaseHas('lease_renewal_offers', [
            'id' => $offer->id,
            'status_id' => LeaseRenewalOffer::STATUS_REJECTED,
        ]);
    }

    public function test_record_decision_validates_decision_value(): void
    {
        $offer = LeaseRenewalOffer::factory()->sent()->create([
            'lease_id' => $this->lease->id,
            'created_by' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $this->withSession($this->withTenant())
            ->post(route('leases.renewal.decision', [$this->lease, $offer]), [
                'decision' => 'maybe',
            ])
            ->assertSessionHasErrors('decision');
    }

    // ── Lease show includes renewal data ───────────────────────────────────────

    public function test_lease_show_includes_renewal_offers_count(): void
    {
        LeaseRenewalOffer::factory()->count(2)->create([
            'lease_id' => $this->lease->id,
            'created_by' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leases.show', $this->lease))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('leasing/leases/Show')
                ->where('renewalOffersCount', 2)
            );
    }

    // ── Tenant isolation ───────────────────────────────────────────────────────

    public function test_cannot_interact_with_offer_belonging_to_other_tenant(): void
    {
        $otherTenant = Tenant::create(['name' => 'Other Tenant']);
        $otherLease = Lease::factory()->create([
            'account_tenant_id' => $otherTenant->id,
        ]);
        $otherOffer = LeaseRenewalOffer::factory()->sent()->create([
            'lease_id' => $otherLease->id,
            'created_by' => $this->user->id,
            'account_tenant_id' => $otherTenant->id,
        ]);

        // The lease is in a different tenant — policy denies access via belongsToCurrentTenant
        $this->withSession($this->withTenant())
            ->post(route('leases.renewal.decision', [$otherLease, $otherOffer]), [
                'decision' => 'accepted',
            ])
            ->assertForbidden();
    }

    public function test_contract_type_id_must_reference_a_contract_type_setting(): void
    {
        $wrongTypeSettingId = DB::table('rf_settings')->insertGetId([
            'name' => 'wrong-type',
            'type' => 'payment_frequency',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $payload = [
            'new_start_date' => now()->addYear()->toDateString(),
            'duration_months' => 12,
            'new_rent_amount' => 50000,
            'valid_until' => now()->addMonths(3)->toDateString(),
            'contract_type_id' => $wrongTypeSettingId,
        ];

        $this->withSession($this->withTenant())
            ->post(route('leases.renewal.store', $this->lease), $payload)
            ->assertSessionHasErrors('contract_type_id');
    }

    public function test_cannot_override_account_tenant_id_via_payload(): void
    {
        $otherTenant = Tenant::create(['name' => 'Hijack Tenant']);

        $payload = [
            'new_start_date' => now()->addYear()->toDateString(),
            'duration_months' => 12,
            'new_rent_amount' => 60000,
            'valid_until' => now()->addMonths(3)->toDateString(),
            'account_tenant_id' => $otherTenant->id,
        ];

        $this->withSession($this->withTenant())
            ->post(route('leases.renewal.store', $this->lease), $payload)
            ->assertRedirectToRoute('leases.show', $this->lease);

        $offer = LeaseRenewalOffer::query()
            ->where('lease_id', $this->lease->id)
            ->latest()
            ->first();

        $this->assertNotNull($offer);
        $this->assertSame($this->tenant->id, $offer->account_tenant_id);
    }
}
