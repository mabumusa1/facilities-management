<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ContactDocument extends Model
{
    use BelongsToAccountTenant;

    protected $table = 'rf_contact_documents';

    protected $fillable = [
        'account_tenant_id', 'contact_type', 'contact_id',
        'type', 'file_path', 'original_name', 'expires_at',
    ];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime'];
    }

    public function contact(): MorphTo
    {
        return $this->morphTo();
    }
}
