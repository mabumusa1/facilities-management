<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UnitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Represents a rentable/sellable unit within a building.
 *
 * Units are the third level in the property hierarchy:
 * Community → Building → Unit
 *
 * Note: This is a minimal stub to support Building relationship tests.
 * Full Unit entity will be implemented in Issue #12.
 */
class Unit extends Model
{
    /** @use HasFactory<UnitFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'community_id',
        'building_id',
        'name',
        'status',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    /**
     * Get the tenant that owns this unit.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the community this unit belongs to.
     */
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * Get the building this unit is in.
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }
}
