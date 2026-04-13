<?php

namespace App\Models;

use Database\Factories\StatusFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    /** @use HasFactory<StatusFactory> */
    use HasFactory;

    // Domain constants
    public const DOMAIN_SERVICE_REQUEST = 'service_request';

    public const DOMAIN_VISITOR_ACCESS = 'visitor_access';

    public const DOMAIN_FACILITY_BOOKING = 'facility_booking';

    public const DOMAIN_MARKETPLACE_UNIT = 'marketplace_unit';

    public const DOMAIN_MARKETPLACE_VISIT = 'marketplace_visit';

    public const DOMAIN_LEASE = 'lease';

    public const DOMAIN_VISIT_SCHEDULING = 'visit_scheduling';

    public const DOMAIN_BOOKING_CONTRACT = 'booking_contract';

    public const DOMAIN_APPLICATION = 'application';

    public const DOMAIN_TRANSACTION = 'transaction';

    public const DOMAIN_UNIT = 'unit';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'name_ar',
        'domain',
        'slug',
        'color',
        'icon',
        'priority',
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
            'priority' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope to filter active statuses.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by domain.
     */
    public function scopeForDomain(Builder $query, string $domain): Builder
    {
        return $query->where('domain', $domain);
    }

    /**
     * Get all available domains.
     *
     * @return array<string>
     */
    public static function domains(): array
    {
        return [
            self::DOMAIN_SERVICE_REQUEST,
            self::DOMAIN_VISITOR_ACCESS,
            self::DOMAIN_FACILITY_BOOKING,
            self::DOMAIN_MARKETPLACE_UNIT,
            self::DOMAIN_MARKETPLACE_VISIT,
            self::DOMAIN_LEASE,
            self::DOMAIN_VISIT_SCHEDULING,
            self::DOMAIN_BOOKING_CONTRACT,
            self::DOMAIN_APPLICATION,
            self::DOMAIN_TRANSACTION,
            self::DOMAIN_UNIT,
        ];
    }

    /**
     * Find status by slug.
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }
}
