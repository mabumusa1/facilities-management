<?php

namespace App\Console\Commands;

use App\Models\VisitorInvitation;
use Illuminate\Console\Command;

/**
 * Transitions active visitor invitations whose valid_until is in the past to "expired".
 *
 * Only "active" invitations are transitioned — "used", "cancelled", and already
 * "expired" rows are left untouched.
 *
 * Scheduled daily via routes/console.php.
 */
class ExpireVisitorInvitations extends Command
{
    protected $signature = 'visitor-access:expire-invitations';

    protected $description = 'Expire visitor invitations whose valid_until has passed';

    public function handle(): int
    {
        $affected = VisitorInvitation::withoutGlobalScopes()
            ->where('status', 'active')
            ->where('valid_until', '<', now())
            ->update(['status' => 'expired']);

        $this->info(sprintf('Expired %d visitor invitation(s).', $affected));

        return self::SUCCESS;
    }
}
