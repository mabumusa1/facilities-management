<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Database\Factories\InvoiceSettingFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Tenant-level invoice and company branding settings.
 *
 * Single-row-per-tenant (enforced by unique index on account_tenant_id).
 *
 * IMPORTANT: invoice_next_sequence must only be incremented atomically
 * using DB::table('rf_invoice_settings')->lockForUpdate() inside a
 * transaction. See Accounting stories #194 / #227 for the consuming pattern.
 *
 * @property string|null $name_en
 * @property string|null $name_ar
 * @property string|null $logo_path
 * @property string|null $logo_ar_path
 * @property string $timezone
 * @property string|null $primary_color
 * @property string $invoice_prefix
 * @property int $invoice_next_sequence
 * @property int $payment_terms_days
 * @property float|null $late_payment_penalty_pct
 * @property int $late_payment_grace_days
 * @property string|null $footer_text_en
 * @property string|null $footer_text_ar
 * @property bool $show_vat_number
 */
class InvoiceSetting extends Model
{
    /** @use HasFactory<InvoiceSettingFactory> */
    use BelongsToAccountTenant, HasFactory;

    protected $table = 'rf_invoice_settings';

    protected $fillable = [
        // Legacy columns — kept for backward compat
        'company_name',
        'logo',
        'address',
        'vat',
        'instructions',
        'notes',
        'vat_number',
        'cr_number',
        'account_tenant_id',
        // New bilingual / branding columns
        'name_en',
        'name_ar',
        'logo_path',
        'logo_ar_path',
        'timezone',
        'primary_color',
        // Invoice numbering
        'invoice_prefix',
        'invoice_next_sequence',
        // Payment terms
        'payment_terms_days',
        'late_payment_penalty_pct',
        'late_payment_grace_days',
        // Invoice footer
        'footer_text_en',
        'footer_text_ar',
        // Display toggles
        'show_vat_number',
    ];

    protected function casts(): array
    {
        return [
            'vat' => 'decimal:2',
            'late_payment_penalty_pct' => 'decimal:2',
            'show_vat_number' => 'boolean',
            'invoice_next_sequence' => 'integer',
            'payment_terms_days' => 'integer',
            'late_payment_grace_days' => 'integer',
        ];
    }

    protected function logoUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (empty($this->logo_path)) {
                return null;
            }

            return Storage::disk('public')->url($this->logo_path);
        });
    }

    protected function logoArUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (empty($this->logo_ar_path)) {
                return null;
            }

            return Storage::disk('public')->url($this->logo_ar_path);
        });
    }
}
