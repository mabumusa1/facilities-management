<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExcelSheet extends Model
{
    use BelongsToAccountTenant;

    protected $table = 'rf_excel_sheets';

    protected $fillable = [
        'type',
        'file_path',
        'file_name',
        'status',
        'error_details',
        'rf_community_id',
        'account_tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'error_details' => 'array',
        ];
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class, 'rf_community_id');
    }
}
