<?php

namespace Tests\Feature\Feature\Contracts;

use App\Models\AccountMembership;
use App\Models\Admin;
use App\Models\City;
use App\Models\Community;
use App\Models\Country;
use App\Models\Currency;
use App\Models\District;
use App\Models\FacilityCategory;
use App\Models\Lease;
use App\Models\Professional;
use App\Models\RequestCategory;
use App\Models\Resident;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\UnitCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AgentHBatchEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateUser(): Tenant
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Agent H Contract Tenant']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($user);

        return $tenant;
    }

    /**
     * @return array<string, mixed>
     */
    private function createLeaseDependencies(Tenant $tenant): array
    {
        $resident = Resident::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $newStatus = Status::factory()->create([
            'type' => 'lease',
            'name' => 'New',
            'name_en' => 'New',
            'name_ar' => 'new',
        ]);

        $activeStatus = Status::factory()->create([
            'type' => 'lease',
            'name' => 'Active',
            'name_en' => 'Active',
            'name_ar' => 'active',
        ]);

        $expiredStatus = Status::factory()->create([
            'type' => 'lease',
            'name' => 'Expired',
            'name_en' => 'Expired',
            'name_ar' => 'expired',
        ]);

        $terminatedStatus = Status::factory()->create([
            'type' => 'lease',
            'name' => 'Terminated',
            'name_en' => 'Terminated',
            'name_ar' => 'terminated',
        ]);

        $unitCategory = UnitCategory::factory()->create([
            'name' => 'Residential',
            'name_en' => 'Residential',
            'name_ar' => 'residential',
        ]);

        $rentalContractType = Setting::factory()->create([
            'type' => 'rental_contract_type',
            'name' => 'Annual',
            'name_en' => 'Annual',
            'name_ar' => 'annual',
        ]);

        $paymentSchedule = Setting::factory()->create([
            'type' => 'payment_schedule',
            'name' => 'Monthly',
            'name_en' => 'Monthly',
            'name_ar' => 'monthly',
        ]);

        return [
            'resident' => $resident,
            'newStatus' => $newStatus,
            'activeStatus' => $activeStatus,
            'expiredStatus' => $expiredStatus,
            'terminatedStatus' => $terminatedStatus,
            'unitCategory' => $unitCategory,
            'rentalContractType' => $rentalContractType,
            'paymentSchedule' => $paymentSchedule,
        ];
    }

    /**
     * @param  array<string, mixed>  $dependencies
     * @return array<string, mixed>
     */
    private function leasePayload(array $dependencies, string $contractNumber): array
    {
        return [
            'contract_number' => $contractNumber,
            'tenant_id' => $dependencies['resident']->id,
            'status_id' => $dependencies['newStatus']->id,
            'lease_unit_type_id' => $dependencies['unitCategory']->id,
            'rental_contract_type_id' => $dependencies['rentalContractType']->id,
            'payment_schedule_id' => $dependencies['paymentSchedule']->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonths(12)->toDateString(),
            'handover_date' => now()->toDateString(),
            'tenant_type' => 'individual',
            'rental_type' => 'total',
            'rental_total_amount' => 12000,
        ];
    }

    public function test_assigned_route_names_exist(): void
    {
        $expectedRoutes = [
            'rf.admins.check-validate',
            'rf.announcements.store',
            'rf.buildings.store',
            'rf.communities.store',
            'rf.facilities.store',
            'rf.leads.store',
            'rf.leases.store',
            'rf.leases.addendum',
            'rf.leases.change-status.move-out',
            'rf.leases.change-status.reactivate',
            'rf.leases.change-status.suspend',
            'rf.leases.change-status.terminate',
            'rf.leases.create.store',
            'rf.leases.renew.store',
            'rf.leases.step-four',
            'rf.owners.store',
            'rf.professionals.store',
            'rf.requests.store',
            'rf.requests.assign',
            'rf.requests.reassign',
        ];

        foreach ($expectedRoutes as $routeName) {
            $this->assertTrue(Route::has($routeName), "Route [{$routeName}] must exist.");
        }
    }

    public function test_rf_contact_and_property_post_endpoints_validate_and_create_records(): void
    {
        $tenant = $this->authenticateUser();

        $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/admins/check-validate', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'phone_country_code', 'phone_number']);

        Admin::factory()->create([
            'first_name' => 'Ahed',
            'last_name' => 'Manager',
            'phone_country_code' => 'SA',
            'phone_number' => '500000123',
            'role' => 'Admins',
            'account_tenant_id' => $tenant->id,
        ]);

        $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/admins/check-validate', [
                'first_name' => 'Ahed',
                'last_name' => 'Manager',
                'phone_country_code' => 'SA',
                'phone_number' => '500000123',
            ])
            ->assertOk()
            ->assertJsonPath('code', 200);

        $country = Country::factory()->create();
        $currency = Currency::factory()->create();
        $city = City::factory()->create(['country_id' => $country->id]);
        $district = District::factory()->create(['city_id' => $city->id]);

        $communityResponse = $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/communities', [
                'name' => 'H Community',
                'country_id' => $country->id,
                'currency_id' => $currency->id,
                'city_id' => $city->id,
                'district_id' => $district->id,
            ])
            ->assertOk();

        $communityId = (int) $communityResponse->json('data.id');

        $buildingResponse = $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/buildings', [
                'name' => 'H Tower',
                'rf_community_id' => $communityId,
            ])
            ->assertOk();

        $buildingId = (int) $buildingResponse->json('data.id');

        $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/announcements', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
                'description',
                'is_visible',
                'start_date',
                'end_date',
                'start_time',
                'end_time',
                'notify_user_type',
            ]);

        $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/announcements', [
                'title' => 'Contract Notice',
                'description' => 'Agent H announcement',
                'is_visible' => true,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDay()->toDateString(),
                'start_time' => '08:00',
                'end_time' => '10:00',
                'notify_user_type' => 'all',
                'community_id' => $communityId,
                'building_id' => $buildingId,
            ])
            ->assertOk()
            ->assertJsonPath('data.title', 'Contract Notice');

        $facilityCategory = FacilityCategory::factory()->create();

        $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/facilities', [
                'name_ar' => 'Pool Ar',
                'name_en' => 'Pool En',
                'days' => ['monday', 'wednesday'],
                'booking_type' => 'hourly',
                'complex_id' => $communityId,
                'gender' => 'all',
                'approved' => 1,
                'category_id' => $facilityCategory->id,
            ])
            ->assertOk()
            ->assertJsonPath('data.name_en', 'Pool En');

        $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/leads', [
                'first_name' => 'Lead',
                'last_name' => 'Person',
                'phone_number' => '500111222',
                'email' => 'lead-agent-h@example.test',
            ])
            ->assertOk()
            ->assertJsonPath('data.phone_number', '500111222');

        $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/owners', [
                'first_name' => 'Owner',
                'last_name' => 'One',
                'phone_country_code' => 'SA',
                'phone_number' => '500000555',
                'email' => 'owner-agent-h@example.test',
            ])
            ->assertOk()
            ->assertJsonPath('data.first_name', 'Owner');

        $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/professionals', [
                'first_name' => 'Professional',
                'last_name' => 'One',
                'phone_country_code' => 'SA',
                'phone_number' => '500000666',
                'email' => 'professional-agent-h@example.test',
            ])
            ->assertOk()
            ->assertJsonPath('data.first_name', 'Professional');
    }

    public function test_rf_lease_post_endpoints_create_alias_addendum_and_status_changes(): void
    {
        $tenant = $this->authenticateUser();
        $dependencies = $this->createLeaseDependencies($tenant);

        $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/leases', [])
            ->assertStatus(422);

        $leaseStoreResponse = $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/leases', $this->leasePayload($dependencies, 'LEASE-H-001'))
            ->assertOk()
            ->assertJsonPath('data.contract_number', 'LEASE-H-001');

        $storedLeaseId = (int) $leaseStoreResponse->json('data.id');

        $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/leases/create', $this->leasePayload($dependencies, 'LEASE-H-002'))
            ->assertOk()
            ->assertJsonPath('data.contract_number', 'LEASE-H-002');

        $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/leases/step-four', $this->leasePayload($dependencies, 'LEASE-H-003'))
            ->assertOk()
            ->assertJsonPath('data.contract_number', 'LEASE-H-003');

        $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/leases/'.$storedLeaseId.'/addendum', [
                'type' => 'amendment',
                'description' => 'Lease addendum details',
                'effective_date' => now()->toDateString(),
            ])
            ->assertOk()
            ->assertJsonPath('data.lease_id', $storedLeaseId);

        $expiredLease = Lease::factory()->create([
            'contract_number' => 'LEASE-H-EXPIRED',
            'tenant_id' => $dependencies['resident']->id,
            'status_id' => $dependencies['expiredStatus']->id,
            'lease_unit_type_id' => $dependencies['unitCategory']->id,
            'rental_contract_type_id' => $dependencies['rentalContractType']->id,
            'payment_schedule_id' => $dependencies['paymentSchedule']->id,
            'created_by_id' => (int) auth()->id(),
            'account_tenant_id' => $tenant->id,
        ]);

        $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/leases/change-status/move-out', [
                'rf_lease_id' => $expiredLease->id,
                'end_at' => now()->toDateString(),
            ])
            ->assertOk()
            ->assertJsonPath('code', 200);

        $activeLease = Lease::factory()->create([
            'contract_number' => 'LEASE-H-ACTIVE',
            'tenant_id' => $dependencies['resident']->id,
            'status_id' => $dependencies['activeStatus']->id,
            'lease_unit_type_id' => $dependencies['unitCategory']->id,
            'rental_contract_type_id' => $dependencies['rentalContractType']->id,
            'payment_schedule_id' => $dependencies['paymentSchedule']->id,
            'created_by_id' => (int) auth()->id(),
            'account_tenant_id' => $tenant->id,
        ]);

        $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/leases/change-status/terminate', [
                'rf_lease_id' => $activeLease->id,
                'end_at' => now()->toDateString(),
            ])
            ->assertOk()
            ->assertJsonPath('code', 200);

        $suspendLease = Lease::factory()->create([
            'contract_number' => 'LEASE-H-SUSPEND',
            'tenant_id' => $dependencies['resident']->id,
            'status_id' => $dependencies['activeStatus']->id,
            'lease_unit_type_id' => $dependencies['unitCategory']->id,
            'rental_contract_type_id' => $dependencies['rentalContractType']->id,
            'payment_schedule_id' => $dependencies['paymentSchedule']->id,
            'created_by_id' => (int) auth()->id(),
            'account_tenant_id' => $tenant->id,
        ]);

        $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/leases/change-status/suspend', [
                'lease_id' => $suspendLease->id,
                'reason' => 'Temporary hold',
            ])
            ->assertOk()
            ->assertJsonPath('code', 200);

        $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/leases/change-status/reactivate', [
                'lease_id' => $suspendLease->id,
                'reason' => 'Resumed',
            ])
            ->assertOk()
            ->assertJsonPath('code', 200);
    }

    public function test_rf_lease_renew_store_endpoint_creates_renewed_lease(): void
    {
        $tenant = $this->authenticateUser();
        $dependencies = $this->createLeaseDependencies($tenant);

        $community = Community::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $unit = Unit::factory()->create([
            'account_tenant_id' => $tenant->id,
            'rf_community_id' => $community->id,
            'category_id' => $dependencies['unitCategory']->id,
        ]);

        $baseLease = Lease::factory()->create([
            'contract_number' => 'LEASE-H-BASE',
            'tenant_id' => $dependencies['resident']->id,
            'status_id' => $dependencies['activeStatus']->id,
            'lease_unit_type_id' => $dependencies['unitCategory']->id,
            'rental_contract_type_id' => $dependencies['rentalContractType']->id,
            'payment_schedule_id' => $dependencies['paymentSchedule']->id,
            'created_by_id' => (int) auth()->id(),
            'account_tenant_id' => $tenant->id,
        ]);

        $response = $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/leases/renew/store', [
                'rf_lease_id' => $baseLease->id,
                'rental_contract_type_id' => $dependencies['rentalContractType']->id,
                'payment_schedule_id' => $dependencies['paymentSchedule']->id,
                'start_date' => now()->addDay()->toDateString(),
                'end_date' => now()->addYear()->toDateString(),
                'rental_type' => 'total',
                'autoGenerateLeaseNumber' => true,
                'units' => [
                    [
                        'id' => $unit->id,
                        'rental_amount' => 15000,
                        'rental_annual_type' => 'yearly',
                    ],
                ],
            ])
            ->assertOk();

        $renewedLeaseId = (int) $response->json('data.id');

        $this->assertNotSame($baseLease->id, $renewedLeaseId);

        $this->assertDatabaseHas('rf_leases', [
            'id' => $renewedLeaseId,
            'parent_lease_id' => $baseLease->id,
            'is_renew' => true,
        ]);
    }

    public function test_rf_requests_store_assign_and_reassign_endpoints_work(): void
    {
        $tenant = $this->authenticateUser();

        $requestCategory = RequestCategory::factory()->create();

        Status::factory()->create([
            'type' => 'request',
            'name' => 'New',
            'name_en' => 'New',
            'name_ar' => 'new',
        ]);

        $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/requests', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['category_id']);

        $requestResponse = $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/requests', [
                'category_id' => $requestCategory->id,
                'description' => 'Please fix this issue',
            ])
            ->assertOk();

        $requestId = (int) $requestResponse->json('data.id');

        $professionalOne = Professional::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $professionalTwo = Professional::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/requests/'.$requestId.'/assign', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['professional_id']);

        $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/requests/'.$requestId.'/assign', [
                'professional_id' => $professionalOne->id,
                'admin_notes' => 'Assigned to first professional',
            ])
            ->assertOk()
            ->assertJsonPath('data.id', $requestId);

        $this->assertDatabaseHas('rf_requests', [
            'id' => $requestId,
            'professional_id' => $professionalOne->id,
        ]);

        $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/requests/'.$requestId.'/reassign', [
                'professional_id' => $professionalTwo->id,
                'admin_notes' => 'Reassigned to second professional',
            ])
            ->assertOk()
            ->assertJsonPath('data.id', $requestId);

        $this->assertDatabaseHas('rf_requests', [
            'id' => $requestId,
            'professional_id' => $professionalTwo->id,
        ]);
    }
}
