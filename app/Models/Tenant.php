<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravelcm\Subscriptions\Traits\HasPlanSubscriptions;
use Spatie\Multitenancy\Models\Tenant as BaseTenant;

#[Fillable(['name', 'domain', 'database'])]
class Tenant extends BaseTenant
{
    use HasPlanSubscriptions;

    public function accountMemberships(): HasMany
    {
        return $this->hasMany(AccountMembership::class, 'account_tenant_id');
    }

    /** @return BelongsToMany<User, $this> */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'account_memberships', 'account_tenant_id', 'user_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function featureFlagOverrides(): HasMany
    {
        return $this->hasMany(FeatureFlagOverride::class, 'account_tenant_id');
    }
}
