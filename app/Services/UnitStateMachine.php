<?php

namespace App\Services;

use App\Enums\UnitStatus;
use App\Models\Unit;
use App\Models\UnitStatusHistory;
use App\Models\User;
use InvalidArgumentException;

class UnitStateMachine
{
    public function transition(Unit $unit, UnitStatus $target, ?User $actor = null, ?string $reason = null): UnitStatusHistory
    {
        $current = $unit->status
            ? UnitStatus::tryFrom($unit->status)
            : null;

        if ($current !== null && ! $current->canTransitionTo($target)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid transition: "%s" → "%s".',
                $current->value,
                $target->value
            ));
        }

        $unit->update(['status' => $target->value]);

        return UnitStatusHistory::create([
            'account_tenant_id' => $unit->account_tenant_id,
            'unit_id' => $unit->id,
            'from_status' => $current?->value,
            'to_status' => $target->value,
            'changed_by' => $actor?->id,
            'reason' => $reason,
        ]);
    }

    /**
     * Automated transition — lease activation sets unit to occupied.
     */
    public function markOccupied(Unit $unit, ?string $reason = null): UnitStatusHistory
    {
        return $this->transition($unit, UnitStatus::Occupied, null, $reason ?? __('Lease activated — auto transition.'));
    }
}
