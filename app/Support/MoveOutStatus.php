<?php

namespace App\Support;

/**
 * Status IDs for the move_out domain — mirrors the fix migration
 * (2026_04_29_212530_fix_move_out_status_id_collision).
 *
 * IDs 77-79 were reserved by the lease-approval workflow
 * (ExpireLeaseQuotes::STATUS_APPROVED_APPLICATION = 77,
 *  ExpireLeaseQuotes::STATUS_REJECTED_APPLICATION = 78).
 * Move-out statuses therefore start at 80.
 *
 * Lease status: 80 (move_out_in_progress)
 * MoveOut record statuses: 81 (in_progress), 82 (completed)
 */
class MoveOutStatus
{
    /** Lease transitions to this status when a move-out is initiated. */
    public const int LEASE_MOVE_OUT_IN_PROGRESS = 80;

    /** MoveOut record initial status. */
    public const int IN_PROGRESS = 81;

    /** MoveOut record status when inspection + deductions are complete. */
    public const int COMPLETED = 82;
}
