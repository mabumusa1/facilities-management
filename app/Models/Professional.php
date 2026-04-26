<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use App\Concerns\HasBilingualName;
use App\Concerns\HasManagerScope;
use App\Enums\IdType;
use Database\Factories\ProfessionalFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Professional extends Model
{
    /** @use HasFactory<ProfessionalFactory> */
    use BelongsToAccountTenant, HasBilingualName, HasFactory, HasManagerScope;

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
        /**
         * Intentionally unrestricted: no FK path from professionals
         * to communities or buildings exists in the current schema.
         * All managers within the tenant see all professionals.
         */
        return $query;
    }

    protected $fillable = [
        'first_name',
        'first_name_ar',
        'last_name',
        'last_name_ar',
        'email',
        'phone_number',
        'national_phone_number',
        'phone_country_code',
        'national_id',
        'id_type',
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
            'id_type' => IdType::class,
        ];
    }

    /**
     * Locale-aware full name virtual attribute.
     */
    protected function name(): Attribute
    {
        return Attribute::get(function () {
            if (app()->getLocale() === 'ar') {
                $ar = trim(($this->first_name_ar ?? '').' '.($this->last_name_ar ?? ''));

                return $ar !== '' ? $ar : trim(($this->first_name ?? '').' '.($this->last_name ?? ''));
            }

            $en = trim(($this->first_name ?? '').' '.($this->last_name ?? ''));

            return $en !== '' ? $en : trim(($this->first_name_ar ?? '').' '.($this->last_name_ar ?? ''));
        });
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

    public function activities(): MorphMany
    {
        return $this->morphMany(ContactActivity::class, 'contact');
    }

    public function kycDocuments(): MorphMany
    {
        return $this->morphMany(ContactDocument::class, 'contact');
    }
}
