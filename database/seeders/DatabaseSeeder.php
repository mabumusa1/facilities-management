<?php

namespace Database\Seeders;

use App\Enums\RolesEnum;
use App\Models\AccountMembership;
use App\Models\Tenant;
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
            CommonListSeeder::class,
            RolesSeeder::class,
            SubscriptionPlanSeeder::class,
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $tenant = Tenant::create([
            'name' => 'Demo Account',
        ]);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::ACCOUNT_ADMINS->value,
        ]);

        $user->assignRole(RolesEnum::ACCOUNT_ADMINS);

        $tenant->makeCurrent();

        $this->call([
            CommunitySeeder::class,
            BuildingSeeder::class,
            OwnerSeeder::class,
            ResidentSeeder::class,
            AdminSeeder::class,
            ProfessionalSeeder::class,
            UnitSeeder::class,
            LeaseSeeder::class,
            TransactionSeeder::class,
            RequestSubcategorySeeder::class,
            RequestSeeder::class,
            AnnouncementSeeder::class,
            FacilitySeeder::class,
            FacilityBookingSeeder::class,
        ]);
    }
}
