<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasBilingualName
{
    protected function name(): Attribute
    {
        return Attribute::get(
            fn () => app()->getLocale() === 'ar'
                ? ($this->name_ar ?: $this->name_en)
                : ($this->name_en ?: $this->name_ar),
        );
    }
}
