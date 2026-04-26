<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitStatusHistory extends Model
{
    use BelongsToAccountTenant;

    protected $table = 'rf_unit_status_history';

    protected $fillable = [
        'account_tenant_id', 'unit_id', 'from_status', 'to_status',
        'changed_by', 'reason',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
