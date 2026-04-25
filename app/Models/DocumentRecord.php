<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Database\Factories\DocumentRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentRecord extends Model
{
    /** @use HasFactory<DocumentRecordFactory> */
    use BelongsToAccountTenant, HasFactory, SoftDeletes;

    protected $table = 'rf_document_records';

    protected $fillable = [
        'account_tenant_id',
        'document_template_version_id',
        'source_type',
        'source_id',
        'generated_at',
        'file_path',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
        ];
    }

    public function templateVersion(): BelongsTo
    {
        return $this->belongsTo(DocumentVersion::class, 'document_template_version_id');
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(DocumentSignature::class);
    }
}
