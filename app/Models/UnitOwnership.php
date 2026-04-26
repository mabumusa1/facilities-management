<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitOwnership extends Model
{
    use BelongsToAccountTenant;

    protected $table = 'rf_unit_ownerships';

    protected $fillable = [
        'account_tenant_id', 'owner_id', 'unit_id',
        'ownership_type', 'ownership_percentage',
        'start_date', 'end_date',
    ];

    protected function casts(): array
    {
        return [
            'ownership_percentage' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
