<?php

namespace Tests\Feature\Facilities;

use App\Models\AccountMembership;
use App\Models\FacilityCategory;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FacilityControllerLocalizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function authenticateUser(): User
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Test Account']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($user);

        return $user;
    }

    public function test_facilities_create_payload_uses_locale_specific_category_name(): void
    {
        $this->authenticateUser();

        FacilityCategory::factory()->create([
            'name_ar' => 'مسبح',
            'name_en' => 'Swimming Pool',
        ]);

        $this->get(route('facilities.create'), ['X-App-Locale' => 'ar'])
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('facilities/Create')
                ->where('categories.0.name', 'مسبح')
            );

        $this->get(route('facilities.create'), ['X-App-Locale' => 'en'])
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('facilities/Create')
                ->where('categories.0.name', 'Swimming Pool')
            );
    }
}
