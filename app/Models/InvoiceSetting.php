<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Illuminate\Database\Eloquent\Model;

class InvoiceSetting extends Model
{
    use BelongsToAccountTenant;

    protected $table = 'rf_invoice_settings';

    protected $fillable = [
        'company_name',
        'logo',
        'address',
        'vat',
        'instructions',
        'notes',
        'vat_number',
        'cr_number',
        'account_tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'vat' => 'decimal:2',
        ];
    }
}
