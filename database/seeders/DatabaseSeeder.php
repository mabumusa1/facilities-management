<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and permissions first
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // Seed reference data (lookups)
        $this->call([
            CountrySeeder::class,
            CurrencySeeder::class,
            CitySeeder::class,
            DistrictSeeder::class,
            UnitCategorySeeder::class,
            UnitTypeSeeder::class,
            FacilityCategorySeeder::class,
            AmenitySeeder::class,
            StatusSeeder::class,
        ]);

        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'contact_type' => 'admin',
            'manager_role' => 1,
        ]);
        $admin->assignRole('Admins');

        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
