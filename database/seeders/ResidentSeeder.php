<?php

namespace Database\Seeders;

use App\Models\Resident;
use Illuminate\Database\Seeder;

class ResidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(1, 10) as $index) {
            Resident::query()->firstOrCreate(
                ['email' => sprintf('resident%02d@demo.test', $index)],
                [
                    'first_name' => 'Resident'.$index,
                    'last_name' => 'Sample',
                    'phone_country_code' => '+966',
                    'phone_number' => sprintf('050800%04d', $index),
                    'active' => true,
                    'accepted_invite' => true,
                ],
            );
        }
    }
}
