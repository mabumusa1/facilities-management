<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormTemplate extends Model
{
    use BelongsToAccountTenant;

    protected $table = 'rf_form_templates';

    protected $fillable = [
        'name',
        'description',
        'request_category_id',
        'community_id',
        'building_id',
        'schema',
        'is_active',
        'account_tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'schema' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function requestCategory(): BelongsTo
    {
        return $this->belongsTo(RequestCategory::class, 'request_category_id');
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }
}
