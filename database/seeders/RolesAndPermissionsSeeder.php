<?php

namespace Database\Seeders;

use App\Services\PermissionGenerator;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Use PermissionGenerator to sync permissions and roles
        $generator = new PermissionGenerator;

        // Create all permissions
        $generator->syncPermissionsToDatabase();

        // Create all roles and assign permissions
        $generator->syncRolesToDatabase();
    }
}
