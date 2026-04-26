<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Database\Factories\OwnerRegistrationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OwnerRegistration extends Model
{
    /** @use HasFactory<OwnerRegistrationFactory> */
    use BelongsToAccountTenant, HasFactory;

    protected $table = 'rf_owner_registrations';

    protected $fillable = [
        'account_tenant_id',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'status',
        'submitted_data',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    protected function casts(): array
    {
        return [
            'submitted_data' => 'json',
            'reviewed_at' => 'datetime',
        ];
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
