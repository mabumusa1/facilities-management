<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use App\Concerns\HasContactInfo;
use Database\Factories\ResidentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resident extends Model
{
    /** @use HasFactory<ResidentFactory> */
    use BelongsToAccountTenant, HasContactInfo, HasFactory, SoftDeletes;

    protected $table = 'rf_tenants';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'national_phone_number',
        'phone_country_code',
        'national_id',
        'nationality_id',
        'gender',
        'georgian_birthdate',
        'image',
        'active',
        'last_active',
        'source_id',
        'accepted_invite',
        'relation',
        'relation_key',
        'account_tenant_id',
    ];

    protected $attributes = [
        'active' => true,
        'accepted_invite' => false,
    ];

    protected function casts(): array
    {
        return [
            'accepted_invite' => 'boolean',
        ];
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->where('collection', 'documents');
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class, 'tenant_id');
    }

    public function dependents(): MorphMany
    {
        return $this->morphMany(Dependent::class, 'dependable');
    }
}
