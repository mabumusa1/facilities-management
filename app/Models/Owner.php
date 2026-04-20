<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use App\Concerns\HasContactInfo;
use Database\Factories\OwnerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Owner extends Model
{
    /** @use HasFactory<OwnerFactory> */
    use BelongsToAccountTenant, HasContactInfo, HasFactory, SoftDeletes;

    protected $table = 'rf_owners';

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
        'relation',
        'relation_key',
        'account_tenant_id',
    ];

    protected $attributes = [
        'active' => true,
    ];

    public function documents(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->where('collection', 'documents');
    }

    public function dependents(): MorphMany
    {
        return $this->morphMany(Dependent::class, 'dependable');
    }
}
