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
            FeatureFlagSeeder::class,
        ]);

        // Seed admin user
        $this->call([
            AdminUserSeeder::class,
        ]);
    }
}
