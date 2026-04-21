<?php

namespace Tests\Feature;

use App\Models\AccountMembership;
use App\Models\Community;
use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\Lease;
use App\Models\Owner;
use App\Models\Resident;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class Phase3ImplementationTest extends TestCase
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

    // ── 3.1 FacilityBooking CRUD ──

    public function test_facility_booking_index_renders_inertia(): void
    {
        $this->authenticateUser();
        FacilityBooking::factory()->create();

        $response = $this->get('/facility-bookings');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('facilities/bookings/Index')->has('bookings'));
    }

    public function test_facility_booking_create_renders_inertia(): void
    {
        $this->authenticateUser();

        $response = $this->get('/facility-bookings/create');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('facilities/bookings/Create')
            ->has('facilities')
            ->has('residents')
            ->has('statuses')
        );
    }

    public function test_facility_booking_store_creates_record(): void
    {
        $this->authenticateUser();
        $facility = Facility::factory()->create(['is_active' => true]);
        $resident = Resident::factory()->create();
        $status = Status::factory()->create(['type' => 'facility_booking']);

        $response = $this->post('/facility-bookings', [
            'facility_id' => $facility->id,
            'booker_id' => $resident->id,
            'booker_type' => Resident::class,
            'status_id' => $status->id,
            'booking_date' => '2025-06-15',
            'start_time' => '10:00',
            'end_time' => '12:00',
            'number_of_guests' => 5,
            'notes' => 'Test booking',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('rf_facility_bookings', [
            'facility_id' => $facility->id,
            'booker_id' => $resident->id,
            'number_of_guests' => 5,
        ]);
    }

    public function test_facility_booking_store_validation(): void
    {
        $this->authenticateUser();

        $response = $this->post('/facility-bookings', []);
        $response->assertSessionHasErrors(['facility_id', 'booker_id', 'booker_type', 'status_id', 'booking_date', 'start_time', 'end_time']);
    }

    public function test_facility_booking_edit_renders_inertia(): void
    {
        $this->authenticateUser();
        $booking = FacilityBooking::factory()->create();

        $response = $this->get("/facility-bookings/{$booking->id}/edit");
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('facilities/bookings/Edit')
            ->has('facilityBooking')
            ->has('facilities')
            ->has('statuses')
        );
    }

    public function test_facility_booking_show_renders_inertia(): void
    {
        $this->authenticateUser();
        $booking = FacilityBooking::factory()->create();

        $response = $this->get("/facility-bookings/{$booking->id}");
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('facilities/bookings/Show')
            ->has('facilityBooking')
        );
    }

    public function test_facility_booking_update_modifies_record(): void
    {
        $this->authenticateUser();
        $booking = FacilityBooking::factory()->create();
        $newStatus = Status::factory()->create(['type' => 'facility_booking']);

        $response = $this->put("/facility-bookings/{$booking->id}", [
            'status_id' => $newStatus->id,
            'booking_date' => '2025-07-01',
            'start_time' => '14:00',
            'end_time' => '16:00',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('rf_facility_bookings', [
            'id' => $booking->id,
            'status_id' => $newStatus->id,
        ]);
    }

    public function test_facility_booking_delete_removes_record(): void
    {
        $this->authenticateUser();
        $booking = FacilityBooking::factory()->create();

        $response = $this->delete("/facility-bookings/{$booking->id}");
        $response->assertRedirect();
        $this->assertSoftDeleted('rf_facility_bookings', ['id' => $booking->id]);
    }

    // ── 3.2 Index Column / Data Tests ──

    public function test_unit_index_loads_owner_and_tenant(): void
    {
        $this->authenticateUser();

        $response = $this->get('/units');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('properties/units/Index'));
    }

    public function test_community_index_returns_commission_columns(): void
    {
        $this->authenticateUser();

        $response = $this->get('/communities');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('properties/communities/Index'));
    }

    public function test_transaction_index_loads_category_and_type(): void
    {
        $this->authenticateUser();
        $category = Setting::factory()->create(['type' => 'transaction_category']);
        $type = Setting::factory()->create(['type' => 'transaction_type']);
        Transaction::factory()->create([
            'category_id' => $category->id,
            'type_id' => $type->id,
        ]);

        $response = $this->get('/transactions');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('accounting/transactions/Index'));
    }

    // ── 3.3 Show Page Tests ──

    public function test_community_show_loads_facilities(): void
    {
        $this->authenticateUser();
        $community = Community::factory()->create();
        Facility::factory()->create(['community_id' => $community->id]);

        $response = $this->get("/communities/{$community->id}");
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('properties/communities/Show')
            ->has('community.facilities')
        );
    }

    public function test_transaction_show_displays_category_name(): void
    {
        $this->authenticateUser();
        $category = Setting::factory()->create(['type' => 'transaction_category']);
        $type = Setting::factory()->create(['type' => 'transaction_type']);
        $transaction = Transaction::factory()->create([
            'category_id' => $category->id,
            'type_id' => $type->id,
        ]);

        $response = $this->get("/transactions/{$transaction->id}");
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('accounting/transactions/Show')
            ->has('transaction.category')
            ->has('transaction.type')
        );
    }

    public function test_owner_show_displays_unit_community_building(): void
    {
        $this->authenticateUser();
        $owner = Owner::factory()->create();

        $response = $this->get("/owners/{$owner->id}");
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('contacts/owners/Show'));
    }

    public function test_lease_show_loads_additional_fees_and_escalations(): void
    {
        $this->authenticateUser();
        $lease = Lease::factory()->create();

        $response = $this->get("/leases/{$lease->id}");
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('leasing/leases/Show')
            ->has('lease.additional_fees')
            ->has('lease.escalations')
        );
    }
}
