<?php

namespace Database\Seeders;

use App\Enums\AdminRole;
use App\Enums\RolesEnum;
use App\Models\AccountMembership;
use App\Models\Admin;
use App\Models\Amenity;
use App\Models\Announcement;
use App\Models\Building;
use App\Models\City;
use App\Models\Community;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Dependent;
use App\Models\District;
use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\FacilityCategory;
use App\Models\Feature;
use App\Models\Lease;
use App\Models\MarketplaceOffer;
use App\Models\MarketplaceUnit;
use App\Models\MarketplaceVisit;
use App\Models\Owner;
use App\Models\Payment;
use App\Models\Professional;
use App\Models\Request as ServiceRequest;
use App\Models\RequestSubcategory;
use App\Models\Resident;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\Unit;
use App\Models\UnitCategory;
use App\Models\UnitType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DemoAccountSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedReferenceData();

        $tenant = $this->seedDemoUsersAndTenant();
        $tenant->makeCurrent();

        $this->seedExtendedDomainData();
        $this->attachShowcaseRelations();
        $this->seedMarketplaceData();
        $this->seedPaymentData();
        $this->seedDependents();
    }

    private function seedReferenceData(): void
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
    }

    private function seedDemoUsersAndTenant(): Tenant
    {
        $tenant = Tenant::query()->firstOrCreate([
            'name' => 'Demo Account',
        ]);

        $this->seedPortalUser(
            tenant: $tenant,
            name: 'Test User',
            email: 'test@example.com',
            role: RolesEnum::ACCOUNT_ADMINS->value,
        );

        $showcaseMembers = [
            ['name' => 'Demo Admin', 'email' => 'admin@demo.test', 'role' => RolesEnum::ADMINS->value],
            ['name' => 'Demo Manager', 'email' => 'manager@demo.test', 'role' => RolesEnum::MANAGERS->value],
            ['name' => 'Demo Tenant', 'email' => 'tenant@demo.test', 'role' => RolesEnum::TENANTS->value],
            ['name' => 'Demo Professional', 'email' => 'professional@demo.test', 'role' => RolesEnum::PROFESSIONALS->value],
        ];

        foreach ($showcaseMembers as $member) {
            $this->seedPortalUser(
                tenant: $tenant,
                name: $member['name'],
                email: $member['email'],
                role: $member['role'],
            );
        }

        return $tenant;
    }

    private function seedPortalUser(Tenant $tenant, string $name, string $email, string $role): User
    {
        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => 'password',
                'email_verified_at' => now(),
            ],
        );

        AccountMembership::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'account_tenant_id' => $tenant->id,
            ],
            [
                'role' => $role,
            ],
        );

        $user->syncRoles([$role]);

        return $user;
    }

    private function seedExtendedDomainData(): void
    {
        $this->topUpCommunities(5);
        $this->topUpBuildings(12);
        $this->topUpOwners(12);
        $this->topUpResidents(24);
        $this->topUpAdmins();
        $this->topUpProfessionals(8);
        $this->topUpUnits(45);
        $this->topUpLeases(20);
        $this->topUpTransactions(60);
        $this->topUpRequests(35);
        $this->topUpAnnouncements(12);
        $this->topUpFacilities(10);
        $this->topUpFacilityBookings(30);
    }

    private function topUpCommunities(int $targetCount): void
    {
        $missingCount = $targetCount - Community::query()->count();

        if ($missingCount <= 0) {
            return;
        }

        $countryIds = Country::query()->pluck('id');
        $currencyIds = Currency::query()->pluck('id');
        $cityIds = City::query()->pluck('id');
        $districtIds = District::query()->pluck('id');

        if ($countryIds->isEmpty() || $currencyIds->isEmpty() || $cityIds->isEmpty() || $districtIds->isEmpty()) {
            return;
        }

        for ($index = 0; $index < $missingCount; $index++) {
            Community::factory()->create([
                'country_id' => $countryIds->random(),
                'currency_id' => $currencyIds->random(),
                'city_id' => $cityIds->random(),
                'district_id' => $districtIds->random(),
                'is_market_place' => random_int(0, 1) === 1,
                'is_buy' => random_int(0, 1) === 1,
                'is_off_plan_sale' => random_int(0, 1) === 1,
            ]);
        }
    }

    private function topUpBuildings(int $targetCount): void
    {
        $missingCount = $targetCount - Building::query()->count();

        if ($missingCount <= 0) {
            return;
        }

        $communities = Community::query()->get(['id', 'city_id', 'district_id']);

        if ($communities->isEmpty()) {
            return;
        }

        for ($index = 0; $index < $missingCount; $index++) {
            $community = $communities->random();

            Building::factory()->create([
                'rf_community_id' => $community->id,
                'city_id' => $community->city_id,
                'district_id' => $community->district_id,
            ]);
        }
    }

    private function topUpOwners(int $targetCount): void
    {
        foreach (range(1, $targetCount) as $index) {
            Owner::query()->firstOrCreate(
                ['email' => sprintf('owner%02d@demo.test', $index)],
                [
                    'first_name' => 'Owner'.$index,
                    'last_name' => 'Sample',
                    'phone_country_code' => '+966',
                    'phone_number' => sprintf('050900%04d', $index),
                    'active' => true,
                ],
            );
        }
    }

    private function topUpResidents(int $targetCount): void
    {
        foreach (range(1, $targetCount) as $index) {
            Resident::query()->firstOrCreate(
                ['email' => sprintf('resident%02d@demo.test', $index)],
                [
                    'first_name' => 'Resident'.$index,
                    'last_name' => 'Sample',
                    'phone_country_code' => '+966',
                    'phone_number' => sprintf('050800%04d', $index),
                    'active' => true,
                    'accepted_invite' => true,
                ],
            );
        }
    }

    private function topUpAdmins(): void
    {
        foreach (array_values(AdminRole::cases()) as $index => $role) {
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

    private function topUpProfessionals(int $targetCount): void
    {
        foreach (range(1, $targetCount) as $index) {
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

    private function topUpUnits(int $targetCount): void
    {
        $missingCount = $targetCount - Unit::query()->count();

        if ($missingCount <= 0) {
            return;
        }

        $buildings = Building::query()->get(['id', 'rf_community_id', 'city_id', 'district_id']);
        $unitTypes = UnitType::query()->get(['id', 'category_id']);
        $statusIds = Status::query()->where('type', 'unit')->pluck('id');
        $ownerIds = Owner::query()->pluck('id');
        $residentIds = Resident::query()->pluck('id');

        if ($buildings->isEmpty() || $unitTypes->isEmpty() || $statusIds->isEmpty()) {
            return;
        }

        for ($index = 0; $index < $missingCount; $index++) {
            $building = $buildings->random();
            $unitType = $unitTypes->random();

            Unit::factory()->create([
                'rf_community_id' => $building->rf_community_id,
                'rf_building_id' => $building->id,
                'city_id' => $building->city_id,
                'district_id' => $building->district_id,
                'category_id' => $unitType->category_id,
                'type_id' => $unitType->id,
                'status_id' => $statusIds->random(),
                'owner_id' => $ownerIds->isNotEmpty() && random_int(0, 1) === 1 ? $ownerIds->random() : null,
                'tenant_id' => $residentIds->isNotEmpty() && random_int(0, 2) === 0 ? $residentIds->random() : null,
                'is_market_place' => random_int(0, 1) === 1,
                'is_buy' => random_int(0, 1) === 1,
                'is_off_plan_sale' => random_int(0, 1) === 1,
            ]);
        }

        $ownersWithoutUnits = Owner::query()
            ->doesntHave('units')
            ->pluck('id');

        if ($ownersWithoutUnits->isEmpty()) {
            return;
        }

        $unitsWithoutOwner = Unit::query()
            ->whereNull('owner_id')
            ->orderBy('id')
            ->take($ownersWithoutUnits->count())
            ->get(['id']);

        foreach ($ownersWithoutUnits as $position => $ownerId) {
            $unit = $unitsWithoutOwner->get($position);

            if ($unit === null) {
                break;
            }

            Unit::query()
                ->whereKey($unit->id)
                ->update(['owner_id' => $ownerId]);
        }
    }

    private function topUpLeases(int $targetCount): void
    {
        $missingCount = $targetCount - Lease::query()->count();

        if ($missingCount <= 0) {
            return;
        }

        $residentIds = Resident::query()->pluck('id');
        $adminIds = Admin::query()->pluck('id');
        $statusIds = Status::query()->where('type', 'lease')->pluck('id');
        $unitIds = Unit::query()->pluck('id');
        $unitCategoryIds = UnitCategory::query()->pluck('id');
        $rentalContractTypeIds = Setting::query()->where('type', 'rental_contract_type')->pluck('id');
        $paymentScheduleIds = Setting::query()->where('type', 'payment_schedule')->pluck('id');

        if (
            $residentIds->isEmpty()
            || $adminIds->isEmpty()
            || $statusIds->isEmpty()
            || $unitIds->isEmpty()
            || $unitCategoryIds->isEmpty()
            || $rentalContractTypeIds->isEmpty()
            || $paymentScheduleIds->isEmpty()
        ) {
            return;
        }

        for ($index = 0; $index < $missingCount; $index++) {
            $startDate = now()->subMonths(random_int(1, 18))->startOfDay();
            $endDate = (clone $startDate)->addYear();

            $lease = Lease::factory()->create([
                'tenant_id' => $residentIds->random(),
                'status_id' => $statusIds->random(),
                'lease_unit_type_id' => $unitCategoryIds->random(),
                'rental_contract_type_id' => $rentalContractTypeIds->random(),
                'payment_schedule_id' => $paymentScheduleIds->random(),
                'created_by_id' => $adminIds->random(),
                'deal_owner_id' => $adminIds->random(),
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'handover_date' => $startDate->toDateString(),
                'tenant_type' => random_int(0, 1) === 1 ? 'individual' : 'company',
                'rental_type' => random_int(0, 1) === 1 ? 'total' : 'detailed',
                'rental_total_amount' => random_int(80000, 260000),
                'security_deposit_amount' => random_int(6000, 24000),
            ]);

            $lease->units()->syncWithoutDetaching([
                $unitIds->random() => [
                    'rental_annual_type' => 'annual',
                    'annual_rental_amount' => $lease->rental_total_amount,
                    'net_area' => random_int(60, 220),
                    'meter_cost' => random_int(400, 1200),
                ],
            ]);
        }
    }

    private function topUpTransactions(int $targetCount): void
    {
        $missingCount = $targetCount - Transaction::query()->count();

        if ($missingCount <= 0) {
            return;
        }

        [$category, $type] = $this->ensureTransactionSettings();

        $statusIds = Status::query()->where('type', 'invoice')->pluck('id');
        $leases = Lease::query()->with('units:id')->get(['id']);

        if ($statusIds->isEmpty() || $leases->isEmpty()) {
            return;
        }

        for ($index = 0; $index < $missingCount; $index++) {
            $lease = $leases->random();
            $unitId = $lease->units->isNotEmpty() ? $lease->units->random()->id : null;
            $amount = random_int(2000, 26000);
            $isPaid = random_int(0, 2) === 0;

            Transaction::factory()->create([
                'lease_id' => $lease->id,
                'unit_id' => $unitId,
                'category_id' => $category->id,
                'type_id' => $type->id,
                'status_id' => $statusIds->random(),
                'amount' => $amount,
                'tax_amount' => round($amount * 0.15, 2),
                'rental_amount' => $amount,
                'due_on' => now()->addDays(random_int(-30, 45))->toDateString(),
                'vat' => 15,
                'is_paid' => $isPaid,
            ]);
        }
    }

    /**
     * @return array{0: Setting, 1: Setting}
     */
    private function ensureTransactionSettings(): array
    {
        $category = Setting::query()->updateOrCreate(
            ['id' => 1001],
            [
                'type' => 'transaction_category',
                'name' => 'Rent',
                'name_ar' => 'Rent',
                'name_en' => 'Rent',
                'parent_id' => null,
            ],
        );

        $type = Setting::query()->updateOrCreate(
            ['id' => 1002],
            [
                'type' => 'transaction_type',
                'name' => 'Invoice',
                'name_ar' => 'Invoice',
                'name_en' => 'Invoice',
                'parent_id' => null,
            ],
        );

        return [$category, $type];
    }

    private function topUpRequests(int $targetCount): void
    {
        $missingCount = $targetCount - ServiceRequest::query()->count();

        if ($missingCount <= 0) {
            return;
        }

        $subcategories = RequestSubcategory::query()->get(['id', 'category_id']);
        $statusIds = Status::query()->where('type', 'request')->pluck('id');
        $units = Unit::query()->get(['id', 'rf_community_id', 'rf_building_id']);
        $residentIds = Resident::query()->pluck('id');
        $professionalIds = Professional::query()->pluck('id');
        $priorities = ['low', 'medium', 'high', 'urgent'];

        if ($subcategories->isEmpty() || $statusIds->isEmpty() || $units->isEmpty() || $residentIds->isEmpty()) {
            return;
        }

        $nextNumber = (int) ServiceRequest::query()->max('id');

        for ($index = 0; $index < $missingCount; $index++) {
            $subcategory = $subcategories->random();
            $unit = $units->random();

            ServiceRequest::factory()->create([
                'category_id' => $subcategory->category_id,
                'subcategory_id' => $subcategory->id,
                'status_id' => $statusIds->random(),
                'requester_type' => Resident::class,
                'requester_id' => $residentIds->random(),
                'unit_id' => $unit->id,
                'community_id' => $unit->rf_community_id,
                'building_id' => $unit->rf_building_id,
                'professional_id' => $professionalIds->isNotEmpty() && random_int(0, 1) === 1 ? $professionalIds->random() : null,
                'request_code' => sprintf('REQ-%05d', $nextNumber + $index + 1),
                'priority' => $priorities[array_rand($priorities)],
            ]);
        }
    }

    private function topUpAnnouncements(int $targetCount): void
    {
        $missingCount = $targetCount - Announcement::query()->count();

        if ($missingCount <= 0) {
            return;
        }

        $communities = Community::query()->get(['id']);
        $buildings = Building::query()->get(['id', 'rf_community_id']);

        if ($communities->isEmpty()) {
            return;
        }

        for ($index = 0; $index < $missingCount; $index++) {
            $community = $communities->random();
            $communityBuildings = $buildings->where('rf_community_id', $community->id);
            $buildingId = $communityBuildings->isNotEmpty() ? $communityBuildings->random()->id : null;
            $isPublished = random_int(0, 1) === 1;

            Announcement::factory()->create([
                'community_id' => $community->id,
                'building_id' => $buildingId,
                'status' => $isPublished,
                'published_at' => $isPublished ? now()->subDays(random_int(1, 14)) : null,
            ]);
        }
    }

    private function topUpFacilities(int $targetCount): void
    {
        $missingCount = $targetCount - Facility::query()->count();

        if ($missingCount <= 0) {
            return;
        }

        $categoryIds = FacilityCategory::query()->pluck('id');
        $communityIds = Community::query()->pluck('id');

        if ($categoryIds->isEmpty() || $communityIds->isEmpty()) {
            return;
        }

        for ($index = 0; $index < $missingCount; $index++) {
            Facility::factory()->create([
                'category_id' => $categoryIds->random(),
                'community_id' => $communityIds->random(),
                'is_active' => random_int(0, 1) === 1,
                'requires_approval' => random_int(0, 1) === 1,
            ]);
        }
    }

    private function topUpFacilityBookings(int $targetCount): void
    {
        $missingCount = $targetCount - FacilityBooking::query()->count();

        if ($missingCount <= 0) {
            return;
        }

        $facilityIds = Facility::query()->pluck('id');
        $residentIds = Resident::query()->pluck('id');
        $statusIds = Status::query()->where('type', 'facility_booking')->pluck('id');

        if ($facilityIds->isEmpty() || $residentIds->isEmpty() || $statusIds->isEmpty()) {
            return;
        }

        for ($index = 0; $index < $missingCount; $index++) {
            $startHour = random_int(8, 18);
            $endHour = min($startHour + 2, 22);

            FacilityBooking::factory()->create([
                'facility_id' => $facilityIds->random(),
                'status_id' => $statusIds->random(),
                'booker_type' => Resident::class,
                'booker_id' => $residentIds->random(),
                'booking_date' => now()->addDays(random_int(1, 21))->toDateString(),
                'start_time' => sprintf('%02d:00', $startHour),
                'end_time' => sprintf('%02d:00', $endHour),
                'number_of_guests' => random_int(1, 8),
            ]);
        }
    }

    private function attachShowcaseRelations(): void
    {
        $this->attachAmenitiesToCommunities();
        $this->attachFeaturesToUnits();
    }

    private function attachAmenitiesToCommunities(): void
    {
        $amenityIds = Amenity::query()->pluck('id');

        if ($amenityIds->isEmpty()) {
            return;
        }

        foreach (Community::query()->get(['id']) as $community) {
            $attachCount = min(4, $amenityIds->count());

            if ($attachCount <= 0) {
                continue;
            }

            $community->amenities()->syncWithoutDetaching(
                $amenityIds->random($attachCount)->all(),
            );
        }
    }

    private function attachFeaturesToUnits(): void
    {
        $residentialFeatureIds = Feature::query()->where('type', 'residential')->pluck('id');
        $commercialFeatureIds = Feature::query()->where('type', 'commercial')->pluck('id');

        foreach (Unit::query()->get(['id', 'category_id']) as $unit) {
            $featurePool = $unit->category_id === 3
                ? $commercialFeatureIds
                : $residentialFeatureIds;

            if ($featurePool->isEmpty()) {
                continue;
            }

            $unit->features()->syncWithoutDetaching(
                $this->randomFeatureIds($featurePool)->all(),
            );
        }
    }

    /**
     * @param  Collection<int, int>  $featureIds
     * @return Collection<int, int>
     */
    private function randomFeatureIds(Collection $featureIds): Collection
    {
        $maxAttachCount = min(4, $featureIds->count());

        if ($maxAttachCount <= 1) {
            return collect($featureIds->random(1)->all());
        }

        $attachCount = random_int(2, $maxAttachCount);

        return collect($featureIds->random($attachCount)->all());
    }

    private function seedMarketplaceData(): void
    {
        $this->topUpMarketplaceListings(12);
        $this->topUpMarketplaceOffers(10);
        $this->topUpMarketplaceVisits(15);
    }

    private function topUpMarketplaceListings(int $targetCount): void
    {
        $missingCount = $targetCount - MarketplaceUnit::query()->count();

        if ($missingCount <= 0) {
            return;
        }

        $marketplaceUnitIds = Unit::query()->where('is_market_place', true)->pluck('id');

        if ($marketplaceUnitIds->count() < $targetCount) {
            $additionalCount = $targetCount - $marketplaceUnitIds->count();
            $unitsToEnable = Unit::query()
                ->where('is_market_place', false)
                ->inRandomOrder()
                ->take($additionalCount)
                ->pluck('id');

            if ($unitsToEnable->isNotEmpty()) {
                Unit::query()->whereIn('id', $unitsToEnable)->update(['is_market_place' => true]);
            }
        }

        $availableUnits = Unit::query()
            ->where('is_market_place', true)
            ->whereDoesntHave('marketplaceListings')
            ->get(['id']);

        if ($availableUnits->isEmpty()) {
            return;
        }

        $createCount = min($missingCount, $availableUnits->count());

        foreach ($availableUnits->take($createCount) as $unit) {
            MarketplaceUnit::factory()->create([
                'unit_id' => $unit->id,
                'listing_type' => random_int(0, 1) === 1 ? 'sale' : 'rent',
                'price' => random_int(120000, 900000),
                'is_active' => random_int(0, 4) !== 0,
            ]);
        }
    }

    private function topUpMarketplaceOffers(int $targetCount): void
    {
        $missingCount = $targetCount - MarketplaceOffer::query()->count();

        if ($missingCount <= 0) {
            return;
        }

        $unitIds = MarketplaceUnit::query()->pluck('unit_id')->unique();

        if ($unitIds->isEmpty()) {
            return;
        }

        for ($index = 0; $index < $missingCount; $index++) {
            $startDate = now()->addDays(random_int(1, 20))->toDateString();
            $endDate = now()->addDays(random_int(25, 60))->toDateString();

            MarketplaceOffer::factory()->create([
                'unit_id' => $unitIds->random(),
                'discount_type' => random_int(0, 1) === 1 ? 'percentage' : 'fixed',
                'discount_value' => random_int(5, 30),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => random_int(0, 1) === 1,
            ]);
        }
    }

    private function topUpMarketplaceVisits(int $targetCount): void
    {
        $missingCount = $targetCount - MarketplaceVisit::query()->count();

        if ($missingCount <= 0) {
            return;
        }

        $listingIds = MarketplaceUnit::query()->pluck('id');
        $statusIds = Status::query()->where('type', 'visit')->pluck('id');

        if ($listingIds->isEmpty() || $statusIds->isEmpty()) {
            return;
        }

        for ($index = 0; $index < $missingCount; $index++) {
            MarketplaceVisit::factory()->create([
                'marketplace_unit_id' => $listingIds->random(),
                'status_id' => $statusIds->random(),
                'scheduled_at' => now()->addDays(random_int(1, 14)),
            ]);
        }
    }

    private function seedPaymentData(): void
    {
        $targetCount = 20;
        $existingCount = Payment::query()->count();

        if ($existingCount >= $targetCount) {
            return;
        }

        $missingCount = $targetCount - $existingCount;
        $paidStatusId = Status::query()
            ->where('type', 'invoice')
            ->where('name_en', 'Paid')
            ->value('id');

        $transactions = Transaction::query()
            ->whereDoesntHave('payments')
            ->orderBy('id')
            ->take($missingCount)
            ->get(['id', 'amount', 'status_id']);

        foreach ($transactions as $transaction) {
            Payment::factory()->create([
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'payment_date' => now()->subDays(random_int(1, 60))->toDateString(),
            ]);

            Transaction::query()
                ->whereKey($transaction->id)
                ->update([
                    'is_paid' => true,
                    'status_id' => $paidStatusId ?? $transaction->status_id,
                ]);
        }
    }

    private function seedDependents(): void
    {
        $targetCount = 16;
        $missingCount = $targetCount - Dependent::query()->count();

        if ($missingCount <= 0) {
            return;
        }

        $residentIds = Resident::query()->pluck('id');
        $ownerIds = Owner::query()->pluck('id');

        if ($residentIds->isEmpty() && $ownerIds->isEmpty()) {
            return;
        }

        for ($index = 0; $index < $missingCount; $index++) {
            $seedForResident = $residentIds->isNotEmpty()
                && ($ownerIds->isEmpty() || random_int(0, 1) === 1);

            Dependent::factory()->create([
                'dependable_type' => $seedForResident ? Resident::class : Owner::class,
                'dependable_id' => $seedForResident ? $residentIds->random() : $ownerIds->random(),
            ]);
        }
    }
}
