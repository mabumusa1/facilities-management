<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Suggestion extends Model
{
    use BelongsToAccountTenant;

    protected $table = 'rf_suggestions';

    protected $fillable = [
        'account_tenant_id', 'resident_id', 'title', 'description',
        'is_anonymous', 'status', 'upvotes_count', 'reviewed_by', 'admin_response',
    ];

    protected function casts(): array
    {
        return ['is_anonymous' => 'boolean', 'upvotes_count' => 'integer'];
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
