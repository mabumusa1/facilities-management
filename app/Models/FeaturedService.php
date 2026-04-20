<?php

namespace App\Models;

use App\Concerns\HasBilingualName;
use Database\Factories\FeaturedServiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeaturedService extends Model
{
    /** @use HasFactory<FeaturedServiceFactory> */
    use HasBilingualName, HasFactory;

    protected $table = 'rf_featured_services';

    protected $fillable = [
        'subcategory_id',
        'title',
        'title_ar',
        'title_en',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /** @return BelongsTo<RequestSubcategory, $this> */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(RequestSubcategory::class, 'subcategory_id');
    }
}
