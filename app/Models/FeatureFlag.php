<?php

namespace App\Models;

use Database\Factories\FeatureFlagFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FeatureFlag extends Model
{
    /** @use HasFactory<FeatureFlagFactory> */
    use HasFactory;

    // Category constants
    public const CATEGORY_CONTACTS = 'contacts';

    public const CATEGORY_PROPERTIES = 'properties';

    public const CATEGORY_LEASING = 'leasing';

    public const CATEGORY_TRANSACTIONS = 'transactions';

    public const CATEGORY_REQUESTS = 'requests';

    public const CATEGORY_COMMUNICATION = 'communication';

    public const CATEGORY_REPORTS = 'reports';

    public const CATEGORY_TOOLS = 'tools';

    public const CATEGORY_INTEGRATIONS = 'integrations';

    public const CATEGORY_MARKETPLACE = 'marketplace';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'name',
        'name_ar',
        'description',
        'category',
        'default_value',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'default_value' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get tenants that have this feature flag configured.
     */
    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_feature_flags')
            ->withPivot('is_enabled')
            ->withTimestamps();
    }

    /**
     * Scope to filter active feature flags.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeForCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to filter enabled by default.
     */
    public function scopeEnabledByDefault(Builder $query): Builder
    {
        return $query->where('default_value', true);
    }

    /**
     * Find feature flag by key.
     */
    public static function findByKey(string $key): ?self
    {
        return static::where('key', $key)->first();
    }

    /**
     * Check if feature is enabled for a tenant.
     */
    public function isEnabledForTenant(?Tenant $tenant): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if (! $tenant) {
            return $this->default_value;
        }

        $tenantFlag = $this->tenants()
            ->where('tenant_id', $tenant->id)
            ->first();

        if ($tenantFlag) {
            return $tenantFlag->pivot->is_enabled;
        }

        return $this->default_value;
    }

    /**
     * Get all available categories.
     *
     * @return array<string>
     */
    public static function categories(): array
    {
        return [
            self::CATEGORY_CONTACTS,
            self::CATEGORY_PROPERTIES,
            self::CATEGORY_LEASING,
            self::CATEGORY_TRANSACTIONS,
            self::CATEGORY_REQUESTS,
            self::CATEGORY_COMMUNICATION,
            self::CATEGORY_REPORTS,
            self::CATEGORY_TOOLS,
            self::CATEGORY_INTEGRATIONS,
            self::CATEGORY_MARKETPLACE,
        ];
    }
}
