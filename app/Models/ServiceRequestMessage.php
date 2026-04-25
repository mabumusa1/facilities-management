<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Database\Factories\ServiceRequestMessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ServiceRequestMessage extends Model
{
    /** @use HasFactory<ServiceRequestMessageFactory> */
    use BelongsToAccountTenant, HasFactory;

    protected $table = 'rf_service_request_messages';

    protected $fillable = [
        'service_request_id',
        'sender_type',
        'sender_id',
        'body',
        'is_internal',
        'account_tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'is_internal' => 'boolean',
        ];
    }

    /** @return BelongsTo<Request, $this> */
    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(Request::class, 'service_request_id');
    }

    /** @return MorphTo<Model, $this> */
    public function sender(): MorphTo
    {
        return $this->morphTo();
    }
}
