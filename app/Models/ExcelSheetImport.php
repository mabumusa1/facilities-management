<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Database\Factories\ExcelSheetImportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExcelSheetImport extends Model
{
    /** @use HasFactory<ExcelSheetImportFactory> */
    use BelongsToAccountTenant, HasFactory;

    protected $table = 'rf_excel_sheet_imports';

    protected $fillable = [
        'account_tenant_id',
        'excel_sheet_id',
        'imported_by',
        'imported_at',
        'total_rows',
        'successful_rows',
        'failed_rows',
        'error_details',
        'error_report_path',
    ];

    protected function casts(): array
    {
        return [
            'imported_at' => 'datetime',
            'total_rows' => 'integer',
            'successful_rows' => 'integer',
            'failed_rows' => 'integer',
            'error_details' => 'json',
        ];
    }

    public function excelSheet(): BelongsTo
    {
        return $this->belongsTo(ExcelSheet::class);
    }

    public function importer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }
}
