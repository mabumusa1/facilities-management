<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    /** @use HasFactory<TransactionFactory> */
    use BelongsToAccountTenant, HasFactory, SoftDeletes;

    protected $table = 'rf_transactions';

    protected $fillable = [
        'lease_id',
        'unit_id',
        'category_id',
        'subcategory_id',
        'type_id',
        'status_id',
        'assignee_type',
        'assignee_id',
        'account_tenant_id',
        'amount',
        'tax_amount',
        'rental_amount',
        'additional_fees_amount',
        'vat',
        'due_on',
        'details',
        'lease_number',
        'is_paid',
        'is_old',
    ];

    protected $attributes = [
        'tax_amount' => 0,
        'additional_fees_amount' => 0,
        'vat' => 0,
        'is_paid' => false,
        'is_old' => false,
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'rental_amount' => 'decimal:2',
            'additional_fees_amount' => 'decimal:2',
            'vat' => 'decimal:2',
            'due_on' => 'date',
            'is_paid' => 'boolean',
            'is_old' => 'boolean',
        ];
    }

    public function assignee(): MorphTo
    {
        return $this->morphTo();
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function additionalFees(): HasMany
    {
        return $this->hasMany(TransactionAdditionalFee::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    protected function paid(): Attribute
    {
        return Attribute::get(
            fn () => number_format((float) $this->payments()->sum('amount'), 2, '.', ''),
        );
    }

    protected function left(): Attribute
    {
        return Attribute::get(
            fn () => number_format((float) $this->amount - (float) $this->paid, 2, '.', ''),
        );
    }
}
