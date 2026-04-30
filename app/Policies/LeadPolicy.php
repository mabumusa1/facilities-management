<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    use ChecksTenantOwnership;

    public function viewAny(User $user): bool
    {
        return $user->can('leads.VIEW');
    }

    public function view(User $user, Lead $lead): bool
    {
        return $user->can('leads.VIEW')
            && $this->belongsToCurrentTenant($lead);
    }

    public function create(User $user): bool
    {
        return $user->can('leads.CREATE');
    }

    public function update(User $user, Lead $lead): bool
    {
        return $user->can('leads.UPDATE')
            && $this->belongsToCurrentTenant($lead);
    }

    public function addNote(User $user, Lead $lead): bool
    {
        return $user->can('leads.UPDATE')
            && $this->belongsToCurrentTenant($lead);
    }

    public function assign(User $user, Lead $lead): bool
    {
        return $user->can('leads.UPDATE')
            && $this->belongsToCurrentTenant($lead);
    }

    public function convert(User $user, Lead $lead): bool
    {
        return $user->can('leads.UPDATE')
            && $this->belongsToCurrentTenant($lead);
    }

    public function delete(User $user, Lead $lead): bool
    {
        return $user->can('leads.DELETE')
            && $this->belongsToCurrentTenant($lead);
    }

    public function restore(User $user, Lead $lead): bool
    {
        return $user->can('leads.RESTORE')
            && $this->belongsToCurrentTenant($lead);
    }

    public function forceDelete(User $user, Lead $lead): bool
    {
        return $user->can('leads.FORCE_DELETE')
            && $this->belongsToCurrentTenant($lead);
    }
}
