<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommonList extends Model
{
    protected $table = 'rf_common_lists';

    protected $fillable = [
        'name',
        'name_ar',
        'name_en',
        'type',
        'priority',
    ];

    protected $attributes = [
        'priority' => 1,
    ];
}
