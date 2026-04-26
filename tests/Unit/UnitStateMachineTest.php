<?php

namespace Tests\Unit;

use App\Enums\UnitStatus;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use App\Services\UnitStateMachine;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class UnitStateMachineTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create(['name' => 'Unit SM Test']);
        $this->tenant->makeCurrent();
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    public function test_valid_transition_available_to_under_maintenance(): void
    {
        $unit = Unit::factory()->create(['status' => UnitStatus::Available->value]);

        $machine = app(UnitStateMachine::class);
        $user = User::factory()->create();
        $history = $machine->transition($unit, UnitStatus::UnderMaintenance, $user, 'Scheduled maintenance');

        $unit->refresh();

        $this->assertSame(UnitStatus::UnderMaintenance->value, $unit->status);
        $this->assertSame(UnitStatus::Available->value, $history->from_status);
        $this->assertSame(UnitStatus::UnderMaintenance->value, $history->to_status);
        $this->assertSame($user->id, $history->changed_by);
        $this->assertSame('Scheduled maintenance', $history->reason);
    }

    public function test_invalid_transition_occupied_to_under_maintenance_throws(): void
    {
        $unit = Unit::factory()->create(['status' => UnitStatus::Occupied->value]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid transition: "occupied"');

        $machine = app(UnitStateMachine::class);
        $machine->transition($unit, UnitStatus::UnderMaintenance);
    }

    public function test_status_history_is_recorded(): void
    {
        $unit = Unit::factory()->create(['status' => UnitStatus::Available->value]);

        $machine = app(UnitStateMachine::class);
        $machine->transition($unit, UnitStatus::UnderMaintenance, null, 'First');
        $machine->transition($unit, UnitStatus::Available, null, 'Second');

        $this->assertSame(2, $unit->statusHistory()->count());
    }

    public function test_mark_occupied_auto_transition(): void
    {
        $unit = Unit::factory()->create(['status' => UnitStatus::Available->value]);

        $machine = app(UnitStateMachine::class);
        $history = $machine->markOccupied($unit);

        $this->assertSame(UnitStatus::Occupied->value, $unit->fresh()->status);
        $this->assertStringContainsString('auto transition', $history->reason);
    }

    public function test_enum_allowed_transitions(): void
    {
        $this->assertTrue(UnitStatus::Available->canTransitionTo(UnitStatus::UnderMaintenance));
        $this->assertTrue(UnitStatus::Available->canTransitionTo(UnitStatus::Occupied));
        $this->assertTrue(UnitStatus::UnderMaintenance->canTransitionTo(UnitStatus::Available));
        $this->assertFalse(UnitStatus::Occupied->canTransitionTo(UnitStatus::UnderMaintenance));
        $this->assertFalse(UnitStatus::UnderMaintenance->canTransitionTo(UnitStatus::Occupied));
    }
}
