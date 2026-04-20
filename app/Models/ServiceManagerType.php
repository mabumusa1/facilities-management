<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ServiceManagerType extends Model
{
    protected $table = 'rf_service_manager_types';

    protected $fillable = [
        'name',
        'name_ar',
        'name_en',
    ];

    /** @return BelongsToMany<Admin, $this> */
    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class, 'admin_service_manager_types');
    }
}
