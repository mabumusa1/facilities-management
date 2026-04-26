<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Database\Factories\ServiceCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    /** @use HasFactory<ServiceCategoryFactory> */
    use BelongsToAccountTenant, HasFactory;

    protected $fillable = [
        'name_en',
        'name_ar',
        'icon',
        'response_sla_hours',
        'resolution_sla_hours',
        'default_assignee_id',
        'require_completion_photo',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'response_sla_hours' => 'integer',
            'resolution_sla_hours' => 'integer',
            'require_completion_photo' => 'boolean',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function defaultAssignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'default_assignee_id');
    }

    /** @return HasMany<ServiceSubcategory, $this> */
    public function subcategories(): HasMany
    {
        return $this->hasMany(ServiceSubcategory::class);
    }

    /** @return BelongsToMany<Community, $this> */
    public function communities(): BelongsToMany
    {
        return $this->belongsToMany(Community::class, 'service_category_communities', 'service_category_id', 'community_id');
    }

    /** @return HasMany<Request, $this> */
    public function serviceRequests(): HasMany
    {
        return $this->hasMany(Request::class, 'service_category_id');
    }
}
