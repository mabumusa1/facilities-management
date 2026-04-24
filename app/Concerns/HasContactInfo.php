<?php

namespace App\Concerns;

use App\Enums\Gender;
use App\Enums\IdType;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasContactInfo
{
    public function initializeHasContactInfo(): void
    {
        $this->mergeFillable([
            'first_name',
            'first_name_ar',
            'last_name',
            'last_name_ar',
            'email',
            'phone_number',
            'national_phone_number',
            'phone_country_code',
            'national_id',
            'nationality_id',
            'id_type',
            'gender',
            'georgian_birthdate',
            'image',
            'active',
            'last_active',
        ]);

        $this->mergeCasts([
            'gender' => Gender::class,
            'georgian_birthdate' => 'date',
            'active' => 'boolean',
            'last_active' => 'datetime',
            'id_type' => IdType::class,
        ]);
    }

    /**
     * Locale-aware full name virtual attribute.
     * Returns Arabic first+last when locale is 'ar' (falling back to English),
     * and English first+last otherwise (falling back to Arabic).
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
}
