<?php

namespace Database\Seeders;

use App\Enums\AdminRole;
use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            AdminRole::Admins,
            AdminRole::AccountingManagers,
            AdminRole::ServiceManagers,
        ];

        foreach ($roles as $index => $role) {
            $sequence = $index + 1;

            Admin::query()->firstOrCreate(
                ['email' => sprintf('admin-%s@demo.test', $role->value)],
                [
                    'first_name' => 'Admin'.$sequence,
                    'last_name' => 'Sample',
                    'phone_country_code' => '+966',
                    'phone_number' => sprintf('050700%04d', $sequence),
                    'role' => $role->value,
                    'active' => true,
                ],
            );
        }
    }
}
