<?php

namespace Tests\Feature\Leasing;

use App\Enums\DeductionReason;
use App\Enums\InspectionCondition;
use App\Enums\MoveOutReason;
use App\Enums\UnitStatus;
use App\Models\AccountMembership;
use App\Models\Lease;
use App\Models\MoveOut;
use App\Models\MoveOutDeduction;
use App\Models\MoveOutRoom;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use App\Support\MoveOutStatus;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MoveOutTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    private Lease $lease;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RbacSeeder::class);

        $this->tenant = Tenant::create(['name' => 'MoveOut Test Account']);

        $this->user = User::factory()->create();
        AccountMembership::create([
            'user_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);
        $this->user->assignRole('admins');

        // Move-out status rows are seeded by migrations (IDs 80-82 after the fix-collision
        // migration). Use insertOrIgnore so setUp is idempotent — migrations already insert
        // these rows via upsert, so we skip on conflict.
        DB::table('rf_statuses')->insertOrIgnore([
            ['id' => MoveOutStatus::LEASE_MOVE_OUT_IN_PROGRESS, 'name' => 'Move-Out In Progress', 'name_en' => 'move_out_in_progress', 'name_ar' => 'إخلاء جاري', 'priority' => 5, 'type' => 'lease'],
            ['id' => MoveOutStatus::IN_PROGRESS, 'name' => 'In Progress', 'name_en' => 'in_progress', 'name_ar' => 'جاري', 'priority' => 1, 'type' => 'move_out'],
            ['id' => MoveOutStatus::COMPLETED, 'name' => 'Completed', 'name_en' => 'completed', 'name_ar' => 'مكتمل', 'priority' => 2, 'type' => 'move_out'],
        ]);

        $this->lease = Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'security_deposit_amount' => 8500.00,
        ]);

        $this->actingAs($this->user);
    }

    private function withTenant(): array
    {
        return ['tenant_id' => $this->tenant->id];
    }

    // ── Initiate page ────────────────────────────────────────────────────────

    public function test_initiate_page_renders_for_authorised_user(): void
    {
        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('rf.leases.move-out.initiate', $this->lease));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('leasing/move-outs/Initiate')
            ->has('lease')
            ->has('reasons')
        );
    }

    // ── Store (initiate move-out) ────────────────────────────────────────────

    public function test_store_creates_move_out_record_and_transitions_statuses(): void
    {
        $unit = Unit::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status' => UnitStatus::Occupied->value,
        ]);
        $this->lease->units()->attach($unit);

        $response = $this->withSession($this->withTenant())
            ->post(route('rf.leases.move-out.store', $this->lease), [
                'move_out_date' => '2027-05-31',
                'reason' => MoveOutReason::EndOfLease->value,
                'notes' => 'Planned move-out.',
            ]);

        $moveOut = MoveOut::where('lease_id', $this->lease->id)->firstOrFail();

        $response->assertRedirectToRoute('rf.leases.move-out.inspection', [
            'lease' => $this->lease->id,
            'moveOut' => $moveOut->id,
        ]);

        $this->assertDatabaseHas('move_outs', [
            'lease_id' => $this->lease->id,
            'reason' => MoveOutReason::EndOfLease->value,
            'status_id' => MoveOutStatus::IN_PROGRESS,
            'account_tenant_id' => $this->tenant->id,
        ]);

        // Lease status should transition to move-out-in-progress.
        $this->lease->refresh();
        $this->assertEquals(MoveOutStatus::LEASE_MOVE_OUT_IN_PROGRESS, $this->lease->status_id);
        $this->assertTrue($this->lease->is_move_out);

        // Unit should become under-maintenance.
        $unit->refresh();
        $this->assertEquals(UnitStatus::UnderMaintenance->value, $unit->status);
    }

    // ── Inspection page ──────────────────────────────────────────────────────

    public function test_inspection_page_renders_with_rooms(): void
    {
        $moveOut = MoveOut::factory()->create([
            'lease_id' => $this->lease->id,
            'account_tenant_id' => $this->tenant->id,
            'status_id' => MoveOutStatus::IN_PROGRESS,
            'initiated_by' => $this->user->id,
        ]);

        MoveOutRoom::factory()->create([
            'move_out_id' => $moveOut->id,
            'name' => 'Living Room',
            'condition' => InspectionCondition::Good,
            'sort_order' => 0,
        ]);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('rf.leases.move-out.inspection', [
                'lease' => $this->lease,
                'moveOut' => $moveOut,
            ]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('leasing/move-outs/Inspection')
            ->has('moveOut.rooms', 1)
        );
    }

    // ── Save inspection ──────────────────────────────────────────────────────

    public function test_save_inspection_upserts_rooms(): void
    {
        $moveOut = MoveOut::factory()->create([
            'lease_id' => $this->lease->id,
            'account_tenant_id' => $this->tenant->id,
            'status_id' => MoveOutStatus::IN_PROGRESS,
            'initiated_by' => $this->user->id,
        ]);

        $response = $this->withSession($this->withTenant())
            ->post(route('rf.leases.move-out.inspection.save', [
                'lease' => $this->lease,
                'moveOut' => $moveOut,
            ]), [
                'rooms' => [
                    [
                        'id' => null,
                        'name' => 'Living Room',
                        'condition' => InspectionCondition::Good->value,
                        'notes' => 'Minor scuff marks.',
                        'sort_order' => 0,
                    ],
                    [
                        'id' => null,
                        'name' => 'Kitchen',
                        'condition' => InspectionCondition::Fair->value,
                        'notes' => 'Grease marks on backsplash.',
                        'sort_order' => 1,
                    ],
                ],
            ]);

        $response->assertRedirectToRoute('rf.leases.move-out.inspection', [
            'lease' => $this->lease->id,
            'moveOut' => $moveOut->id,
        ]);

        $this->assertDatabaseCount('move_out_rooms', 2);
        $this->assertDatabaseHas('move_out_rooms', [
            'move_out_id' => $moveOut->id,
            'name' => 'Living Room',
            'condition' => InspectionCondition::Good->value,
        ]);
    }

    public function test_save_inspection_removes_deleted_rooms(): void
    {
        $moveOut = MoveOut::factory()->create([
            'lease_id' => $this->lease->id,
            'account_tenant_id' => $this->tenant->id,
            'status_id' => MoveOutStatus::IN_PROGRESS,
            'initiated_by' => $this->user->id,
        ]);

        $existingRoom = MoveOutRoom::factory()->create([
            'move_out_id' => $moveOut->id,
            'name' => 'Bedroom 1',
            'condition' => InspectionCondition::Excellent,
            'sort_order' => 0,
        ]);

        // Submit with only a new room — existing room should be deleted.
        $this->withSession($this->withTenant())
            ->post(route('rf.leases.move-out.inspection.save', [
                'lease' => $this->lease,
                'moveOut' => $moveOut,
            ]), [
                'rooms' => [
                    [
                        'id' => null,
                        'name' => 'Kitchen',
                        'condition' => InspectionCondition::Good->value,
                        'notes' => null,
                        'sort_order' => 0,
                    ],
                ],
            ]);

        $this->assertModelMissing($existingRoom);
        $this->assertDatabaseCount('move_out_rooms', 1);
    }

    // ── Deductions page ──────────────────────────────────────────────────────

    public function test_deductions_page_renders_with_summary(): void
    {
        $moveOut = MoveOut::factory()->create([
            'lease_id' => $this->lease->id,
            'account_tenant_id' => $this->tenant->id,
            'status_id' => MoveOutStatus::IN_PROGRESS,
            'initiated_by' => $this->user->id,
        ]);

        MoveOutDeduction::factory()->create([
            'move_out_id' => $moveOut->id,
            'label_en' => 'Wall paint',
            'label_ar' => 'طلاء جدار',
            'amount' => 1200.00,
            'reason' => DeductionReason::Damage,
        ]);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('rf.leases.move-out.deductions', [
                'lease' => $this->lease,
                'moveOut' => $moveOut,
            ]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('leasing/move-outs/Deductions')
            ->has('moveOut.deductions', 1)
            ->where('moveOut.summary.security_deposit', 8500)
            ->where('moveOut.summary.total_deductions', 1200)
            ->where('moveOut.summary.exceeds_deposit', false)
        );
    }

    // ── Save deductions ──────────────────────────────────────────────────────

    public function test_save_deductions_syncs_and_redirects(): void
    {
        $moveOut = MoveOut::factory()->create([
            'lease_id' => $this->lease->id,
            'account_tenant_id' => $this->tenant->id,
            'status_id' => MoveOutStatus::IN_PROGRESS,
            'initiated_by' => $this->user->id,
        ]);

        $response = $this->withSession($this->withTenant())
            ->post(route('rf.leases.move-out.deductions.save', [
                'lease' => $this->lease,
                'moveOut' => $moveOut,
            ]), [
                'deductions' => [
                    [
                        'id' => null,
                        'label_en' => 'Cabinet repair',
                        'label_ar' => 'إصلاح خزانة',
                        'amount' => 350.00,
                        'reason' => DeductionReason::Damage->value,
                    ],
                    [
                        'id' => null,
                        'label_en' => 'Deep clean',
                        'label_ar' => 'تنظيف عميق',
                        'amount' => 500.00,
                        'reason' => DeductionReason::Cleaning->value,
                    ],
                ],
            ]);

        $response->assertRedirectToRoute('rf.leases.move-out.deductions', [
            'lease' => $this->lease->id,
            'moveOut' => $moveOut->id,
        ]);

        $this->assertDatabaseCount('move_out_deductions', 2);
        $this->assertDatabaseHas('move_out_deductions', [
            'move_out_id' => $moveOut->id,
            'label_en' => 'Cabinet repair',
            'amount' => 350.00,
        ]);
    }

    public function test_save_deductions_removes_deleted_deductions(): void
    {
        $moveOut = MoveOut::factory()->create([
            'lease_id' => $this->lease->id,
            'account_tenant_id' => $this->tenant->id,
            'status_id' => MoveOutStatus::IN_PROGRESS,
            'initiated_by' => $this->user->id,
        ]);

        $existing = MoveOutDeduction::factory()->create([
            'move_out_id' => $moveOut->id,
            'label_en' => 'Old deduction',
            'label_ar' => 'خصم قديم',
            'amount' => 200.00,
            'reason' => DeductionReason::Utility,
        ]);

        // Submit with an empty deductions array — existing should be removed.
        $this->withSession($this->withTenant())
            ->post(route('rf.leases.move-out.deductions.save', [
                'lease' => $this->lease,
                'moveOut' => $moveOut,
            ]), [
                'deductions' => [],
            ]);

        $this->assertModelMissing($existing);
        $this->assertDatabaseCount('move_out_deductions', 0);
    }

    public function test_save_deductions_allows_exceeding_security_deposit(): void
    {
        $moveOut = MoveOut::factory()->create([
            'lease_id' => $this->lease->id,
            'account_tenant_id' => $this->tenant->id,
            'status_id' => MoveOutStatus::IN_PROGRESS,
            'initiated_by' => $this->user->id,
        ]);

        // Total deductions (12 000) > security deposit (8 500) — still succeeds.
        $response = $this->withSession($this->withTenant())
            ->post(route('rf.leases.move-out.deductions.save', [
                'lease' => $this->lease,
                'moveOut' => $moveOut,
            ]), [
                'deductions' => [
                    [
                        'id' => null,
                        'label_en' => 'Major damage',
                        'label_ar' => 'أضرار كبيرة',
                        'amount' => 12000.00,
                        'reason' => DeductionReason::Damage->value,
                    ],
                ],
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('move_out_deductions', [
            'move_out_id' => $moveOut->id,
            'amount' => 12000.00,
        ]);
    }

    // ── Lease Show: activeMoveOut prop ───────────────────────────────────────

    public function test_lease_show_exposes_active_move_out(): void
    {
        $moveOut = MoveOut::factory()->create([
            'lease_id' => $this->lease->id,
            'account_tenant_id' => $this->tenant->id,
            'status_id' => MoveOutStatus::IN_PROGRESS,
            'initiated_by' => $this->user->id,
            'move_out_date' => '2027-05-31',
        ]);

        $response = $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leases.show', $this->lease));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('activeMoveOut.id', $moveOut->id)
            ->where('activeMoveOut.status_id', MoveOutStatus::IN_PROGRESS)
        );
    }
}
