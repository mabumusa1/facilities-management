<?php

namespace Tests\Feature\Requests;

use App\Models\AccountMembership;
use App\Models\RequestCategory;
use App\Models\RequestSubcategory;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ServiceRequestControllerLocalizationTest extends TestCase
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

    public function test_request_create_payload_uses_locale_specific_category_and_subcategory_names(): void
    {
        $this->authenticateUser();

        $category = RequestCategory::factory()->create([
            'name_ar' => 'خدمات الوحدات',
            'name_en' => 'Unit Services',
        ]);

        RequestSubcategory::factory()->create([
            'category_id' => $category->id,
            'name_ar' => 'تنظيف المنزل',
            'name_en' => 'House Cleaning',
        ]);

        $this->get(route('requests.create'), ['X-App-Locale' => 'ar'])
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('requests/Create')
                ->where('categories.0.name', 'خدمات الوحدات')
                ->where('categories.0.subcategories.0.name', 'تنظيف المنزل')
            );

        $this->get(route('requests.create'), ['X-App-Locale' => 'en'])
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('requests/Create')
                ->where('categories.0.name', 'Unit Services')
                ->where('categories.0.subcategories.0.name', 'House Cleaning')
            );
    }
}
