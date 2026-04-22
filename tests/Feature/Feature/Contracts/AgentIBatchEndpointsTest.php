<?php

namespace Tests\Feature\Feature\Contracts;

use App\Models\AccountMembership;
use App\Models\Admin;
use App\Models\Announcement;
use App\Models\Building;
use App\Models\Community;
use App\Models\Lease;
use App\Models\MarketplaceUnit;
use App\Models\Request as ServiceRequest;
use App\Models\RequestCategory;
use App\Models\Resident;
use App\Models\Setting;
use App\Models\Status;
use App\Models\SystemSetting;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\UnitCategory;
use App\Models\UnitType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AgentIBatchEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateUser(): Tenant
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Agent I Contract Tenant']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($user);

        return $tenant;
    }

    public function test_assigned_route_names_exist(): void
    {
        $expectedRoutes = [
            'rf.requests.categories.store',
            'rf.requests.change-status.approved',
            'rf.requests.change-status.canceled',
            'rf.requests.change-status.completed',
            'rf.requests.change-status.in-progress',
            'rf.requests.change-status.pending',
            'rf.requests.change-status.rejected',
            'rf.requests.service-settings.update-or-create',
            'rf.requests.sub-categories.store',
            'rf.requests.types.create.store',
            'rf.sub-leases.store',
            'rf.tenants.store',
            'rf.tenants.family-members.store',
            'rf.transactions.store',
            'rf.units.store',
            'rf.units.bulk-delete',
            'rf.units.bulk-update',
            'marketplace-admin.listings.update',
            'marketplace-admin.settings.banks.update',
            'rf.admins.update',
            'rf.announcements.update',
            'rf.buildings.update',
        ];

        foreach ($expectedRoutes as $routeName) {
            $this->assertTrue(Route::has($routeName), "Route [{$routeName}] must exist.");
        }
    }

    public function test_requests_agent_i_post_endpoints_work(): void
    {
        $tenant = $this->authenticateUser();

        $category = RequestCategory::factory()->create();

        $newStatus = Status::factory()->create([
            'type' => 'request',
            'name' => 'New',
            'name_en' => 'New',
            'name_ar' => 'New',
        ]);

        $approvedStatus = Status::factory()->create([
            'type' => 'request',
            'name' => 'Approved',
            'name_en' => 'Approved',
            'name_ar' => 'Approved',
        ]);

        $canceledStatus = Status::factory()->create([
            'type' => 'request',
            'name' => 'Canceled',
            'name_en' => 'Canceled',
            'name_ar' => 'Canceled',
        ]);

        $completedStatus = Status::factory()->create([
            'type' => 'request',
            'name' => 'Completed',
            'name_en' => 'Completed',
            'name_ar' => 'Completed',
        ]);

        $inProgressStatus = Status::factory()->create([
            'type' => 'request',
            'name' => 'In Progress',
            'name_en' => 'In Progress',
            'name_ar' => 'In Progress',
        ]);

        $pendingStatus = Status::factory()->create([
            'type' => 'request',
            'name' => 'Pending',
            'name_en' => 'Pending',
            'name_ar' => 'Pending',
        ]);

        $rejectedStatus = Status::factory()->create([
            'type' => 'request',
            'name' => 'Rejected',
            'name_en' => 'Rejected',
            'name_ar' => 'Rejected',
        ]);

        $serviceRequest = ServiceRequest::factory()->create([
            'category_id' => $category->id,
            'status_id' => $newStatus->id,
            'requester_type' => User::class,
            'requester_id' => (int) auth()->id(),
            'account_tenant_id' => $tenant->id,
        ]);

        $storeCategory = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/requests/categories', [
                'name_ar' => 'فئة جديدة',
                'name_en' => 'New Category',
                'status' => true,
                'has_sub_categories' => true,
            ]);

        $storeCategory
            ->assertOk()
            ->assertJsonPath('data.name', 'New Category');

        $storeServiceSetting = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/requests/service-settings/updateOrCreate', [
                'rf_category_id' => $category->id,
                'permissions' => [
                    'manager_close_Request' => true,
                    'not_require_professional_enter_request_code' => false,
                    'not_require_professional_upload_request_photo' => false,
                    'attachments_required' => true,
                    'allow_professional_reschedule' => false,
                ],
                'visibilities' => [
                    'hide_resident_number' => false,
                    'hide_resident_name' => false,
                    'hide_professional_number_and_name' => false,
                    'show_unified_number_only' => false,
                ],
                'submit_request_before_type' => 'hours',
                'submit_request_before_value' => 4,
                'capacity_type' => 'daily',
                'capacity_value' => 10,
            ]);

        $storeServiceSetting
            ->assertOk()
            ->assertJsonPath('data.category_id', $category->id)
            ->assertJsonPath('data.permissions.manager_close_Request', true);

        $storeSubCategory = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/requests/sub-categories', [
                'category_id' => $category->id,
                'name_ar' => 'تصنيف فرعي',
                'name_en' => 'Sub Category',
                'status' => true,
                'is_all_day' => true,
            ]);

        $storeSubCategory
            ->assertOk()
            ->assertJsonPath('data.name_en', 'Sub Category');

        $storeType = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/requests/types/create', [
                'category_id' => $category->id,
                'name_ar' => 'نوع طلب',
                'name_en' => 'Request Type',
                'status' => true,
            ]);

        $storeType
            ->assertOk()
            ->assertJsonPath('data.name_en', 'Request Type');

        $statusAssertions = [
            '/rf/requests/change-status/approved' => $approvedStatus->id,
            '/rf/requests/change-status/canceled' => $canceledStatus->id,
            '/rf/requests/change-status/completed' => $completedStatus->id,
            '/rf/requests/change-status/in-progress' => $inProgressStatus->id,
            '/rf/requests/change-status/pending' => $pendingStatus->id,
            '/rf/requests/change-status/rejected' => $rejectedStatus->id,
        ];

        foreach ($statusAssertions as $endpoint => $expectedStatusId) {
            $response = $this
                ->withSession(['tenant_id' => $tenant->id])
                ->postJson($endpoint, [
                    'rf_request_id' => $serviceRequest->id,
                    'admin_notes' => 'Agent I status update',
                ]);

            $response
                ->assertOk()
                ->assertJsonPath('data.id', $serviceRequest->id)
                ->assertJsonPath('data.status.id', $expectedStatusId);
        }
    }

    public function test_sublease_and_tenant_family_member_endpoints_work(): void
    {
        $tenant = $this->authenticateUser();

        $leaseStatus = Status::factory()->create([
            'type' => 'lease',
            'name' => 'Active',
            'name_en' => 'Active',
            'name_ar' => 'Active',
        ]);

        $leaseUnitCategory = UnitCategory::factory()->create();

        $rentalContractType = Setting::factory()->create([
            'type' => 'rental_contract_type',
            'name' => 'Annual',
            'name_en' => 'Annual',
            'name_ar' => 'Annual',
        ]);

        $paymentSchedule = Setting::factory()->create([
            'type' => 'payment_schedule',
            'name' => 'Monthly',
            'name_en' => 'Monthly',
            'name_ar' => 'Monthly',
        ]);

        $resident = Resident::factory()->create([
            'id' => 23,
            'account_tenant_id' => $tenant->id,
            'phone_country_code' => 'SA',
            'phone_number' => '500000123',
        ]);

        $parentLease = Lease::factory()->create([
            'tenant_id' => $resident->id,
            'status_id' => $leaseStatus->id,
            'lease_unit_type_id' => $leaseUnitCategory->id,
            'rental_contract_type_id' => $rentalContractType->id,
            'payment_schedule_id' => $paymentSchedule->id,
            'created_by_id' => (int) auth()->id(),
            'account_tenant_id' => $tenant->id,
        ]);

        $storeTenant = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/tenants', [
                'first_name' => 'New',
                'last_name' => 'Tenant',
                'email' => 'new-tenant@example.test',
                'phone_number' => '500000124',
                'phone_country_code' => 'SA',
            ]);

        $storeTenant
            ->assertOk()
            ->assertJsonPath('data.first_name', 'New')
            ->assertJsonPath('data.last_name', 'Tenant');

        $storeFamilyMember = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/tenants/23/family-members', [
                'first_name' => 'Dependent',
                'last_name' => 'One',
                'relationship' => 'Spouse',
            ]);

        $storeFamilyMember
            ->assertOk()
            ->assertJsonPath('data.first_name', 'Dependent')
            ->assertJsonPath('data.relationship', 'Spouse');

        $storeSubLease = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/sub-leases', [
                'rf_lease_id' => $parentLease->id,
                'contract_number' => 'SUB-LEASE-AGENT-I-1',
                'status_id' => $leaseStatus->id,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonth()->toDateString(),
                'handover_date' => now()->toDateString(),
                'rental_total_amount' => 12000,
            ]);

        $storeSubLease
            ->assertOk()
            ->assertJsonPath('data.contract_number', 'SUB-LEASE-AGENT-I-1');

        $subLeaseId = (int) $storeSubLease->json('data.id');

        $this->assertDatabaseHas('rf_leases', [
            'id' => $subLeaseId,
            'parent_lease_id' => $parentLease->id,
            'is_sub_lease' => true,
        ]);
    }

    public function test_transactions_and_units_bulk_endpoints_work(): void
    {
        $tenant = $this->authenticateUser();

        $transactionStatus = Status::factory()->create([
            'type' => 'transaction',
            'name' => 'Pending',
            'name_en' => 'Pending',
            'name_ar' => 'Pending',
        ]);

        $storeTransaction = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/transactions', [
                'category_id' => 1,
                'type_id' => 1,
                'status_id' => $transactionStatus->id,
                'assignee_id' => 1,
                'amount' => 5000,
                'due_on' => now()->addWeek()->toDateString(),
                'details' => 'Agent I transaction',
            ]);

        $storeTransaction
            ->assertOk()
            ->assertJsonPath('data.amount', 5000);

        $community = Community::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $building = Building::factory()->create([
            'account_tenant_id' => $tenant->id,
            'rf_community_id' => $community->id,
            'city_id' => $community->city_id,
            'district_id' => $community->district_id,
        ]);

        $unitCategory = UnitCategory::factory()->create();
        $unitType = UnitType::factory()->create([
            'category_id' => $unitCategory->id,
        ]);

        $availableStatus = Status::factory()->create([
            'type' => 'unit',
            'name' => 'Available',
            'name_en' => 'Available',
            'name_ar' => 'Available',
        ]);

        $maintenanceStatus = Status::factory()->create([
            'type' => 'unit',
            'name' => 'Maintenance',
            'name_en' => 'Maintenance',
            'name_ar' => 'Maintenance',
        ]);

        $storeUnit = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/units', [
                'name' => 'Agent I Unit',
                'rf_community_id' => $community->id,
                'rf_building_id' => $building->id,
                'category_id' => $unitCategory->id,
                'type_id' => $unitType->id,
                'status_id' => $availableStatus->id,
            ]);

        $storeUnit
            ->assertOk()
            ->assertJsonPath('data.name', 'Agent I Unit');

        $bulkOne = Unit::factory()->create([
            'account_tenant_id' => $tenant->id,
            'rf_community_id' => $community->id,
            'rf_building_id' => $building->id,
            'category_id' => $unitCategory->id,
            'type_id' => $unitType->id,
            'status_id' => $availableStatus->id,
        ]);

        $bulkTwo = Unit::factory()->create([
            'account_tenant_id' => $tenant->id,
            'rf_community_id' => $community->id,
            'rf_building_id' => $building->id,
            'category_id' => $unitCategory->id,
            'type_id' => $unitType->id,
            'status_id' => $availableStatus->id,
        ]);

        $bulkUpdate = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/units/bulk-update', [
                'unit_ids' => [$bulkOne->id, $bulkTwo->id],
                'status_id' => $maintenanceStatus->id,
            ]);

        $bulkUpdate
            ->assertOk()
            ->assertJsonPath('data.updated_count', 2);

        $this->assertDatabaseHas('rf_units', [
            'id' => $bulkOne->id,
            'status_id' => $maintenanceStatus->id,
        ]);

        $bulkDelete = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/units/bulk-delete', [
                'unit_ids' => [$bulkOne->id, $bulkTwo->id],
            ]);

        $bulkDelete
            ->assertOk()
            ->assertJsonPath('data.deleted_count', 2);

        $this->assertDatabaseMissing('rf_units', ['id' => $bulkOne->id]);
        $this->assertDatabaseMissing('rf_units', ['id' => $bulkTwo->id]);
    }

    public function test_marketplace_admin_update_endpoints_work(): void
    {
        $tenant = $this->authenticateUser();

        $unit = Unit::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $listing = MarketplaceUnit::factory()->create([
            'unit_id' => $unit->id,
            'listing_type' => 'rent',
            'price' => 50000,
            'is_active' => true,
        ]);

        $listingUpdate = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->putJson('/marketplace/admin/listings/'.$listing->id, [
                'unit_id' => (string) $unit->id,
                'listing_type' => 'sale',
                'price' => 90000,
                'is_active' => false,
            ]);

        $listingUpdate
            ->assertOk()
            ->assertJsonPath('data.id', $listing->id)
            ->assertJsonPath('data.listing_type', 'sale')
            ->assertJsonPath('data.price', '90000.00');

        $bankSetting = SystemSetting::create([
            'key' => 'bank-details',
            'payload' => [
                'beneficiary_name' => 'Old Name',
                'bank_name' => 'Old Bank',
                'account_number' => '12345678901234',
                'iban' => 'SA0000000000000000000000',
            ],
            'account_tenant_id' => $tenant->id,
        ]);

        $bankUpdate = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->putJson('/marketplace/admin/settings/banks/'.$bankSetting->id, [
                'beneficiary_name' => 'New Name',
                'bank_name' => 'New Bank',
                'account_number' => '12345678901235',
                'iban' => 'SA0000000000000000000001',
            ]);

        $bankUpdate
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $bankSetting->id)
            ->assertJsonPath('data.bank_name', 'New Bank');
    }

    public function test_rf_put_endpoints_for_admin_announcement_and_building_work(): void
    {
        $tenant = $this->authenticateUser();

        $community = Community::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $building = Building::factory()->create([
            'account_tenant_id' => $tenant->id,
            'rf_community_id' => $community->id,
            'city_id' => $community->city_id,
            'district_id' => $community->district_id,
        ]);

        $admin = Admin::factory()->create([
            'account_tenant_id' => $tenant->id,
            'role' => 'Admins',
        ]);

        $announcement = Announcement::factory()->create([
            'account_tenant_id' => $tenant->id,
            'community_id' => $community->id,
            'building_id' => $building->id,
            'title' => 'Old Announcement',
            'content' => 'Old content',
        ]);

        $adminUpdate = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->putJson('/rf/admins/'.$admin->id, [
                'first_name' => 'Updated',
                'last_name' => 'Admin',
                'email' => 'updated-admin@example.test',
                'phone_number' => '500000888',
                'phone_country_code' => 'SA',
                'role' => 'Admins',
            ]);

        $adminUpdate
            ->assertOk()
            ->assertJsonPath('data.id', $admin->id)
            ->assertJsonPath('data.first_name', 'Updated')
            ->assertJsonPath('data.last_name', 'Admin');

        $announcementUpdate = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->putJson('/rf/announcements/'.$announcement->id, [
                'title' => 'Updated Announcement',
                'description' => 'Updated description',
                'is_visible' => true,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDay()->toDateString(),
                'start_time' => '09:00',
                'end_time' => '11:00',
                'notify_user_type' => 'all',
                'community_id' => $community->id,
                'building_id' => $building->id,
            ]);

        $announcementUpdate
            ->assertOk()
            ->assertJsonPath('data.id', $announcement->id)
            ->assertJsonPath('data.title', 'Updated Announcement')
            ->assertJsonPath('data.content', 'Updated description');

        $buildingUpdate = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->putJson('/rf/buildings/'.$building->id, [
                'name' => 'Updated Tower',
                'rf_community_id' => $community->id,
                'city_id' => $community->city_id,
                'district_id' => $community->district_id,
                'no_floors' => 12,
                'year_build' => '2024',
            ]);

        $buildingUpdate
            ->assertOk()
            ->assertJsonPath('data.id', $building->id)
            ->assertJsonPath('data.name', 'Updated Tower');
    }
}
