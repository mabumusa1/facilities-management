<?php

namespace Tests\Feature\Feature\Contracts;

use App\Models\AccountMembership;
use App\Models\Building;
use App\Models\City;
use App\Models\Community;
use App\Models\Country;
use App\Models\Currency;
use App\Models\District;
use App\Models\Facility;
use App\Models\FacilityCategory;
use App\Models\Lease;
use App\Models\Owner;
use App\Models\Request as ServiceRequest;
use App\Models\RequestCategory;
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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AgentJBatchPutEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateUser(): Tenant
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Agent J Contract Tenant']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($user);

        return $tenant;
    }

    public function test_assigned_put_route_names_exist(): void
    {
        $expectedRoutes = [
            'rf.buildings.update',
            'rf.communities.update',
            'rf.facilities.update',
            'rf.leases.update',
            'rf.owners.update',
            'rf.requests.update',
            'rf.requests.categories.update',
            'rf.requests.sub-categories.update',
            'rf.requests.types.update',
            'rf.tenants.update',
            'rf.transactions.update',
            'rf.units.update',
        ];

        foreach ($expectedRoutes as $routeName) {
            $this->assertTrue(Route::has($routeName), "Route [{$routeName}] must exist.");
        }
    }

    public function test_rf_property_put_endpoints_for_buildings_communities_facilities_and_units_work(): void
    {
        $tenant = $this->authenticateUser();

        $country = Country::factory()->create();
        $currency = Currency::factory()->create();
        $city = City::factory()->create(['country_id' => $country->id]);
        $district = District::factory()->create(['city_id' => $city->id]);

        $community = Community::factory()->create([
            'name' => 'Agent J Community',
            'country_id' => $country->id,
            'currency_id' => $currency->id,
            'city_id' => $city->id,
            'district_id' => $district->id,
            'account_tenant_id' => $tenant->id,
        ]);

        $building = Building::factory()->create([
            'name' => 'Tower J',
            'rf_community_id' => $community->id,
            'city_id' => $city->id,
            'district_id' => $district->id,
            'account_tenant_id' => $tenant->id,
        ]);

        $facilityCategory = FacilityCategory::factory()->create();

        $facility = Facility::factory()->create([
            'name' => 'Gym J',
            'name_ar' => 'نادي جي',
            'name_en' => 'Gym J',
            'category_id' => $facilityCategory->id,
            'community_id' => $community->id,
            'account_tenant_id' => $tenant->id,
        ]);

        $unitCategory = UnitCategory::factory()->create();
        $unitType = UnitType::factory()->create([
            'category_id' => $unitCategory->id,
        ]);
        $unitStatus = Status::factory()->create([
            'type' => 'unit',
            'name' => 'Available',
            'name_ar' => 'متاح',
            'name_en' => 'Available',
        ]);

        $unit = Unit::factory()->create([
            'name' => 'Unit J1',
            'rf_community_id' => $community->id,
            'rf_building_id' => $building->id,
            'category_id' => $unitCategory->id,
            'type_id' => $unitType->id,
            'status_id' => $unitStatus->id,
            'account_tenant_id' => $tenant->id,
        ]);

        $buildingUpdate = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->putJson('/rf/buildings/'.$building->id, [
                'name' => 'Tower J Updated',
                'rf_community_id' => $community->id,
                'city_id' => $city->id,
                'district_id' => $district->id,
                'no_floors' => 9,
            ]);

        $buildingUpdate
            ->assertOk()
            ->assertJsonPath('data.id', $building->id)
            ->assertJsonPath('data.name', 'Tower J Updated');

        $communityUpdate = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->putJson('/rf/communities/'.$community->id, [
                'name' => 'Agent J Community Updated',
                'country_id' => $country->id,
                'currency_id' => $currency->id,
                'city_id' => $city->id,
                'district_id' => $district->id,
                'sales_commission_rate' => 5.5,
                'rental_commission_rate' => 3.25,
            ]);

        $communityUpdate
            ->assertOk()
            ->assertJsonPath('data.id', $community->id)
            ->assertJsonPath('data.name', 'Agent J Community Updated');

        $facilityUpdate = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->putJson('/rf/facilities/'.$facility->id, [
                'name_ar' => 'النادي المحدث',
                'name_en' => 'Updated Gym',
                'days' => ['sunday', 'monday'],
                'booking_type' => 'hourly',
                'complex_id' => $community->id,
                'gender' => 'all',
                'approved' => true,
                'category_id' => $facilityCategory->id,
            ]);

        $facilityUpdate
            ->assertOk()
            ->assertJsonPath('data.id', $facility->id)
            ->assertJsonPath('data.name_en', 'Updated Gym');

        $unitUpdate = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->putJson('/rf/units/'.$unit->id, [
                'name' => 'Unit J1 Updated',
                'rf_community_id' => $community->id,
                'category_id' => $unitCategory->id,
                'type_id' => $unitType->id,
            ]);

        $unitUpdate
            ->assertOk()
            ->assertJsonPath('code', 200)
            ->assertJsonPath('data.id', $unit->id)
            ->assertJsonPath('data.name', 'Unit J1 Updated');
    }

    public function test_rf_put_endpoints_for_leases_owners_tenants_and_transactions_work(): void
    {
        $tenant = $this->authenticateUser();

        $resident = Resident::factory()->create([
            'first_name' => 'Lease',
            'last_name' => 'Tenant',
            'phone_country_code' => 'SA',
            'phone_number' => '500123111',
            'account_tenant_id' => $tenant->id,
        ]);

        $leaseStatus = Status::factory()->create([
            'type' => 'lease',
            'name' => 'Active',
            'name_ar' => 'نشط',
            'name_en' => 'Active',
        ]);

        $leaseUnitType = UnitCategory::factory()->create();

        $rentalContractType = Setting::factory()->create([
            'type' => 'rental_contract_type',
            'name' => 'Annual',
            'name_ar' => 'سنوي',
            'name_en' => 'Annual',
        ]);

        $paymentSchedule = Setting::factory()->create([
            'type' => 'payment_schedule',
            'name' => 'Monthly',
            'name_ar' => 'شهري',
            'name_en' => 'Monthly',
        ]);

        $lease = Lease::factory()->create([
            'contract_number' => 'LEASE-J-001',
            'tenant_id' => $resident->id,
            'status_id' => $leaseStatus->id,
            'lease_unit_type_id' => $leaseUnitType->id,
            'rental_contract_type_id' => $rentalContractType->id,
            'payment_schedule_id' => $paymentSchedule->id,
            'created_by_id' => (int) auth()->id(),
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addMonths(11)->toDateString(),
            'handover_date' => now()->subMonth()->toDateString(),
            'rental_total_amount' => 12000,
            'account_tenant_id' => $tenant->id,
        ]);

        $owner = Owner::factory()->create([
            'first_name' => 'Owner',
            'last_name' => 'Original',
            'phone_country_code' => 'SA',
            'phone_number' => '500555111',
            'account_tenant_id' => $tenant->id,
        ]);

        $transactionStatus = Status::factory()->create([
            'type' => 'invoice',
            'name' => 'Pending',
            'name_ar' => 'قيد الانتظار',
            'name_en' => 'Pending',
        ]);

        $transactionCategory = Setting::factory()->create([
            'type' => 'transaction_category',
            'name' => 'Rent',
            'name_ar' => 'إيجار',
            'name_en' => 'Rent',
        ]);

        $transactionType = Setting::factory()->create([
            'type' => 'transaction_type',
            'name' => 'Invoice',
            'name_ar' => 'فاتورة',
            'name_en' => 'Invoice',
        ]);

        $transaction = Transaction::query()->create([
            'category_id' => $transactionCategory->id,
            'type_id' => $transactionType->id,
            'status_id' => $transactionStatus->id,
            'amount' => 5000,
            'due_on' => now()->addWeek()->toDateString(),
            'details' => 'Original transaction',
            'account_tenant_id' => $tenant->id,
        ]);

        $leaseUpdate = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->putJson('/rf/leases/'.$lease->id, [
                'contract_number' => 'LEASE-J-001-UPDATED',
                'start_date' => now()->toDateString(),
                'end_date' => now()->addYear()->toDateString(),
                'rental_total_amount' => 14000,
                'notes' => 'Updated lease notes',
            ]);

        $leaseUpdate
            ->assertOk()
            ->assertJsonPath('data.id', $lease->id)
            ->assertJsonPath('data.contract_number', 'LEASE-J-001-UPDATED');

        $ownerUpdate = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->putJson('/rf/owners/'.$owner->id, [
                'first_name' => 'Owner',
                'last_name' => 'Updated',
                'phone_country_code' => 'SA',
                'phone_number' => '500555222',
            ]);

        $ownerUpdate
            ->assertOk()
            ->assertJsonPath('data.id', $owner->id)
            ->assertJsonPath('data.last_name', 'Updated');

        $tenantUpdate = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->putJson('/rf/tenants/'.$resident->id, [
                'first_name' => 'Lease',
                'last_name' => 'Tenant Updated',
                'phone_country_code' => 'SA',
                'phone_number' => '500123222',
            ]);

        $tenantUpdate
            ->assertOk()
            ->assertJsonPath('data.id', $resident->id)
            ->assertJsonPath('data.last_name', 'Tenant Updated');

        $transactionUpdate = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->putJson('/rf/transactions/'.$transaction->id, [
                'description' => 'Updated transaction details',
            ]);

        $transactionUpdate
            ->assertOk()
            ->assertJsonPath('data.id', $transaction->id)
            ->assertJsonPath('data.details', 'Updated transaction details');
    }

    public function test_rf_request_put_endpoints_for_requests_categories_sub_categories_and_types_work(): void
    {
        $tenant = $this->authenticateUser();

        $requestStatus = Status::factory()->create([
            'type' => 'request',
            'name' => 'New',
            'name_ar' => 'جديد',
            'name_en' => 'New',
        ]);

        $category = RequestCategory::factory()->create([
            'name' => 'Maintenance',
            'name_ar' => 'صيانة',
            'name_en' => 'Maintenance',
        ]);

        $subcategory = RequestSubcategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Electrical',
            'name_ar' => 'كهرباء',
            'name_en' => 'Electrical',
        ]);

        $serviceRequest = ServiceRequest::factory()->create([
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'status_id' => $requestStatus->id,
            'requester_type' => User::class,
            'requester_id' => (int) auth()->id(),
            'account_tenant_id' => $tenant->id,
        ]);

        $requestUpdate = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->putJson('/rf/requests/'.$serviceRequest->id, [
                'description' => 'Updated request details',
            ]);

        $requestUpdate
            ->assertOk()
            ->assertJsonPath('data.id', $serviceRequest->id)
            ->assertJsonPath('data.description', 'Updated request details');

        $categoryUpdate = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->putJson('/rf/requests/categories/'.$category->id, [
                'name' => 'Maintenance Updated',
            ]);

        $categoryUpdate
            ->assertOk()
            ->assertJsonPath('data.id', $category->id)
            ->assertJsonPath('data.name', 'Maintenance Updated');

        $subCategoryUpdate = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->putJson('/rf/requests/sub-categories/'.$subcategory->id, [
                'terms_and_conditions' => 'Updated service terms',
            ]);

        $subCategoryUpdate
            ->assertOk()
            ->assertJsonPath('data.id', $subcategory->id)
            ->assertJsonPath('data.terms_and_conditions', 'Updated service terms');

        $typeUpdate = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->putJson('/rf/requests/types/'.$subcategory->id, [
                'name_ar' => 'نوع خدمة محدث',
                'name_en' => 'Updated Service Type',
                'rf_sub_category_id' => $subcategory->id,
                'fee_type' => 'fixed',
            ]);

        $typeUpdate
            ->assertOk()
            ->assertJsonPath('data.id', $subcategory->id)
            ->assertJsonPath('data.name_en', 'Updated Service Type')
            ->assertJsonPath('data.fee_type', 'fixed');
    }
}
