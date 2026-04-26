<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Complaint extends Model
{
    use BelongsToAccountTenant, SoftDeletes;

    protected $table = 'rf_complaints';

    protected $fillable = [
        'account_tenant_id', 'resident_id', 'title', 'description',
        'category', 'status', 'assigned_to', 'resolution_notes', 'resolved_at',
    ];

    protected function casts(): array
    {
        return ['resolved_at' => 'datetime'];
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
