<?php

namespace Database\Seeders;

use App\Models\ManagerRole;
use Illuminate\Database\Seeder;

class ManagerRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['id' => 1, 'role' => 'Admins', 'name_ar' => 'مدير', 'name_en' => 'Admin'],
            ['id' => 2, 'role' => 'accountingManagers', 'name_ar' => 'مدير حسابات', 'name_en' => 'Accounting Manager'],
            ['id' => 3, 'role' => 'serviceManagers', 'name_ar' => 'مدير خدمات', 'name_en' => 'Service Manager'],
            ['id' => 4, 'role' => 'marketingManagers', 'name_ar' => 'مدير تسويق', 'name_en' => 'Marketing Manager'],
            ['id' => 5, 'role' => 'salesAndLeasingManagers', 'name_ar' => 'مدير مبيعات وتأجير', 'name_en' => 'Sales & Leasing Manager'],
        ];

        foreach ($roles as $role) {
            ManagerRole::updateOrCreate(['role' => $role['role']], $role);
        }
    }
}
