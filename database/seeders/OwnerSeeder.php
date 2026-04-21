<?php

namespace Database\Seeders;

use App\Models\Owner;
use Illuminate\Database\Seeder;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(1, 5) as $index) {
            Owner::query()->firstOrCreate(
                ['email' => sprintf('owner%02d@demo.test', $index)],
                [
                    'first_name' => 'Owner'.$index,
                    'last_name' => 'Sample',
                    'phone_country_code' => '+966',
                    'phone_number' => sprintf('050900%04d', $index),
                    'active' => true,
                ],
            );
        }
    }
}
