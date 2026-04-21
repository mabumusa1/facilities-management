<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasBilingualName
{
    public static function bootHasBilingualName(): void
    {
        static::creating(function ($model): void {
            if (! in_array('name', $model->getFillable())) {
                return;
            }

            if (empty($model->getAttributes()['name'] ?? null)) {
                $model->setAttribute('name', $model->getAttributes()['name_en'] ?? $model->getAttributes()['name_ar'] ?? '');
            }
        });

        static::updating(function ($model): void {
            if (! in_array('name', $model->getFillable())) {
                return;
            }

            if ($model->isDirty('name_en') || $model->isDirty('name_ar')) {
                $model->setAttribute('name', $model->getAttributes()['name_en'] ?? $model->getAttributes()['name_ar'] ?? $model->getAttributes()['name']);
            }
        });
    }

    protected function name(): Attribute
    {
        return Attribute::get(
            fn () => app()->getLocale() === 'ar'
                ? ($this->name_ar ?: $this->name_en)
                : ($this->name_en ?: $this->name_ar),
        );
    }
}
