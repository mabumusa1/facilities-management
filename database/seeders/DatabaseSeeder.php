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
        $this->call([
            CountrySeeder::class,
            CitySeeder::class,
            DistrictSeeder::class,
            CurrencySeeder::class,
            StatusSeeder::class,
            SettingSeeder::class,
            UnitCategorySeeder::class,
            UnitTypeSeeder::class,
            ManagerRoleSeeder::class,
            RequestCategorySeeder::class,
            FacilityCategorySeeder::class,
            LeadSourceSeeder::class,
            FeatureSeeder::class,
            AmenitySeeder::class,
            RolesSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
