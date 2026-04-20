<?php

namespace App\Concerns;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasContactInfo
{
    public function initializeHasContactInfo(): void
    {
        $this->mergeFillable([
            'first_name',
            'last_name',
            'email',
            'phone_number',
            'national_phone_number',
            'phone_country_code',
            'national_id',
            'nationality_id',
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
        ]);
    }

    protected function name(): Attribute
    {
        return Attribute::get(
            fn () => trim($this->first_name.' '.$this->last_name),
        );
    }
}
