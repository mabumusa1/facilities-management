<?php

namespace Tests\Feature\Feature\AppSettings;

use App\Models\AccountMembership;
use App\Models\Community;
use App\Models\Facility;
use App\Models\FacilityCategory;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SettingsFacilityControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function authenticateUser(): Tenant
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Facilities Account']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($user);

        return $tenant;
    }

    /**
     * @return array{0: FacilityCategory, 1: Community}
     */
    private function facilityDependencies(int $tenantId): array
    {
        $category = FacilityCategory::factory()->create([
            'name' => 'Sports',
            'name_en' => 'Sports',
        ]);

        $community = Community::factory()->create([
            'name' => 'Al Reef',
            'account_tenant_id' => $tenantId,
        ]);

        return [$category, $community];
    }

    public function test_index_renders_facilities_list_for_tenant(): void
    {
        $tenant = $this->authenticateUser();
        [$category, $community] = $this->facilityDependencies($tenant->id);

        Facility::factory()->create([
            'category_id' => $category->id,
            'community_id' => $community->id,
            'account_tenant_id' => $tenant->id,
            'name' => 'Main Pool',
            'name_en' => 'Main Pool',
        ]);

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('settings.facilities.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('app-settings/settings/FacilitiesIndex')
                ->has('facilities.data', 1)
                ->where('facilities.data.0.name', 'Main Pool')
            );
    }

    public function test_store_creates_new_facility_record(): void
    {
        $tenant = $this->authenticateUser();
        [$category, $community] = $this->facilityDependencies($tenant->id);

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('settings.facilities.store'), [
                'name' => 'Tennis Court',
                'name_en' => 'Tennis Court',
                'category_id' => $category->id,
                'community_id' => $community->id,
                'capacity' => 12,
                'open_time' => '08:00',
                'close_time' => '22:00',
                'booking_fee' => 125,
                'is_active' => true,
                'requires_approval' => true,
            ]);

        $facility = Facility::query()->first();

        $response->assertRedirect(route('settings.facilities.show', $facility));
        $this->assertDatabaseHas('rf_facilities', [
            'id' => $facility?->id,
            'name' => 'Tennis Court',
            'account_tenant_id' => $tenant->id,
            'community_id' => $community->id,
        ]);
    }

    public function test_update_modifies_existing_facility(): void
    {
        $tenant = $this->authenticateUser();
        [$category, $community] = $this->facilityDependencies($tenant->id);

        $facility = Facility::factory()->create([
            'category_id' => $category->id,
            'community_id' => $community->id,
            'account_tenant_id' => $tenant->id,
            'name' => 'Gym Hall',
            'capacity' => 20,
        ]);

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->put(route('settings.facilities.update', $facility), [
                'name' => 'Updated Gym Hall',
                'name_en' => 'Updated Gym Hall',
                'category_id' => $category->id,
                'community_id' => $community->id,
                'capacity' => 35,
                'open_time' => '09:00',
                'close_time' => '23:00',
                'booking_fee' => 50,
                'is_active' => true,
                'requires_approval' => false,
            ]);

        $response->assertRedirect(route('settings.facilities.show', $facility));
        $this->assertDatabaseHas('rf_facilities', [
            'id' => $facility->id,
            'name' => 'Updated Gym Hall',
            'capacity' => 35,
        ]);

        $this->assertDatabaseMissing('rf_facilities', [
            'id' => $facility->id,
            'name' => 'Gym Hall',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $tenant = $this->authenticateUser();

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('settings.facilities.store'), []);

        $response->assertSessionHasErrors([
            'name',
            'category_id',
        ]);
    }
}
