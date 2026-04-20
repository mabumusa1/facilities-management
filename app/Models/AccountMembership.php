<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'account_tenant_id', 'role'])]
class AccountMembership extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function accountTenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'account_tenant_id');
    }
}
