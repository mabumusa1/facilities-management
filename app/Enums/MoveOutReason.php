<?php

namespace App\Enums;

enum MoveOutReason: string
{
    case EndOfLease = 'end_of_lease';
    case EarlyTenant = 'early_tenant';
    case EarlyManagement = 'early_management';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::EndOfLease => __('End of lease term'),
            self::EarlyTenant => __('Early termination by tenant'),
            self::EarlyManagement => __('Early termination by management'),
            self::Other => __('Other'),
        };
    }
}
