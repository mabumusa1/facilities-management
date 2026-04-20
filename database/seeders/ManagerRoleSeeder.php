<?php

namespace Database\Seeders;

use App\Models\ManagerRole;
use Illuminate\Database\Seeder;

class ManagerRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['role' => 'Admins', 'name_ar' => 'مدراء', 'name_en' => 'Admins'],
            ['role' => 'accountingManagers', 'name_ar' => 'مدراء المحاسبة', 'name_en' => 'Accounting Managers'],
            ['role' => 'serviceManagers', 'name_ar' => 'مدراء الخدمات', 'name_en' => 'Service Managers'],
            ['role' => 'marketingManagers', 'name_ar' => 'مدراء التسويق', 'name_en' => 'Marketing Managers'],
            ['role' => 'salesAndLeasingManagers', 'name_ar' => 'مدراء المبيعات والتأجير', 'name_en' => 'Sales & Leasing Managers'],
        ];

        foreach ($roles as $role) {
            ManagerRole::updateOrCreate(['role' => $role['role']], $role);
        }
    }
}
