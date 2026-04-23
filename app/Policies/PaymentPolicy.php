<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\Payment;
use App\Models\User;
use App\Support\ManagerScopeHelper;

class PaymentPolicy
{
    use ChecksTenantOwnership;

    public function viewAny(User $user): bool
    {
        return $user->can('payments.VIEW');
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->can('payments.VIEW')
            && $this->belongsToCurrentTenant($payment)
            && ManagerScopeHelper::userCanAccessModel($user, $payment);
    }

    public function create(User $user): bool
    {
        return $user->can('payments.CREATE');
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->can('payments.UPDATE')
            && $this->belongsToCurrentTenant($payment)
            && ManagerScopeHelper::userCanAccessModel($user, $payment);
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->can('payments.DELETE')
            && $this->belongsToCurrentTenant($payment)
            && ManagerScopeHelper::userCanAccessModel($user, $payment);
    }

    public function restore(User $user, Payment $payment): bool
    {
        return $user->can('payments.RESTORE')
            && $this->belongsToCurrentTenant($payment)
            && ManagerScopeHelper::userCanAccessModel($user, $payment);
    }

    public function forceDelete(User $user, Payment $payment): bool
    {
        return $user->can('payments.FORCE_DELETE')
            && $this->belongsToCurrentTenant($payment)
            && ManagerScopeHelper::userCanAccessModel($user, $payment);
    }
}
