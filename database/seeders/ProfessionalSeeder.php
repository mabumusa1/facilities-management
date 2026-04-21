<?php

namespace Database\Seeders;

use App\Models\Professional;
use Illuminate\Database\Seeder;

class ProfessionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(1, 3) as $index) {
            Professional::query()->firstOrCreate(
                ['email' => sprintf('professional%02d@demo.test', $index)],
                [
                    'first_name' => 'Professional'.$index,
                    'last_name' => 'Sample',
                    'phone_country_code' => '+966',
                    'phone_number' => sprintf('050600%04d', $index),
                    'active' => true,
                ],
            );
        }
    }
}
