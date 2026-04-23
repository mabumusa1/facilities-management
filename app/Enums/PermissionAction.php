<?php

namespace App\Enums;

enum PermissionAction: string
{
    case View = 'VIEW';
    case Create = 'CREATE';
    case Update = 'UPDATE';
    case Delete = 'DELETE';
    case Export = 'EXPORT';
    case Import = 'IMPORT';
}
