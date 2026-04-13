<?php

declare(strict_types=1);

namespace App\Enums;

enum ServiceManagerType: int
{
    case HomeServiceRequests = 1;
    case CommonAreaServiceRequests = 2;
    case VisitorAccessRequests = 3;
    case FacilityBookingRequests = 5;

    /**
     * Get the display label for the service manager type.
     */
    public function label(): string
    {
        return match ($this) {
            self::HomeServiceRequests => 'Home Service Requests',
            self::CommonAreaServiceRequests => 'Common Area Service Requests',
            self::VisitorAccessRequests => 'Visitor Access Requests',
            self::FacilityBookingRequests => 'Facility Booking Requests',
        };
    }

    /**
     * Get the Arabic label for the service manager type.
     */
    public function labelAr(): string
    {
        return match ($this) {
            self::HomeServiceRequests => 'طلبات خدمات المنازل',
            self::CommonAreaServiceRequests => 'طلبات خدمات المناطق المشتركة',
            self::VisitorAccessRequests => 'طلبات دخول الزوار',
            self::FacilityBookingRequests => 'طلبات الحجوزات للمرافق',
        };
    }

    /**
     * Get all values as an array.
     *
     * @return array<int>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
