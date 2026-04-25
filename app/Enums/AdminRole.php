<?php

namespace App\Enums;

enum AdminRole: string
{
    case Admins = 'Admins';
    case AccountingManagers = 'accountingManagers';
    case ServiceManagers = 'serviceManagers';
    case MarketingManagers = 'marketingManagers';
    case SalesAndLeasingManagers = 'salesAndLeasingManagers';
    case GateOfficers = 'gateOfficers';
}
