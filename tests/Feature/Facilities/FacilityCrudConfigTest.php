<?php

namespace Tests\Feature\Facilities;

use App\Models\AccountMembership;
use App\Models\Community;
use App\Models\Facility;
use App\Models\FacilityAvailabilityRule;
use App\Models\FacilityBooking;
use App\Models\FacilityCategory;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FacilityCrudConfigTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    private Community $community;

    private FacilityCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->tenant = Tenant::create(['name' => 'Test Tenant']);
        $this->tenant->makeCurrent();

        $this->user = User::factory()->create();

        AccountMembership::create([
            'user_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($this->user);

        $this->community = Community::factory()->create([
            'account_tenant_id' => $this->tenant->id,
        ]);

        $this->category = FacilityCategory::factory()->create();
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    // -------------------------------------------------------------------------
    // Create page
    // -------------------------------------------------------------------------

    public function test_create_page_renders_with_categories_and_communities(): void
    {
        $response = $this->get(route('facilities.create'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('facilities/Create')
                ->has('categories')
                ->has('communities')
            );
    }

    // -------------------------------------------------------------------------
    // Store (Inertia web path)
    // -------------------------------------------------------------------------

    public function test_store_creates_facility_with_availability_rules(): void
    {
        $payload = [
            'name_en' => 'Main Pool',
            'name_ar' => 'المسبح الرئيسي',
            'category_id' => $this->category->id,
            'community_id' => $this->community->id,
            'capacity' => 50,
            'pricing_mode' => 'per_session',
            'price_amount' => '25.00',
            'currency' => 'SAR',
            'booking_horizon_days' => 14,
            'cancellation_hours_before' => 2,
            'min_booking_duration_minutes' => 30,
            'max_booking_duration_minutes' => null,
            'contract_required' => false,
            'notes' => 'Open to all residents.',
            'availability_rules' => [
                [
                    'day_of_week' => 1,
                    'open_time' => '06:00',
                    'close_time' => '22:00',
                    'slot_duration_minutes' => 60,
                    'max_concurrent_bookings' => 1,
                    'is_active' => true,
                ],
                [
                    'day_of_week' => 2,
                    'open_time' => '06:00',
                    'close_time' => '22:00',
                    'slot_duration_minutes' => 60,
                    'max_concurrent_bookings' => 1,
                    'is_active' => true,
                ],
            ],
        ];

        $response = $this->post(route('facilities.store'), $payload);

        $facility = Facility::where('name_en', 'Main Pool')->first();

        $this->assertNotNull($facility);
        $response->assertRedirect(route('facilities.show', $facility));

        $this->assertDatabaseHas('rf_facilities', [
            'id' => $facility->id,
            'name_en' => 'Main Pool',
            'name_ar' => 'المسبح الرئيسي',
            'pricing_mode' => 'per_session',
            'booking_horizon_days' => 14,
            'cancellation_hours_before' => 2,
            'contract_required' => false,
        ]);

        $this->assertCount(2, $facility->availabilityRules);

        $this->assertDatabaseHas('rf_facility_availability_rules', [
            'facility_id' => $facility->id,
            'day_of_week' => 1,
            'open_time' => '06:00:00',
            'slot_duration_minutes' => 60,
            'is_active' => true,
        ]);
    }

    public function test_store_fails_validation_without_required_fields(): void
    {
        $response = $this->post(route('facilities.store'), []);

        $response->assertSessionHasErrors(['name_en', 'name_ar', 'category_id', 'community_id', 'pricing_mode']);
    }

    public function test_store_requires_price_when_pricing_mode_is_per_session(): void
    {
        $response = $this->post(route('facilities.store'), [
            'name_en' => 'Pool',
            'name_ar' => 'مسبح',
            'category_id' => $this->category->id,
            'community_id' => $this->community->id,
            'pricing_mode' => 'per_session',
            'price_amount' => null,
            'booking_horizon_days' => 14,
            'cancellation_hours_before' => 2,
            'min_booking_duration_minutes' => 30,
            'availability_rules' => [],
        ]);

        $response->assertSessionHasErrors(['price_amount']);
    }

    // -------------------------------------------------------------------------
    // Edit page
    // -------------------------------------------------------------------------

    public function test_edit_page_renders_facility_with_availability_rules(): void
    {
        $facility = Facility::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'community_id' => $this->community->id,
            'category_id' => $this->category->id,
        ]);

        FacilityAvailabilityRule::factory()->create([
            'facility_id' => $facility->id,
            'day_of_week' => 1,
        ]);

        $response = $this->get(route('facilities.edit', $facility));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('facilities/Edit')
                ->has('facility')
                ->has('categories')
                ->has('communities')
                ->has('upcomingBookingsCount')
                ->where('upcomingBookingsCount', 0)
            );
    }

    // -------------------------------------------------------------------------
    // Update (Inertia web path)
    // -------------------------------------------------------------------------

    public function test_update_replaces_availability_rules_in_transaction(): void
    {
        $facility = Facility::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'community_id' => $this->community->id,
            'category_id' => $this->category->id,
        ]);

        FacilityAvailabilityRule::factory()->create([
            'facility_id' => $facility->id,
            'day_of_week' => 5,
        ]);

        $payload = [
            'name_en' => 'Updated Pool',
            'name_ar' => 'المسبح المحدّث',
            'category_id' => $this->category->id,
            'community_id' => $this->community->id,
            'capacity' => 60,
            'is_active' => true,
            'pricing_mode' => 'free',
            'booking_horizon_days' => 21,
            'cancellation_hours_before' => 4,
            'min_booking_duration_minutes' => 60,
            'max_booking_duration_minutes' => null,
            'contract_required' => true,
            'notes' => null,
            'availability_rules' => [
                [
                    'day_of_week' => 1,
                    'open_time' => '08:00',
                    'close_time' => '20:00',
                    'slot_duration_minutes' => 30,
                    'max_concurrent_bookings' => 2,
                    'is_active' => true,
                ],
            ],
        ];

        $response = $this->put(route('facilities.update', $facility), $payload);

        $response->assertRedirect();

        $facility->refresh();

        $this->assertEquals('Updated Pool', $facility->name_en);
        $this->assertEquals(21, $facility->booking_horizon_days);
        $this->assertTrue($facility->contract_required);

        // Old rule (day_of_week=5) is gone, new rule (day_of_week=1) exists
        $this->assertDatabaseMissing('rf_facility_availability_rules', [
            'facility_id' => $facility->id,
            'day_of_week' => 5,
        ]);

        $this->assertDatabaseHas('rf_facility_availability_rules', [
            'facility_id' => $facility->id,
            'day_of_week' => 1,
            'slot_duration_minutes' => 30,
        ]);
    }

    // -------------------------------------------------------------------------
    // Show page
    // -------------------------------------------------------------------------

    public function test_show_page_renders_with_upcoming_bookings_count(): void
    {
        $facility = Facility::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'community_id' => $this->community->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->get(route('facilities.show', $facility));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('facilities/Show')
                ->has('facility')
                ->has('upcomingBookingsCount')
            );
    }

    // -------------------------------------------------------------------------
    // Authorization
    // -------------------------------------------------------------------------

    public function test_unauthenticated_user_cannot_access_facility_create(): void
    {
        $this->post('/logout');
        auth()->logout();

        $response = $this->get(route('facilities.create'));

        $response->assertRedirect();
    }

    public function test_user_without_permission_cannot_store_facility(): void
    {
        $unprivilegedUser = User::factory()->create();

        AccountMembership::create([
            'user_id' => $unprivilegedUser->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'tenants',
        ]);

        $response = $this->actingAs($unprivilegedUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('facilities.store'), [
                'name_en' => 'Forbidden Facility',
                'name_ar' => 'مرفق محظور',
                'category_id' => $this->category->id,
                'community_id' => $this->community->id,
                'pricing_mode' => 'free',
                'booking_horizon_days' => 14,
                'cancellation_hours_before' => 2,
                'min_booking_duration_minutes' => 30,
                'availability_rules' => [],
            ]);

        $response->assertForbidden();
    }

    public function test_user_without_permission_cannot_update_facility(): void
    {
        $facility = Facility::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'community_id' => $this->community->id,
            'category_id' => $this->category->id,
        ]);

        $unprivilegedUser = User::factory()->create();

        AccountMembership::create([
            'user_id' => $unprivilegedUser->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'tenants',
        ]);

        $response = $this->actingAs($unprivilegedUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put(route('facilities.update', $facility), [
                'name_en' => 'Updated Name',
                'name_ar' => 'اسم محدث',
                'category_id' => $this->category->id,
                'community_id' => $this->community->id,
                'pricing_mode' => 'free',
                'booking_horizon_days' => 14,
                'cancellation_hours_before' => 2,
                'min_booking_duration_minutes' => 30,
                'availability_rules' => [],
            ]);

        $response->assertForbidden();
    }

    public function test_update_rejects_deactivation_without_confirmation_when_upcoming_bookings_exist(): void
    {
        $facility = Facility::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'community_id' => $this->community->id,
            'category_id' => $this->category->id,
            'is_active' => true,
        ]);

        // Create an upcoming booking for this facility
        FacilityBooking::factory()->create([
            'facility_id' => $facility->id,
            'account_tenant_id' => $this->tenant->id,
            'start_at' => now()->addDays(3),
        ]);

        $payload = [
            'name_en' => $facility->name_en,
            'name_ar' => $facility->name_ar,
            'category_id' => $this->category->id,
            'community_id' => $this->community->id,
            'is_active' => false,
            'pricing_mode' => 'free',
            'booking_horizon_days' => 14,
            'cancellation_hours_before' => 2,
            'min_booking_duration_minutes' => 30,
            'availability_rules' => [],
            // deactivation_confirmed intentionally omitted
        ];

        $response = $this->put(route('facilities.update', $facility), $payload);

        $response->assertSessionHasErrors(['is_active']);
        $this->assertTrue($facility->fresh()->is_active);
    }

    public function test_update_allows_deactivation_with_confirmation_when_upcoming_bookings_exist(): void
    {
        $facility = Facility::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'community_id' => $this->community->id,
            'category_id' => $this->category->id,
            'is_active' => true,
        ]);

        // Create an upcoming booking for this facility
        FacilityBooking::factory()->create([
            'facility_id' => $facility->id,
            'account_tenant_id' => $this->tenant->id,
            'start_at' => now()->addDays(3),
        ]);

        $payload = [
            'name_en' => $facility->name_en,
            'name_ar' => $facility->name_ar,
            'category_id' => $this->category->id,
            'community_id' => $this->community->id,
            'is_active' => false,
            'deactivation_confirmed' => true,
            'pricing_mode' => 'free',
            'booking_horizon_days' => 14,
            'cancellation_hours_before' => 2,
            'min_booking_duration_minutes' => 30,
            'availability_rules' => [],
        ];

        $response = $this->put(route('facilities.update', $facility), $payload);

        $response->assertRedirect();
        $this->assertFalse($facility->fresh()->is_active);
    }
}
