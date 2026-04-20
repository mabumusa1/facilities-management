<?php

namespace App\Enums;

enum LeaseEscalationType: string
{
    case Fixed = 'fixed';
    case Percentage = 'percentage';
}
