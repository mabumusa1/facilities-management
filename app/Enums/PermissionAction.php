<?php

namespace App\Enums;

enum PermissionAction: string
{
    case View = 'VIEW';
    case Create = 'CREATE';
    case Update = 'UPDATE';
    case Approve = 'APPROVE';
    case Delete = 'DELETE';
    case Restore = 'RESTORE';
    case ForceDelete = 'FORCE_DELETE';
}
