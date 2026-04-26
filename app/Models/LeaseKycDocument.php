<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaseKycDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lease_id',
        'document_type_id',
        'is_required',
        'original_file_name',
        'stored_path',
        'mime_type',
        'file_size',
        'account_tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'file_size' => 'integer',
        ];
    }

    /** @return BelongsTo<Lease, $this> */
    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    /** @return BelongsTo<Setting, $this> */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(Setting::class, 'document_type_id');
    }
}
