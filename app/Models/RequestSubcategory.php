<?php

namespace App\Models;

use App\Concerns\HasBilingualName;
use Database\Factories\RequestSubcategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RequestSubcategory extends Model
{
    /** @use HasFactory<RequestSubcategoryFactory> */
    use HasBilingualName, HasFactory;

    protected $table = 'rf_request_subcategories';

    protected $fillable = [
        'category_id',
        'name',
        'name_ar',
        'name_en',
        'status',
        'icon_id',
        'start',
        'end',
        'is_all_day',
        'terms_and_conditions',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'is_all_day' => 'boolean',
        ];
    }

    /** @return BelongsTo<RequestCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(RequestCategory::class, 'category_id');
    }

    /** @return HasMany<WorkingDay, $this> */
    public function workingDays(): HasMany
    {
        return $this->hasMany(WorkingDay::class, 'subcategory_id');
    }

    /** @return HasMany<FeaturedService, $this> */
    public function featuredServices(): HasMany
    {
        return $this->hasMany(FeaturedService::class, 'subcategory_id');
    }
}
