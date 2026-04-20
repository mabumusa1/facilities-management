<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class LeaseUnit extends Pivot
{
    protected $table = 'lease_units';

    public $incrementing = true;

    protected function casts(): array
    {
        return [
            'annual_rental_amount' => 'decimal:2',
            'net_area' => 'decimal:2',
            'meter_cost' => 'decimal:2',
        ];
    }
}
