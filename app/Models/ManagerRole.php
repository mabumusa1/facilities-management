<?php

namespace App\Models;

use App\Concerns\HasBilingualName;
use Database\Factories\ManagerRoleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagerRole extends Model
{
    /** @use HasFactory<ManagerRoleFactory> */
    use HasBilingualName, HasFactory;

    protected $table = 'rf_manager_roles';

    protected $fillable = [
        'role',
        'name_ar',
        'name_en',
    ];
}
