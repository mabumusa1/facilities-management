<?php

namespace App\Concerns;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToAccountTenant
{
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

    public function accountTenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'account_tenant_id');
    }
}
