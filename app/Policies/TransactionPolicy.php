<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\Transaction;
use App\Models\User;
use App\Support\ManagerScopeHelper;

class TransactionPolicy
{
    use ChecksTenantOwnership;

    public function viewAny(User $user): bool
    {
        return $user->can('transactions.VIEW');
    }

    public function view(User $user, Transaction $transaction): bool
    {
        return $user->can('transactions.VIEW')
            && $this->belongsToCurrentTenant($transaction)
            && ManagerScopeHelper::userCanAccessModel($user, $transaction);
    }

    public function create(User $user): bool
    {
        return $user->can('transactions.CREATE');
    }

    public function update(User $user, Transaction $transaction): bool
    {
        return $user->can('transactions.UPDATE')
            && $this->belongsToCurrentTenant($transaction)
            && ManagerScopeHelper::userCanAccessModel($user, $transaction);
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->can('transactions.DELETE')
            && $this->belongsToCurrentTenant($transaction)
            && ManagerScopeHelper::userCanAccessModel($user, $transaction);
    }

    public function restore(User $user, Transaction $transaction): bool
    {
        return $user->can('transactions.RESTORE')
            && $this->belongsToCurrentTenant($transaction)
            && ManagerScopeHelper::userCanAccessModel($user, $transaction);
    }

    public function forceDelete(User $user, Transaction $transaction): bool
    {
        return $user->can('transactions.FORCE_DELETE')
            && $this->belongsToCurrentTenant($transaction)
            && ManagerScopeHelper::userCanAccessModel($user, $transaction);
    }
}
