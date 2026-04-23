<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use App\Concerns\HasBilingualName;
use App\Concerns\HasManagerScope;
use Database\Factories\FacilityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facility extends Model
{
    /** @use HasFactory<FacilityFactory> */
    use BelongsToAccountTenant, HasBilingualName, HasFactory, HasManagerScope, SoftDeletes;

    protected $table = 'rf_facilities';

    protected $fillable = [
        'category_id',
        'community_id',
        'name',
        'name_ar',
        'name_en',
        'description',
        'capacity',
        'open_time',
        'close_time',
        'booking_fee',
        'is_active',
        'requires_approval',
        'account_tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'booking_fee' => 'decimal:2',
            'is_active' => 'boolean',
            'requires_approval' => 'boolean',
        ];
    }

    /** @return BelongsTo<FacilityCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(FacilityCategory::class, 'category_id');
    }

    /** @return BelongsTo<Community, $this> */
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    /** @return HasMany<FacilityBooking, $this> */
    public function bookings(): HasMany
    {
        return $this->hasMany(FacilityBooking::class);
    }
}
