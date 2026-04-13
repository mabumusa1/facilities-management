<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Represents a unit listing in the marketplace for sale.
 */
#[Fillable([
    'tenant_id',
    'unit_id',
    'status_id',
    'listing_title_en',
    'listing_title_ar',
    'listing_description_en',
    'listing_description_ar',
    'listing_price',
    'original_price',
    'price_per_sqm',
    'price_negotiable',
    'is_featured',
    'is_published',
    'published_at',
    'expires_at',
    'listed_by',
    'assigned_agent',
    'views_count',
    'inquiries_count',
    'buyer_id',
    'sold_at',
    'sold_price',
])]
class MarketplaceUnit extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'listing_price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'price_per_sqm' => 'decimal:2',
            'sold_price' => 'decimal:2',
            'price_negotiable' => 'boolean',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
            'sold_at' => 'datetime',
        ];
    }

    /**
     * Get the tenant that owns this listing.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the unit being listed.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the status of this listing.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Get the contact who listed this unit.
     */
    public function lister(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'listed_by');
    }

    /**
     * Get the assigned agent for this listing.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'assigned_agent');
    }

    /**
     * Get the buyer of this unit.
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'buyer_id');
    }

    /**
     * Get the visits for this listing.
     */
    public function visits(): HasMany
    {
        return $this->hasMany(MarketplaceVisit::class);
    }

    /**
     * Publish the listing.
     */
    public function publish(): void
    {
        $this->update([
            'is_published' => true,
            'published_at' => now(),
        ]);
    }

    /**
     * Unpublish the listing.
     */
    public function unpublish(): void
    {
        $this->update([
            'is_published' => false,
        ]);
    }

    /**
     * Feature the listing.
     */
    public function feature(): void
    {
        $this->update(['is_featured' => true]);
    }

    /**
     * Unfeature the listing.
     */
    public function unfeature(): void
    {
        $this->update(['is_featured' => false]);
    }

    /**
     * Mark the listing as sold.
     */
    public function markAsSold(int $buyerId, float $soldPrice): void
    {
        $this->update([
            'buyer_id' => $buyerId,
            'sold_at' => now(),
            'sold_price' => $soldPrice,
            'is_published' => false,
        ]);
    }

    /**
     * Increment the view count.
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Increment the inquiry count.
     */
    public function incrementInquiries(): void
    {
        $this->increment('inquiries_count');
    }

    /**
     * Check if the listing is published.
     */
    public function isPublished(): bool
    {
        return $this->is_published;
    }

    /**
     * Check if the listing is featured.
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Check if the listing is sold.
     */
    public function isSold(): bool
    {
        return $this->sold_at !== null;
    }

    /**
     * Check if the listing is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /**
     * Check if price is negotiable.
     */
    public function isPriceNegotiable(): bool
    {
        return $this->price_negotiable;
    }

    /**
     * Get the discount percentage if original price exists.
     */
    public function getDiscountPercentage(): ?float
    {
        if (! $this->original_price || $this->original_price <= 0) {
            return null;
        }

        return round((($this->original_price - $this->listing_price) / $this->original_price) * 100, 2);
    }
}
