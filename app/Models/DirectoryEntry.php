<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DirectoryEntry extends Model
{
    use BelongsToAccountTenant, SoftDeletes;

    protected $table = 'rf_directory_entries';

    protected $fillable = [
        'account_tenant_id', 'name', 'category', 'phone_number',
        'email', 'description', 'status', 'created_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
