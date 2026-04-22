<?php

namespace Tests\Feature\Feature\Contracts;

use App\Models\AccountMembership;
use App\Models\Admin;
use App\Models\Announcement;
use App\Models\Building;
use App\Models\Community;
use App\Models\Facility;
use App\Models\FacilityCategory;
use App\Models\Lease;
use App\Models\MarketplaceUnit;
use App\Models\Owner;
use App\Models\Professional;
use App\Models\Request as ServiceRequest;
use App\Models\SystemSetting;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AgentABatchDeleteEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateUser(): Tenant
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Agent A Contract Tenant']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($user);

        return $tenant;
    }

    public function test_assigned_delete_route_names_exist(): void
    {
        $expectedRoutes = [
            'marketplace-admin.listings.destroy',
            'marketplace-admin.settings.banks.destroy',
            'rf.admins.destroy',
            'rf.announcements.destroy',
            'rf.buildings.destroy',
            'rf.communities.destroy',
            'rf.facilities.destroy',
            'rf.leases.destroy',
            'rf.owners.destroy',
            'rf.professionals.destroy',
            'rf.requests.destroy',
        ];

        foreach ($expectedRoutes as $routeName) {
            $this->assertTrue(Route::has($routeName), "Route [{$routeName}] must exist.");
        }
    }

    public function test_marketplace_admin_delete_endpoints_delete_listing_and_bank_setting(): void
    {
        $tenant = $this->authenticateUser();

        $unit = Unit::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $listing = MarketplaceUnit::factory()->create([
            'unit_id' => $unit->id,
        ]);

        $bankSetting = SystemSetting::create([
            'key' => 'bank-details',
            'payload' => [
                'beneficiary_name' => 'Acme Properties',
                'bank_name' => 'National Bank',
                'account_number' => '12345678901234',
                'iban' => 'SA0380000000608010167519',
            ],
            'account_tenant_id' => $tenant->id,
        ]);

        $listingDelete = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->deleteJson(route('marketplace-admin.listings.destroy', $listing));

        $listingDelete
            ->assertOk()
            ->assertJsonPath('data.id', $listing->id)
            ->assertJsonPath('message', 'Marketplace listing deleted.');

        $this->assertDatabaseMissing('rf_marketplace_units', [
            'id' => $listing->id,
        ]);

        $bankDelete = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->deleteJson(route('marketplace-admin.settings.banks.destroy', $bankSetting));

        $bankDelete
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $bankSetting->id)
            ->assertJsonPath('message', 'Bank settings deleted successfully.');

        $this->assertDatabaseMissing('rf_system_settings', [
            'id' => $bankSetting->id,
        ]);
    }

    public function test_rf_contact_delete_endpoints_return_json_and_remove_records(): void
    {
        $tenant = $this->authenticateUser();

        $admin = Admin::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $owner = Owner::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $professional = Professional::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $adminDelete = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->deleteJson(route('rf.admins.destroy', $admin));

        $adminDelete
            ->assertOk()
            ->assertJsonPath('data.id', $admin->id)
            ->assertJsonPath('message', 'Admin deleted.');

        $this->assertDatabaseMissing('rf_admins', [
            'id' => $admin->id,
        ]);

        $ownerDelete = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->deleteJson(route('rf.owners.destroy', $owner));

        $ownerDelete
            ->assertOk()
            ->assertJsonPath('data.id', $owner->id)
            ->assertJsonPath('message', 'Owner deleted.');

        $this->assertSoftDeleted('rf_owners', [
            'id' => $owner->id,
        ]);

        $professionalDelete = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->deleteJson(route('rf.professionals.destroy', $professional));

        $professionalDelete
            ->assertOk()
            ->assertJsonPath('data.id', $professional->id)
            ->assertJsonPath('message', 'Professional deleted.');

        $this->assertDatabaseMissing('rf_professionals', [
            'id' => $professional->id,
        ]);
    }

    public function test_rf_property_delete_endpoints_return_json_and_remove_records(): void
    {
        $tenant = $this->authenticateUser();

        $communityForAnnouncement = Community::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $announcement = Announcement::factory()->create([
            'community_id' => $communityForAnnouncement->id,
            'account_tenant_id' => $tenant->id,
        ]);

        $communityForBuilding = Community::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $building = Building::factory()->create([
            'rf_community_id' => $communityForBuilding->id,
            'account_tenant_id' => $tenant->id,
        ]);

        $community = Community::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $facilityCategory = FacilityCategory::factory()->create();
        $communityForFacility = Community::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $facility = Facility::factory()->create([
            'category_id' => $facilityCategory->id,
            'community_id' => $communityForFacility->id,
            'account_tenant_id' => $tenant->id,
        ]);

        $announcementDelete = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->deleteJson(route('rf.announcements.destroy', $announcement));

        $announcementDelete
            ->assertOk()
            ->assertJsonPath('data.id', $announcement->id)
            ->assertJsonPath('message', 'Announcement deleted.');

        $this->assertSoftDeleted('rf_announcements', [
            'id' => $announcement->id,
        ]);

        $buildingDelete = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->deleteJson(route('rf.buildings.destroy', $building));

        $buildingDelete
            ->assertOk()
            ->assertJsonPath('data.id', $building->id)
            ->assertJsonPath('message', 'Building deleted.');

        $this->assertDatabaseMissing('rf_buildings', [
            'id' => $building->id,
        ]);

        $communityDelete = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->deleteJson(route('rf.communities.destroy', $community));

        $communityDelete
            ->assertOk()
            ->assertJsonPath('data.id', $community->id)
            ->assertJsonPath('message', 'Community deleted.');

        $this->assertDatabaseMissing('rf_communities', [
            'id' => $community->id,
        ]);

        $facilityDelete = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->deleteJson(route('rf.facilities.destroy', $facility));

        $facilityDelete
            ->assertOk()
            ->assertJsonPath('data.id', $facility->id)
            ->assertJsonPath('message', 'Facility deleted.');

        $this->assertSoftDeleted('rf_facilities', [
            'id' => $facility->id,
        ]);
    }

    public function test_rf_lease_and_request_delete_endpoints_return_json_and_remove_records(): void
    {
        $tenant = $this->authenticateUser();

        $lease = Lease::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $serviceRequest = ServiceRequest::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $leaseDelete = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->deleteJson(route('rf.leases.destroy', $lease));

        $leaseDelete
            ->assertOk()
            ->assertJsonPath('data.id', $lease->id)
            ->assertJsonPath('message', 'Lease deleted.');

        $this->assertSoftDeleted('rf_leases', [
            'id' => $lease->id,
        ]);

        $requestDelete = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->deleteJson(route('rf.requests.destroy', $serviceRequest));

        $requestDelete
            ->assertOk()
            ->assertJsonPath('data.id', $serviceRequest->id)
            ->assertJsonPath('message', 'Request deleted.');

        $this->assertSoftDeleted('rf_requests', [
            'id' => $serviceRequest->id,
        ]);
    }
}
