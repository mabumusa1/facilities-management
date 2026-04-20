<?php

namespace App\Enums;

enum MarketplaceType: string
{
    case Rent = 'rent';
    case Sale = 'sale';
    case Both = 'both';
}
