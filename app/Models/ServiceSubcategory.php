<?php

namespace App\Models;

use Database\Factories\ServiceSubcategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceSubcategory extends Model
{
    /** @use HasFactory<ServiceSubcategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'service_category_id',
        'name_en',
        'name_ar',
        'response_sla_hours',
        'resolution_sla_hours',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'response_sla_hours' => 'integer',
            'resolution_sla_hours' => 'integer',
        ];
    }

    /** @return BelongsTo<ServiceCategory, $this> */
    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    /**
     * Resolve the effective response SLA hours, falling back to the parent category.
     */
    public function resolvedResponseSlaHours(): ?int
    {
        return $this->response_sla_hours ?? $this->serviceCategory?->response_sla_hours;
    }

    /**
     * Resolve the effective resolution SLA hours, falling back to the parent category.
     */
    public function resolvedResolutionSlaHours(): ?int
    {
        return $this->resolution_sla_hours ?? $this->serviceCategory?->resolution_sla_hours;
    }
}
