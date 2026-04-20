<?php

namespace App\Models;

use App\Concerns\HasBilingualName;
use Database\Factories\StatusFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    /** @use HasFactory<StatusFactory> */
    use HasBilingualName, HasFactory;

    protected $table = 'rf_statuses';

    protected $fillable = [
        'name',
        'name_ar',
        'name_en',
        'priority',
        'type',
    ];

    protected $attributes = [
        'priority' => 1,
    ];
}
