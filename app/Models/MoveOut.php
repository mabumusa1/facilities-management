<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use App\Enums\MoveOutReason;
use Database\Factories\MoveOutFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MoveOut extends Model
{
    /** @use HasFactory<MoveOutFactory> */
    use BelongsToAccountTenant, HasFactory;

    protected $fillable = [
        'lease_id',
        'move_out_date',
        'reason',
        'status_id',
        'initiated_by',
        'account_tenant_id',
        'notes',
        'settled_at',
    ];

    protected function casts(): array
    {
        return [
            'reason' => MoveOutReason::class,
            'move_out_date' => 'date',
            'settled_at' => 'datetime',
        ];
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function initiatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(MoveOutRoom::class)->orderBy('sort_order');
    }

    public function deductions(): HasMany
    {
        return $this->hasMany(MoveOutDeduction::class);
    }

    /**
     * Total amount of all deductions.
     */
    public function totalDeductions(): string
    {
        return number_format((float) $this->deductions()->sum('amount'), 2, '.', '');
    }
}
