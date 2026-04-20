<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use App\Enums\AdminRole;
use Database\Factories\AdminFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    /** @use HasFactory<AdminFactory> */
    use BelongsToAccountTenant, HasFactory;

    protected $table = 'rf_admins';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'full_phone_number',
        'phone_country_code',
        'national_id',
        'nationality_id',
        'gender',
        'georgian_birthdate',
        'image',
        'role',
        'active',
        'last_login_at',
        'account_tenant_id',
    ];

    protected $attributes = [
        'active' => true,
    ];

    protected function casts(): array
    {
        return [
            'role' => AdminRole::class,
            'active' => 'boolean',
            'georgian_birthdate' => 'date',
            'last_login_at' => 'datetime',
        ];
    }
}
