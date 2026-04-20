<?php

namespace App\Models;

use App\Concerns\HasBilingualName;
use Database\Factories\LeadSourceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadSource extends Model
{
    /** @use HasFactory<LeadSourceFactory> */
    use HasBilingualName, HasFactory;

    protected $table = 'rf_lead_sources';

    protected $fillable = [
        'name',
        'name_ar',
        'name_en',
    ];
}
