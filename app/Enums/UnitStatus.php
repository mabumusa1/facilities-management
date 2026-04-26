<?php

namespace App\Enums;

enum UnitStatus: string
{
    case Available = 'available';
    case UnderMaintenance = 'under_maintenance';
    case Occupied = 'occupied';
    case OffPlan = 'off_plan';

    public function label(): string
    {
        return match ($this) {
            self::Available => __('Available'),
            self::UnderMaintenance => __('Under Maintenance'),
            self::Occupied => __('Occupied'),
            self::OffPlan => __('Off Plan'),
        };
    }

    /**
     * @return array<string, string[]>
     */
    public static function allowedTransitions(): array
    {
        return [
            self::Available->value => [
                self::UnderMaintenance->value,
                self::Occupied->value,
                self::OffPlan->value,
            ],
            self::UnderMaintenance->value => [
                self::Available->value,
            ],
            self::Occupied->value => [
                self::Available->value,
            ],
            self::OffPlan->value => [
                self::Available->value,
            ],
        ];
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array(
            $target->value,
            self::allowedTransitions()[$this->value] ?? [],
            true
        );
    }

    public static function default(): self
    {
        return self::Available;
    }
}
