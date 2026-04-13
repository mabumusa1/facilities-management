<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Permission actions that can be performed on subjects.
 */
enum PermissionAction: string
{
    case View = 'view';
    case Create = 'create';
    case Edit = 'edit';
    case Delete = 'delete';
    case Manage = 'manage';
    case Close = 'close';
    case Assign = 'assign';

    /**
     * Get the display label for the action.
     */
    public function label(): string
    {
        return match ($this) {
            self::View => 'View',
            self::Create => 'Create',
            self::Edit => 'Edit',
            self::Delete => 'Delete',
            self::Manage => 'Manage',
            self::Close => 'Close',
            self::Assign => 'Assign',
        };
    }

    /**
     * Get the Arabic label for the action.
     */
    public function labelAr(): string
    {
        return match ($this) {
            self::View => 'عرض',
            self::Create => 'إنشاء',
            self::Edit => 'تعديل',
            self::Delete => 'حذف',
            self::Manage => 'إدارة',
            self::Close => 'إغلاق',
            self::Assign => 'تعيين',
        };
    }

    /**
     * Check if this is a destructive action.
     */
    public function isDestructive(): bool
    {
        return $this === self::Delete;
    }

    /**
     * Check if this is a read-only action.
     */
    public function isReadOnly(): bool
    {
        return $this === self::View;
    }

    /**
     * Get all CRUD actions.
     *
     * @return array<self>
     */
    public static function crud(): array
    {
        return [
            self::View,
            self::Create,
            self::Edit,
            self::Delete,
        ];
    }

    /**
     * Get all values as an array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
