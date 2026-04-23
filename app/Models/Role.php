<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use App\Enums\RoleType;
use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    /** @use HasFactory<RoleFactory> */
    use BelongsToAccountTenant, HasFactory;

    /**
     * Override the shared tenant scope so that system-wide roles
     * (account_tenant_id IS NULL) are visible in every tenant context,
     * while tenant-specific roles remain isolated to their own tenant.
     *
     * Only Role uses this logic — other tenant-scoped models keep strict isolation.
     */
    public static function bootBelongsToAccountTenant(): void
    {
        static::addGlobalScope('account_tenant', function (Builder $builder) {
            if ($tenant = Tenant::current()) {
                $column = $builder->getModel()->qualifyColumn('account_tenant_id');
                $builder->where(function (Builder $q) use ($column, $tenant) {
                    $q->where($column, $tenant->id)
                        ->orWhereNull($column);
                });
            }
        });

        static::creating(function (Model $model) {
            if (! $model->account_tenant_id && $tenant = Tenant::current()) {
                $model->account_tenant_id = $tenant->id;
            }
        });
    }

    /**
     * Override Spatie's create() to scope uniqueness by account_tenant_id
     * instead of a global (name, guard_name) check.
     *
     * Two tenants may each have a role named "admins"; only roles within the
     * same tenant must be unique by (account_tenant_id, name, guard_name).
     *
     * @throws RoleAlreadyExists
     */
    public static function create(array $attributes = []): static
    {
        $attributes['guard_name'] ??= Guard::getDefaultName(static::class);

        $query = static::query()
            ->where('name', $attributes['name'])
            ->where('guard_name', $attributes['guard_name']);

        // Scope the uniqueness check by tenant when an account_tenant_id is given.
        if (isset($attributes['account_tenant_id'])) {
            $query->where('account_tenant_id', $attributes['account_tenant_id']);
        } else {
            $query->whereNull('account_tenant_id');
        }

        if ($query->withoutGlobalScopes()->exists()) {
            throw RoleAlreadyExists::create($attributes['name'], $attributes['guard_name']);
        }

        return static::withoutGlobalScopes()->create($attributes);
    }

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'type' => RoleType::class,
        ]);
    }
}
