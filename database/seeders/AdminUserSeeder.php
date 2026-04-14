<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ContactType;
use App\Enums\ManagerRole;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder for creating the system admin user.
 *
 * The admin user is a system-wide admin with full access to all features.
 * Default credentials:
 * - Email: admin@example.com
 * - Password: password
 */
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create system admin user if it doesn't exist
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'password' => 'password', // Will be hashed by the User model's 'hashed' cast
                'email_verified_at' => now(),
                'contact_type' => ContactType::Admin,
                'manager_role' => ManagerRole::Admin,
                'is_all_communities' => true,
                'is_all_buildings' => true,
            ]
        );

        // Ensure admin has the Admins role
        if (! $admin->hasRole('Admins')) {
            $admin->assignRole('Admins');
        }

        $this->command->info('System admin user created/verified: '.$admin->email);
    }
}
