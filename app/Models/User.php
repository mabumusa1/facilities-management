<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'phone_number', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles {
        HasRoles::syncRoles as protected traitSyncRoles;
        HasRoles::removeRole as protected traitRemoveRole;
    }

    use Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function accountMemberships(): HasMany
    {
        return $this->hasMany(AccountMembership::class);
    }

    /**
     * Remove a single scoped role row identified by the exact scope tuple.
     *
     * Spatie's default removeRole() issues DELETE WHERE role_id+model_id+model_type,
     * which wipes every scoped row for that role in one go. This override restricts
     * deletion to the exact (community_id, building_id, service_type_id) tuple so
     * that sibling scope rows are never accidentally removed.
     */
    public function removeScopedRole(
        string|int|Role $role,
        ?int $communityId = null,
        ?int $buildingId = null,
        ?int $serviceTypeId = null,
    ): static {
        $roleModel = $this->getStoredRole($role);

        \DB::table(config('permission.table_names.model_has_roles'))
            ->where('role_id', $roleModel->getKey())
            ->where('model_type', get_class($this))
            ->where('model_id', $this->id)
            ->where('community_id', $communityId)
            ->where('building_id', $buildingId)
            ->where('service_type_id', $serviceTypeId)
            ->delete();

        $this->unsetRelation('roles');
        $this->forgetWildcardPermissionIndex();

        return $this;
    }

    /**
     * Override to prevent silent mass-deletion of scoped role rows.
     *
     * For system-wide (null-null-null) rows, delegates to parent. For scoped rows,
     * call removeScopedRole() with the explicit scope tuple instead.
     *
     * @param  string|int|array|Role|Collection|\BackedEnum  ...$role
     */
    public function removeRole(...$role): static
    {
        /**
         * Resolve each variadic argument to its role primary key.
         * We cannot call collectRoles() here because Spatie declares it private.
         *
         * @var int[]
         */
        $roleIds = collect($role)
            ->flatten()
            ->map(fn ($r): int|string => $this->getStoredRole($r)->getKey())
            ->all();
        $table = config('permission.table_names.model_has_roles');

        foreach ($roleIds as $roleId) {
            $hasScopedRows = \DB::table($table)
                ->where('role_id', $roleId)
                ->where('model_type', get_class($this))
                ->where('model_id', $this->id)
                ->where(function ($q): void {
                    $q->whereNotNull('community_id')
                        ->orWhereNotNull('building_id')
                        ->orWhereNotNull('service_type_id');
                })
                ->exists();

            if ($hasScopedRows) {
                throw new \LogicException(
                    'Cannot use removeRole() on a role with scoped rows. '
                    .'Use removeScopedRole() with an explicit scope tuple to avoid data loss.'
                );
            }
        }

        return $this->traitRemoveRole(...$role);
    }

    /**
     * Override to prevent silent mass-wipe of scoped role rows.
     *
     * syncRoles() detaches all current roles before re-assigning, which would
     * destroy every scoped row. Use removeScopedRole() + assignRole() instead.
     *
     * @param  string|int|array|Role|Collection|\BackedEnum  ...$roles
     */
    public function syncRoles(...$roles): static
    {
        $table = config('permission.table_names.model_has_roles');

        $hasScopedRows = \DB::table($table)
            ->where('model_type', get_class($this))
            ->where('model_id', $this->id)
            ->where(function ($q): void {
                $q->whereNotNull('community_id')
                    ->orWhereNotNull('building_id')
                    ->orWhereNotNull('service_type_id');
            })
            ->exists();

        if ($hasScopedRows) {
            throw new \LogicException(
                'Cannot use syncRoles() when scoped role rows exist. '
                .'Use removeScopedRole() with explicit scope tuples and assignRole() instead.'
            );
        }

        return $this->traitSyncRoles(...$roles);
    }

    /**
     * @return BelongsToMany<Tenant, $this>
     */
    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'account_memberships', 'user_id', 'account_tenant_id')
            ->withPivot('role')
            ->withTimestamps();
    }
}
