<?php

namespace App\Models;

use App\Concerns\HasBilingualName;
use Database\Factories\RequestCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RequestCategory extends Model
{
    /** @use HasFactory<RequestCategoryFactory> */
    use HasBilingualName, HasFactory;

    protected $table = 'rf_request_categories';

    protected $fillable = [
        'name',
        'name_ar',
        'name_en',
        'description',
        'status',
        'has_sub_categories',
        'icon_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'has_sub_categories' => 'boolean',
        ];
    }

    /** @return HasMany<RequestSubcategory, $this> */
    public function subcategories(): HasMany
    {
        return $this->hasMany(RequestSubcategory::class, 'category_id');
    }

    /** @return HasMany<Request, $this> */
    public function requests(): HasMany
    {
        return $this->hasMany(Request::class, 'category_id');
    }

    /** @return HasMany<ServiceSetting, $this> */
    public function serviceSettings(): HasMany
    {
        return $this->hasMany(ServiceSetting::class, 'category_id');
    }
}
