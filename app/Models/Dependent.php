<?php

namespace App\Models;

use App\Concerns\HasBilingualName;
use Database\Factories\DependentFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Dependent extends Model
{
    /** @use HasFactory<DependentFactory> */
    use HasBilingualName, HasFactory;

    protected $table = 'rf_dependents';

    protected $fillable = [
        'dependable_type',
        'dependable_id',
        'first_name',
        'first_name_ar',
        'last_name',
        'last_name_ar',
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

    /**
     * Locale-aware full name virtual attribute.
     */
    protected function name(): Attribute
    {
        return Attribute::get(function () {
            if (app()->getLocale() === 'ar') {
                $ar = trim(($this->first_name_ar ?? '').' '.($this->last_name_ar ?? ''));

                return $ar !== '' ? $ar : trim(($this->first_name ?? '').' '.($this->last_name ?? ''));
            }

            $en = trim(($this->first_name ?? '').' '.($this->last_name ?? ''));

            return $en !== '' ? $en : trim(($this->first_name_ar ?? '').' '.($this->last_name_ar ?? ''));
        });
    }

    /** @return MorphTo<Model, $this> */
    public function dependable(): MorphTo
    {
        return $this->morphTo();
    }
}
