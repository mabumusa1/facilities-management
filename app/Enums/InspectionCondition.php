<?php

namespace App\Enums;

enum InspectionCondition: string
{
    case Excellent = 'excellent';
    case Good = 'good';
    case Fair = 'fair';
    case Poor = 'poor';

    public function label(): string
    {
        return match ($this) {
            self::Excellent => __('Excellent'),
            self::Good => __('Good'),
            self::Fair => __('Fair'),
            self::Poor => __('Poor'),
        };
    }
}
