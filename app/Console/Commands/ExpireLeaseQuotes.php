<?php

namespace App\Console\Commands;

use App\Models\LeaseQuote;
use App\Models\Status;
use Illuminate\Console\Command;

/**
 * Transitions non-terminal lease quotes whose valid_until is in the past to "expired".
 *
 * Non-terminal statuses (type=lease_quote): draft, sent, viewed.
 * Terminal statuses: accepted, rejected, expired.
 *
 * Scheduled daily via routes/console.php.
 */
class ExpireLeaseQuotes extends Command
{
    protected $signature = 'quotes:expire';

    protected $description = 'Expire lease quotes whose valid_until has passed';

    public function handle(): int
    {
        $expiredStatus = Status::where('type', 'lease_quote')
            ->where('name_en', 'expired')
            ->first();

        if ($expiredStatus === null) {
            $this->error('Expired status for type=lease_quote not found. Run LeaseQuoteStatusSeeder first.');

            return self::FAILURE;
        }

        $nonTerminalStatuses = Status::where('type', 'lease_quote')
            ->whereIn('name_en', ['draft', 'sent', 'viewed'])
            ->pluck('id');

        $affected = LeaseQuote::withoutGlobalScopes()
            ->whereIn('status_id', $nonTerminalStatuses)
            ->where('valid_until', '<', now())
            ->update(['status_id' => $expiredStatus->id]);

        $this->info(sprintf('Expired %d lease quote(s).', $affected));

        return self::SUCCESS;
    }
}
