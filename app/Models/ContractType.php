<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Database\Factories\ContractTypeFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Tenant-configurable contract types (Yearly Rental, Monthly Rental, etc.).
 *
 * Distinct from rf_settings rows of type='rental_contract_type', which remain
 * as system-wide reference data for legacy Leasing FK compatibility.
 * Once Leasing story #226 cuts the FK over to this table, the rf_settings
 * reference rows can be deprecated.
 *
 * @see Setting  for legacy system-wide reference rows
 */
class ContractType extends Model
{
    /** @use HasFactory<ContractTypeFactory> */
    use BelongsToAccountTenant, HasFactory;

    protected $table = 'rf_contract_types';

    protected $fillable = [
        'account_tenant_id',
        'name_en',
        'name_ar',
        'default_payment_terms_days',
        'default_escalation_type',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'default_payment_terms_days' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Scope to active contract types, ordered by sort_order.
     *
     * @param  Builder<ContractType>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order');
    }
}
