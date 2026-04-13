<?php

namespace App\Models;

use Database\Factories\ServiceRequestCategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequestCategory extends Model
{
    /** @use HasFactory<ServiceRequestCategoryFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'description',
        'description_ar',
        'active',
        'has_sub_categories',
        'icon_id',
        'service_settings',
    ];

    protected $casts = [
        'active' => 'boolean',
        'has_sub_categories' => 'boolean',
        'service_settings' => 'array',
    ];

    /**
     * Get the subcategories for this category.
     */
    public function subcategories(): HasMany
    {
        return $this->hasMany(ServiceRequestSubcategory::class, 'category_id');
    }

    /**
     * Get the active subcategories for this category.
     */
    public function activeSubcategories(): HasMany
    {
        return $this->hasMany(ServiceRequestSubcategory::class, 'category_id')
            ->where('active', true);
    }

    /**
     * Scope to filter only active categories.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * Scope to filter only inactive categories.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('active', false);
    }

    /**
     * Scope to filter categories with subcategories.
     */
    public function scopeWithSubcategories(Builder $query): Builder
    {
        return $query->where('has_sub_categories', true);
    }

    /**
     * Scope to filter categories without subcategories.
     */
    public function scopeWithoutSubcategories(Builder $query): Builder
    {
        return $query->where('has_sub_categories', false);
    }

    /**
     * Check if the category is active.
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Check if the category has subcategories.
     */
    public function hasSubcategories(): bool
    {
        return $this->has_sub_categories;
    }

    /**
     * Get a service setting value.
     */
    public function getServiceSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->service_settings, $key, $default);
    }

    /**
     * Check if a permission is enabled.
     */
    public function hasPermission(string $permission): bool
    {
        return (bool) $this->getServiceSetting("permissions.{$permission}", false);
    }

    /**
     * Check if a visibility setting is enabled.
     */
    public function hasVisibilitySetting(string $setting): bool
    {
        return (bool) $this->getServiceSetting("visibilities.{$setting}", false);
    }
}
