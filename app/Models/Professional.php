<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Database\Factories\ProfessionalFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professional extends Model
{
    /** @use HasFactory<ProfessionalFactory> */
    use BelongsToAccountTenant, HasFactory;

    protected $table = 'rf_professionals';

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
}
