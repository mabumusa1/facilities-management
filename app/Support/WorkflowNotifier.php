<?php

namespace App\Support;

use App\Models\User;
use App\Notifications\WorkflowStatusChangedNotification;

class WorkflowNotifier
{
    public function notifyTenantUsers(
        ?int $tenantId,
        string $module,
        int $resourceId,
        ?string $fromStatus,
        string $toStatus,
        ?string $url = null,
        ?string $actor = null,
    ): void {
        if (! $tenantId) {
            return;
        }

        $users = User::query()
            ->whereHas('accountMemberships', function ($query) use ($tenantId): void {
                $query->where('account_tenant_id', $tenantId);
            })
            ->get();

        foreach ($users as $user) {
            $user->notify(new WorkflowStatusChangedNotification(
                module: $module,
                resourceId: $resourceId,
                fromStatus: $fromStatus,
                toStatus: $toStatus,
                url: $url,
                actor: $actor,
            ));
        }
    }
}
