<?php

namespace App\Console\Commands;

use App\Models\LeaseQuote;
use Illuminate\Console\Command;

/**
 * Transitions non-terminal lease quotes whose valid_until is in the past to "expired".
 *
 * Non-terminal statuses (type=lease_quote): draft (70), sent (71), viewed (72).
 * Terminal statuses: accepted (73), rejected (74), expired (75).
 *
 * IDs are the reserved rf_statuses primary keys seeded by StatusSeeder.
 * Scheduled daily via routes/console.php.
 */
class ExpireLeaseQuotes extends Command
{
    /** @var int Reserved rf_statuses.id for lease_quote draft */
    public const STATUS_DRAFT = 70;

    /** @var int Reserved rf_statuses.id for lease_quote sent */
    public const STATUS_SENT = 71;

    /** @var int Reserved rf_statuses.id for lease_quote viewed */
    public const STATUS_VIEWED = 72;

    /** @var int Reserved rf_statuses.id for lease_quote accepted (terminal) */
    public const STATUS_ACCEPTED = 73;

    /** @var int Reserved rf_statuses.id for lease_quote rejected (terminal) */
    public const STATUS_REJECTED = 74;

    /** @var int Reserved rf_statuses.id for lease_quote expired (terminal) */
    public const STATUS_EXPIRED = 75;

    /** @var int Reserved rf_statuses.id for a lease application pending KYC/approval */
    public const STATUS_PENDING_APPLICATION = 76;

    protected $signature = 'quotes:expire';

    protected $description = 'Expire lease quotes whose valid_until has passed';

    public function handle(): int
    {
        $nonTerminalIds = [self::STATUS_DRAFT, self::STATUS_SENT, self::STATUS_VIEWED];

        $affected = LeaseQuote::withoutGlobalScopes()
            ->whereIn('status_id', $nonTerminalIds)
            ->where('valid_until', '<', now())
            ->update(['status_id' => self::STATUS_EXPIRED]);

        $this->info(sprintf('Expired %d lease quote(s).', $affected));

        return self::SUCCESS;
    }
}
