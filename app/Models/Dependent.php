<?php

namespace App\Models;

use Database\Factories\DependentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Dependent extends Model
{
    /** @use HasFactory<DependentFactory> */
    use HasFactory;

    protected $table = 'rf_dependents';

    protected $fillable = [
        'dependable_type',
        'dependable_id',
        'first_name',
        'last_name',
        'phone_number',
        'phone_country_code',
        'email',
        'national_id',
        'gender',
        'birthdate',
        'relationship',
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
        ];
    }

    /** @return MorphTo<Model, $this> */
    public function dependable(): MorphTo
    {
        return $this->morphTo();
    }
}
