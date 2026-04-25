<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\LeaseQuote;
use App\Models\User;
use App\Support\ManagerScopeHelper;

class LeaseQuotePolicy
{
    use ChecksTenantOwnership;

    public function viewAny(User $user): bool
    {
        return $user->can('leases.VIEW');
    }

    public function view(User $user, LeaseQuote $leaseQuote): bool
    {
        return $user->can('leases.VIEW')
            && $this->belongsToCurrentTenant($leaseQuote)
            && ManagerScopeHelper::userCanAccessModel($user, $leaseQuote);
    }

    public function create(User $user): bool
    {
        return $user->can('leases.CREATE');
    }

    public function update(User $user, LeaseQuote $leaseQuote): bool
    {
        return $user->can('leases.UPDATE')
            && $this->belongsToCurrentTenant($leaseQuote)
            && ManagerScopeHelper::userCanAccessModel($user, $leaseQuote);
    }

    public function delete(User $user, LeaseQuote $leaseQuote): bool
    {
        return $user->can('leases.DELETE')
            && $this->belongsToCurrentTenant($leaseQuote)
            && ManagerScopeHelper::userCanAccessModel($user, $leaseQuote);
    }
}
