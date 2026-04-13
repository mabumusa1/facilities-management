<?php

namespace App\Models;

use Database\Factories\ServiceRequestSubcategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequestSubcategory extends Model
{
    /** @use HasFactory<ServiceRequestSubcategoryFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'name_ar',
        'active',
        'icon_id',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get the category that owns the subcategory.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestCategory::class, 'category_id');
    }

    /**
     * Scope to filter only active subcategories.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * Scope to filter only inactive subcategories.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('active', false);
    }

    /**
     * Scope to filter subcategories by category.
     */
    public function scopeForCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Check if the subcategory is active.
     */
    public function isActive(): bool
    {
        return $this->active;
    }
}
