<?php

namespace App\Models;

use App\Concerns\HasManagerScope;
use App\Support\ManagerScopeHelper;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory, HasManagerScope, SoftDeletes;

    protected $table = 'rf_payments';

    /**
     * Payments: filter via transaction_id → rf_transactions.unit_id → rf_units.
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeForManager(Builder $query, User $user): Builder
    {
        $scopes = ManagerScopeHelper::scopesForUser($user);

        if ($scopes['is_unrestricted']) {
            return $query;
        }

        $communityIds = $scopes['community_ids'];
        $buildingIds = $scopes['building_ids'];

        if (empty($communityIds) && empty($buildingIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn(
            $this->getTable().'.transaction_id',
            fn ($sub) => $sub
                ->select('rf_transactions.id')
                ->from('rf_transactions')
                ->join('rf_units', 'rf_units.id', '=', 'rf_transactions.unit_id')
                ->where(function ($q) use ($communityIds, $buildingIds): void {
                    if (! empty($communityIds)) {
                        $q->orWhereIn('rf_units.rf_community_id', $communityIds);
                    }
                    if (! empty($buildingIds)) {
                        $q->orWhereIn('rf_units.rf_building_id', $buildingIds);
                    }
                })
        );
    }

    protected $fillable = [
        'transaction_id',
        'amount',
        'payment_date',
        'payment_method',
        'reference',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
