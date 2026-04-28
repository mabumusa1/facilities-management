<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExcelSheet extends Model
{
    use BelongsToAccountTenant;

    protected $table = 'rf_excel_sheets';

    protected $fillable = [
        'type',
        'import_type',
        'file_path',
        'file_name',
        'status',
        'column_schema',
        'template_file_path',
        'error_details',
        'total_rows',
        'success_count',
        'error_count',
        'meta',
        'rf_community_id',
        'account_tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'error_details' => 'array',
            'column_schema' => 'array',
            'meta' => 'array',
            'total_rows' => 'integer',
            'success_count' => 'integer',
            'error_count' => 'integer',
        ];
    }

    public function imports(): HasMany
    {
        return $this->hasMany(ExcelSheetImport::class);
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class, 'rf_community_id');
    }
}
