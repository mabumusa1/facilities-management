<?php

namespace App\Models;

use Database\Factories\DocumentSignatureFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentSignature extends Model
{
    /** @use HasFactory<DocumentSignatureFactory> */
    use HasFactory;

    protected $table = 'rf_document_signatures';

    protected $fillable = [
        'document_record_id',
        'signer_name',
        'signer_email',
        'signed_at',
        'ip_address',
        'otp_verified_at',
        'signed_file_path',
    ];

    protected function casts(): array
    {
        return [
            'signed_at' => 'datetime',
            'otp_verified_at' => 'datetime',
        ];
    }

    public function documentRecord(): BelongsTo
    {
        return $this->belongsTo(DocumentRecord::class);
    }
}
