<?php

namespace App\Models;

use Database\Factories\DocumentVersionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentVersion extends Model
{
    /** @use HasFactory<DocumentVersionFactory> */
    use HasFactory;

    protected $table = 'rf_document_versions';

    protected $fillable = [
        'document_template_id',
        'version_number',
        'body',
        'file_path',
        'merge_fields',
        'published_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'version_number' => 'integer',
            'merge_fields' => 'json',
            'published_at' => 'datetime',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(DocumentTemplate::class, 'document_template_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function records(): HasMany
    {
        return $this->hasMany(DocumentRecord::class, 'document_template_version_id');
    }
}
