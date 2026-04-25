<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Database\Factories\DocumentTemplateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentTemplate extends Model
{
    /** @use HasFactory<DocumentTemplateFactory> */
    use BelongsToAccountTenant, HasFactory, SoftDeletes;

    protected $table = 'rf_document_templates';

    protected $fillable = [
        'account_tenant_id',
        'name',
        'type',
        'status',
        'format',
        'created_by',
        'current_version_id',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'json',
        ];
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function currentVersion(): BelongsTo
    {
        return $this->belongsTo(DocumentVersion::class, 'current_version_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
