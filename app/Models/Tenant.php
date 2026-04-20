<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Spatie\Multitenancy\Models\Tenant as BaseTenant;

#[Fillable(['name', 'domain', 'database'])]
class Tenant extends BaseTenant
{
    //
}
