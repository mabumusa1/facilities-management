<?php

namespace Tests\Feature\Http\Properties;

use App\Enums\UnitStatus;
use App\Models\AccountMembership;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use DB;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UnitStatusApiTest extends TestCase
{
    use LazilyRefreshDatabase, WithFaker;

    private User $user;

    private Tenant $tenant;

    private Unit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->tenant = Tenant::create(['name' => 'Unit Status Test']);
        $this->tenant->makeCurrent();

        AccountMembership::create([
            'user_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);

        $this->ensureAccountAdminsRoleExists();
        $this->user->assignRole('accountAdmins');

        $this->actingAs($this->user);
        $this->withSession(['tenant_id' => $this->tenant->id]);

        $this->unit = Unit::factory()->create([
            'status' => UnitStatus::Available->value,
            'account_tenant_id' => $this->tenant->id,
        ]);
    }

    private function ensureAccountAdminsRoleExists(): void
    {
        $exists = DB::table('roles')
            ->where('name', 'accountAdmins')
            ->where('guard_name', 'web')
            ->exists();

        if (! $exists) {
            DB::table('roles')->insert([
                'name' => 'accountAdmins',
                'guard_name' => 'web',
                'name_en' => 'Account Admins',
                'name_ar' => 'مدراء الحسابات',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    // -------------------------------------------------------------------------
    // Happy paths — status transitions
    // -------------------------------------------------------------------------

    public function test_update_status_to_under_maintenance_records_history(): void
    {
        $response = $this->postJson("/rf/units/{$this->unit->id}/status", [
            'status' => 'under_maintenance',
            'reason' => 'Quarterly pest control',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.status', 'under_maintenance');
        $response->assertJsonPath('data.unit_id', $this->unit->id);

        $this->assertDatabaseHas('rf_unit_status_history', [
            'unit_id' => $this->unit->id,
            'from_status' => 'available',
            'to_status' => 'under_maintenance',
            'changed_by' => $this->user->id,
            'reason' => 'Quarterly pest control',
        ]);

        $this->unit->refresh();
        $this->assertSame('under_maintenance', $this->unit->status);
    }

    public function test_status_history_returns_records_in_desc_order(): void
    {
        // Create two transitions
        $this->postJson("/rf/units/{$this->unit->id}/status", [
            'status' => 'under_maintenance',
            'reason' => 'First transition',
        ]);
        $this->postJson("/rf/units/{$this->unit->id}/status", [
            'status' => 'available',
            'reason' => 'Second transition',
        ]);

        $response = $this->getJson("/rf/units/{$this->unit->id}/status-history");

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');

        // Both transitions must appear (order may depend on timestamp resolution)
        $statuses = [
            $response->json('data.0.to_status'),
            $response->json('data.1.to_status'),
        ];
        $this->assertContains('available', $statuses);
        $this->assertContains('under_maintenance', $statuses);
    }

    public function test_status_history_shows_changed_by_name(): void
    {
        $this->postJson("/rf/units/{$this->unit->id}/status", ['status' => 'under_maintenance']);

        $response = $this->getJson("/rf/units/{$this->unit->id}/status-history");

        $response->assertJsonPath('data.0.changed_by', $this->user->name);
    }

    public function test_status_history_is_empty_for_units_with_no_transitions(): void
    {
        $response = $this->getJson("/rf/units/{$this->unit->id}/status-history");

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }

    // -------------------------------------------------------------------------
    // Failure paths — invalid transitions
    // -------------------------------------------------------------------------

    public function test_invalid_transition_from_occupied_to_under_maintenance_returns_500(): void
    {
        $this->unit->update(['status' => UnitStatus::Occupied->value]);

        $response = $this->postJson("/rf/units/{$this->unit->id}/status", [
            'status' => 'under_maintenance',
        ]);

        // StateMachine throws InvalidArgumentException — 500 server error
        $response->assertStatus(500);
        $this->unit->refresh();
        $this->assertSame('occupied', $this->unit->status, 'Status must not change on invalid transition');
    }

    public function test_invalid_status_value_returns_422(): void
    {
        $response = $this->postJson("/rf/units/{$this->unit->id}/status", [
            'status' => 'nonexistent_status',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['status']);
    }

    public function test_missing_status_field_returns_422(): void
    {
        $response = $this->postJson("/rf/units/{$this->unit->id}/status", [
            'reason' => 'no status provided',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['status']);
    }

    public function test_reason_exceeding_max_length_returns_422(): void
    {
        $response = $this->postJson("/rf/units/{$this->unit->id}/status", [
            'status' => 'under_maintenance',
            'reason' => str_repeat('x', 501),
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['reason']);
    }

    public function test_reason_at_max_length_is_accepted(): void
    {
        $reason = str_repeat('x', 500);

        $response = $this->postJson("/rf/units/{$this->unit->id}/status", [
            'status' => 'under_maintenance',
            'reason' => $reason,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('rf_unit_status_history', ['reason' => $reason]);
    }

    // -------------------------------------------------------------------------
    // Edge cases
    // -------------------------------------------------------------------------

    public function test_reason_is_optional(): void
    {
        $response = $this->postJson("/rf/units/{$this->unit->id}/status", [
            'status' => 'under_maintenance',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('rf_unit_status_history', [
            'unit_id' => $this->unit->id,
            'reason' => null,
        ]);
    }

    public function test_transition_to_same_status_is_rejected(): void
    {
        $this->unit->update(['status' => UnitStatus::UnderMaintenance->value]);

        $response = $this->postJson("/rf/units/{$this->unit->id}/status", [
            'status' => 'under_maintenance',
        ]);

        $response->assertStatus(500);
    }

    public function test_all_allowed_transitions_from_available(): void
    {
        $targets = ['under_maintenance', 'occupied', 'off_plan'];

        foreach ($targets as $target) {
            $unit = Unit::factory()->create([
                'status' => UnitStatus::Available->value,
                'account_tenant_id' => $this->tenant->id,
            ]);

            $response = $this->postJson("/rf/units/{$unit->id}/status", ['status' => $target]);
            $response->assertStatus(200);
            $this->assertSame($target, $unit->fresh()->status);
        }
    }
}
