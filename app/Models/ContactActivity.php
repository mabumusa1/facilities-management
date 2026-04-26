<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ContactActivity extends Model
{
    use BelongsToAccountTenant;

    protected $table = 'rf_contact_activities';

    protected $fillable = [
        'account_tenant_id', 'contact_type', 'contact_id',
        'event_type', 'metadata',
    ];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function contact(): MorphTo
    {
        return $this->morphTo();
    }
}
