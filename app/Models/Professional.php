<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use App\Concerns\HasManagerScope;
use Database\Factories\ProfessionalFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Professional extends Model
{
    /** @use HasFactory<ProfessionalFactory> */
    use BelongsToAccountTenant, HasFactory, HasManagerScope;

    protected $table = 'rf_professionals';

    /**
     * Professionals: no direct community/service-type FK exists in the schema.
     * The service_type path via professional_subcategories → rf_request_subcategories
     * → service_manager_type_id does not exist in the current schema.
     * Until a schema linkage is added, all managers see all professionals within tenant.
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeForManager(Builder $query, User $user): Builder
    {
        // intentionally unrestricted — no FK path available in schema
        return $query;
    }

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'phone_country_code',
        'national_id',
        'image',
        'active',
        'account_tenant_id',
    ];

    protected $attributes = [
        'active' => true,
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    /** @return BelongsToMany<RequestSubcategory, $this> */
    public function subcategories(): BelongsToMany
    {
        return $this->belongsToMany(RequestSubcategory::class, 'professional_subcategories', 'professional_id', 'subcategory_id');
    }

    /** @return HasMany<Request, $this> */
    public function requests(): HasMany
    {
        return $this->hasMany(Request::class, 'professional_id');
    }
}
