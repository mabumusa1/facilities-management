<?php

namespace Tests\Feature;

use App\Models\AccountMembership;
use App\Models\Admin;
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
use App\Models\Professional;
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
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class Phase1ImplementationTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function authenticateUser(): User
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Test Account']);
        $tenant->makeCurrent();
        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);
        $this->actingAs($user);

        return $user;
    }

    private function createCommunityWithDeps(): Community
    {
        $country = Country::factory()->create();
        $currency = Currency::factory()->create();
        $city = City::factory()->recycle($country)->create();
        $district = District::factory()->recycle($city)->create();

        return Community::factory()
            ->recycle([$country, $currency, $city, $district])
            ->create();
    }

    private function createUnitWithDeps(): Unit
    {
        $community = $this->createCommunityWithDeps();
        $building = Building::factory()->recycle($community)->create();
        $category = UnitCategory::factory()->create();
        $type = UnitType::factory()->recycle($category)->create();
        $status = Status::factory()->create(['type' => 'unit']);

        return Unit::factory()->recycle([$community, $building, $category, $type, $status])->create();
    }

    // -------------------------------------------------------
    // Phase 1.1 — Model Relationship Tests
    // -------------------------------------------------------

    public function test_lease_has_created_by_relationship(): void
    {
        $admin = Admin::factory()->create();
        $status = Status::factory()->create(['type' => 'lease']);
        $resident = Resident::factory()->create();
        $lease = Lease::factory()
            ->recycle([$status, $resident])
            ->create(['created_by_id' => $admin->id]);

        $this->assertTrue($lease->createdBy->is($admin));
    }

    public function test_lease_has_deal_owner_relationship(): void
    {
        $admin = Admin::factory()->create();
        $status = Status::factory()->create(['type' => 'lease']);
        $resident = Resident::factory()->create();
        $lease = Lease::factory()
            ->recycle([$status, $resident])
            ->create(['deal_owner_id' => $admin->id]);

        $this->assertTrue($lease->dealOwner->is($admin));
    }

    public function test_transaction_has_category_subcategory_type_relationships(): void
    {
        $category = Setting::factory()->create(['type' => 'transaction_category']);
        $subcategory = Setting::factory()->create(['type' => 'transaction_subcategory', 'parent_id' => $category->id]);
        $txnType = Setting::factory()->create(['type' => 'transaction_type']);
        $status = Status::factory()->create(['type' => 'invoice']);

        $transaction = Transaction::factory()->recycle($status)->create([
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'type_id' => $txnType->id,
        ]);

        $this->assertTrue($transaction->category->is($category));
        $this->assertTrue($transaction->subcategory->is($subcategory));
        $this->assertTrue($transaction->type->is($txnType));
    }

    public function test_community_has_many_facilities(): void
    {
        $community = $this->createCommunityWithDeps();
        $facilityCategory = FacilityCategory::factory()->create();

        Facility::factory()->count(2)->recycle([$facilityCategory])->create([
            'community_id' => $community->id,
        ]);

        $this->assertCount(2, $community->facilities);
    }

    public function test_professional_has_many_requests(): void
    {
        $professional = Professional::factory()->create();
        $community = $this->createCommunityWithDeps();
        $category = RequestCategory::factory()->create();
        $status = Status::factory()->create(['type' => 'request']);
        $resident = Resident::factory()->create();

        ServiceRequest::factory()->count(3)->recycle([$community, $category, $status])->create([
            'professional_id' => $professional->id,
            'requester_type' => Resident::class,
            'requester_id' => $resident->id,
        ]);

        $this->assertCount(3, $professional->requests);
    }

    public function test_resident_has_many_units(): void
    {
        $resident = Resident::factory()->create();
        $unit = $this->createUnitWithDeps();
        $unit->update(['tenant_id' => $resident->id]);

        $this->assertCount(1, $resident->fresh()->units);
    }

    // -------------------------------------------------------
    // Phase 1.2 + Phase 2 — Controller Data Loading Tests
    // -------------------------------------------------------

    public function test_community_index_includes_requests_count(): void
    {
        $user = $this->authenticateUser();
        $community = $this->createCommunityWithDeps();
        $category = RequestCategory::factory()->create();
        $status = Status::factory()->create(['type' => 'request']);

        ServiceRequest::factory()->count(2)->recycle([$community, $category, $status])->create([
            'requester_type' => User::class,
            'requester_id' => $user->id,
        ]);

        $response = $this->get(route('communities.index'));
        $response->assertOk();

        $communityData = $response->original->getData()['page']['props']['communities']['data'][0] ?? null;
        $this->assertNotNull($communityData, 'Community data should be present in index');
        $this->assertArrayHasKey('requests_count', $communityData);
    }

    public function test_transaction_index_loads_category_and_type(): void
    {
        $this->authenticateUser();
        $category = Setting::factory()->create(['type' => 'transaction_category']);
        $txnType = Setting::factory()->create(['type' => 'transaction_type']);
        $status = Status::factory()->create(['type' => 'invoice']);

        Transaction::factory()->recycle($status)->create([
            'category_id' => $category->id,
            'type_id' => $txnType->id,
        ]);

        $response = $this->get(route('transactions.index'));
        $response->assertOk();
    }

    public function test_owner_create_passes_countries(): void
    {
        $this->authenticateUser();
        Country::factory()->create();

        $response = $this->get(route('owners.create'));
        $response->assertOk();

        $props = $response->original->getData()['page']['props'];
        $this->assertArrayHasKey('countries', $props);
        $this->assertNotEmpty($props['countries']);
    }

    public function test_resident_create_passes_countries(): void
    {
        $this->authenticateUser();
        Country::factory()->create();

        $response = $this->get(route('residents.create'));
        $response->assertOk();

        $props = $response->original->getData()['page']['props'];
        $this->assertArrayHasKey('countries', $props);
    }

    public function test_admin_create_passes_countries_and_scope_data(): void
    {
        $this->authenticateUser();
        Country::factory()->create();
        $this->createCommunityWithDeps();

        $response = $this->get(route('admins.create'));
        $response->assertOk();

        $props = $response->original->getData()['page']['props'];
        $this->assertArrayHasKey('countries', $props);
        $this->assertArrayHasKey('communities', $props);
        $this->assertArrayHasKey('buildings', $props);
    }

    public function test_professional_create_passes_subcategories(): void
    {
        $this->authenticateUser();
        $category = RequestCategory::factory()->create();
        RequestSubcategory::factory()->recycle($category)->create();

        $response = $this->get(route('professionals.create'));
        $response->assertOk();

        $props = $response->original->getData()['page']['props'];
        $this->assertArrayHasKey('subcategories', $props);
        $this->assertNotEmpty($props['subcategories']);
    }

    public function test_lease_create_passes_admins(): void
    {
        $this->authenticateUser();
        Admin::factory()->create(['account_tenant_id' => Tenant::current()->id]);

        $response = $this->get(route('leases.create'));
        $response->assertOk();

        $props = $response->original->getData()['page']['props'];
        $this->assertArrayHasKey('admins', $props);
        $this->assertNotEmpty($props['admins']);
    }

    public function test_building_create_passes_location_form_data(): void
    {
        $this->authenticateUser();
        $this->createCommunityWithDeps();

        $response = $this->get(route('buildings.create'));
        $response->assertOk();

        $props = $response->original->getData()['page']['props'];
        $this->assertArrayHasKey('communities', $props);
        $this->assertArrayHasKey('cities', $props);
        $this->assertArrayHasKey('districts', $props);
    }

    public function test_building_edit_passes_location_form_data(): void
    {
        $this->authenticateUser();
        $community = $this->createCommunityWithDeps();
        $building = Building::factory()->recycle($community)->create();

        $response = $this->get(route('buildings.edit', $building));
        $response->assertOk();

        $props = $response->original->getData()['page']['props'];
        $this->assertArrayHasKey('communities', $props);
        $this->assertArrayHasKey('cities', $props);
        $this->assertArrayHasKey('districts', $props);
    }

    public function test_unit_create_passes_full_form_data(): void
    {
        $this->authenticateUser();
        $this->createCommunityWithDeps();
        Owner::factory()->create();
        Resident::factory()->create();

        $response = $this->get(route('units.create'));
        $response->assertOk();

        $props = $response->original->getData()['page']['props'];
        $this->assertArrayHasKey('buildings', $props);
        $this->assertArrayHasKey('statuses', $props);
        $this->assertArrayHasKey('owners', $props);
        $this->assertArrayHasKey('residents', $props);
        $this->assertArrayHasKey('cities', $props);
        $this->assertArrayHasKey('districts', $props);
    }

    public function test_unit_edit_passes_full_form_data(): void
    {
        $this->authenticateUser();
        $community = $this->createCommunityWithDeps();
        $building = Building::factory()->recycle($community)->create();
        $category = UnitCategory::factory()->create();
        $type = UnitType::factory()->recycle($category)->create();
        $status = Status::factory()->create(['type' => 'unit']);
        $owner = Owner::factory()->create();
        $resident = Resident::factory()->create();

        $unit = Unit::factory()->create([
            'rf_community_id' => $community->id,
            'rf_building_id' => $building->id,
            'category_id' => $category->id,
            'type_id' => $type->id,
            'status_id' => $status->id,
            'owner_id' => $owner->id,
            'tenant_id' => $resident->id,
            'city_id' => $community->city_id,
            'district_id' => $community->district_id,
        ]);

        $response = $this->get(route('units.edit', $unit));
        $response->assertOk();

        $props = $response->original->getData()['page']['props'];
        $this->assertArrayHasKey('buildings', $props);
        $this->assertArrayHasKey('statuses', $props);
        $this->assertArrayHasKey('owners', $props);
        $this->assertArrayHasKey('residents', $props);
        $this->assertArrayHasKey('cities', $props);
        $this->assertArrayHasKey('districts', $props);
    }

    public function test_owner_store_accepts_nationality_and_active(): void
    {
        $this->authenticateUser();
        $country = Country::factory()->create();

        $response = $this->post(route('owners.store'), [
            'first_name' => 'Test',
            'last_name' => 'Owner',
            'phone_number' => '1234567890',
            'phone_country_code' => '+966',
            'nationality_id' => $country->id,
            'active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('rf_owners', [
            'first_name' => 'Test',
            'nationality_id' => $country->id,
        ]);
    }

    public function test_admin_store_syncs_communities_and_buildings(): void
    {
        $this->authenticateUser();
        $community = $this->createCommunityWithDeps();
        $building = Building::factory()->recycle($community)->create();

        $response = $this->post(route('admins.store'), [
            'first_name' => 'New',
            'last_name' => 'Admin',
            'phone_number' => '0501234567',
            'phone_country_code' => '+966',
            'role' => 'Admins',
            'communities' => [$community->id],
            'buildings' => [$building->id],
        ]);

        $response->assertRedirect();
        $admin = Admin::where('first_name', 'New')->first();
        $this->assertNotNull($admin);
        $this->assertCount(1, $admin->communities);
        $this->assertCount(1, $admin->buildings);
    }

    public function test_announcement_store_accepts_published_at(): void
    {
        $this->authenticateUser();

        $response = $this->post(route('announcements.store'), [
            'title' => 'Test Announcement',
            'content' => 'Test body content.',
            'published_at' => '2026-01-15',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('rf_announcements', [
            'title' => 'Test Announcement',
        ]);
    }
}
