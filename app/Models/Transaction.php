<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    /** @use HasFactory<TransactionFactory> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'subcategory_id',
        'type_id',
        'status_id',
        'unit_id',
        'lease_id',
        'assignee_id',
        'amount',
        'tax_amount',
        'rental_amount',
        'additional_fees_amount',
        'vat',
        'paid',
        'left',
        'lease_number',
        'details',
        'additional_fees',
        'images',
        'due_on',
        'is_paid',
        'is_old',
        'assignee_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'rental_amount' => 'decimal:2',
        'additional_fees_amount' => 'decimal:2',
        'vat' => 'decimal:2',
        'paid' => 'decimal:2',
        'left' => 'decimal:2',
        'additional_fees' => 'array',
        'images' => 'array',
        'due_on' => 'date',
        'is_paid' => 'boolean',
        'is_old' => 'boolean',
        'assignee_active' => 'boolean',
    ];

    // Relationships

    public function category(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class, 'category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(TransactionSubcategory::class, 'subcategory_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(TransactionType::class, 'type_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'assignee_id');
    }

    // Uncomment when Payment model is created
    // public function payments(): HasMany
    // {
    //     return $this->hasMany(Payment::class);
    // }

    // Scopes

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('is_paid', true);
    }

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where('is_paid', false);
    }

    public function scopeDue(Builder $query): Builder
    {
        return $query->where('due_on', '<=', now())->where('is_paid', false);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('due_on', '<', now()->startOfDay())->where('is_paid', false);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('due_on', '>', now())->where('is_paid', false);
    }

    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByType(Builder $query, int $typeId): Builder
    {
        return $query->where('type_id', $typeId);
    }

    public function scopeByStatus(Builder $query, int $statusId): Builder
    {
        return $query->where('status_id', $statusId);
    }

    public function scopeByAssignee(Builder $query, int $assigneeId): Builder
    {
        return $query->where('assignee_id', $assigneeId);
    }

    public function scopeByUnit(Builder $query, int $unitId): Builder
    {
        return $query->where('unit_id', $unitId);
    }

    public function scopeByLease(Builder $query, int $leaseId): Builder
    {
        return $query->where('lease_id', $leaseId);
    }

    // Helper Methods

    /**
     * Get the formatted amount.
     */
    public function getAmountFormatted(): string
    {
        return number_format($this->amount, 2);
    }

    /**
     * Get the formatted paid amount.
     */
    public function getPaidFormatted(): string
    {
        return number_format($this->paid, 2);
    }

    /**
     * Get the formatted remaining amount.
     */
    public function getLeftFormatted(): string
    {
        return number_format($this->left, 2);
    }

    /**
     * Mark the transaction as paid.
     */
    public function markAsPaid(): void
    {
        $this->update([
            'is_paid' => true,
            'paid' => $this->amount,
            'left' => 0,
        ]);
    }

    /**
     * Check if the transaction is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_on < now()->startOfDay() && ! $this->is_paid;
    }

    /**
     * Check if the transaction is due today.
     */
    public function isDueToday(): bool
    {
        return $this->due_on->isToday() && ! $this->is_paid;
    }

    /**
     * Update the payment status.
     * Uncomment when Payment model is created
     */
    // public function updatePaymentStatus(): void
    // {
    //     $totalPaid = $this->payments()->sum('amount');
    //
    //     $this->update([
    //         'paid' => $totalPaid,
    //         'left' => $this->amount - $totalPaid,
    //         'is_paid' => $totalPaid >= $this->amount,
    //     ]);
    // }

    /**
     * Get the payment percentage.
     */
    public function getPaymentPercentage(): float
    {
        if ($this->amount == 0) {
            return 0;
        }

        return ($this->paid / $this->amount) * 100;
    }
}
