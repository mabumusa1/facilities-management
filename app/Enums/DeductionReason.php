<?php

namespace App\Enums;

enum DeductionReason: string
{
    case Damage = 'damage';
    case Cleaning = 'cleaning';
    case UnpaidRent = 'unpaid_rent';
    case Utility = 'utility';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Damage => __('Damage'),
            self::Cleaning => __('Cleaning'),
            self::UnpaidRent => __('Unpaid Rent'),
            self::Utility => __('Utility'),
            self::Other => __('Other'),
        };
    }
}
