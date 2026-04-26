<?php

namespace Tests\Feature\Facilities;

use App\Models\AccountMembership;
use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\Resident;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\User;
use App\Support\FacilityBookingStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class FacilityCalendarControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    private Facility $facility;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->tenant = Tenant::create(['name' => 'Calendar Test Tenant']);
        $this->tenant->makeCurrent();

        $this->user = User::factory()->create();

        AccountMembership::create([
            'user_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($this->user);

        $this->facility = Facility::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'is_active' => true,
            'contract_required' => false,
        ]);

        // Ensure canonical status rows exist for constant-based lookups.
        Status::firstOrCreate(['id' => FacilityBookingStatus::BOOKED], [
            'type' => 'facility_booking',
            'name' => 'Booked',
            'name_en' => 'Booked',
        ]);

        Status::firstOrCreate(['id' => FacilityBookingStatus::PENDING_APPROVAL], [
            'type' => 'facility_booking',
            'name' => 'Pending Approval',
            'name_en' => 'Pending Approval',
        ]);

        Status::firstOrCreate(['id' => FacilityBookingStatus::CANCELLED], [
            'type' => 'facility_booking',
            'name' => 'Cancelled',
            'name_en' => 'Cancelled',
        ]);
    }

    // ── index ─────────────────────────────────────────────────────────────────

    /** Admin calendar page renders with facilities and currentWeekStart. */
    public function test_index_renders_calendar_page_for_authorised_admin(): void
    {
        $response = $this->get(route('facilities.calendar'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('facilities/Calendar')
            ->has('facilities')
            ->has('currentWeekStart')
        );
    }

    /** Unauthenticated users are redirected. */
    public function test_index_redirects_unauthenticated_users(): void
    {
        auth()->logout();

        $this->get(route('facilities.calendar'))
            ->assertRedirect();
    }

    /** User without permissions is forbidden. */
    public function test_index_forbids_user_without_permission(): void
    {
        $outsider = User::factory()->create();
        $this->actingAs($outsider);

        $this->get(route('facilities.calendar'))
            ->assertForbidden();
    }

    // ── bookings (AJAX) ───────────────────────────────────────────────────────

    /** Happy path: returns bookings array for a given week. */
    public function test_bookings_returns_json_for_current_week(): void
    {
        $weekStart = Carbon::now()->startOfWeek(Carbon::SUNDAY)->toDateString();

        FacilityBooking::factory()->create([
            'facility_id' => $this->facility->id,
            'account_tenant_id' => $this->tenant->id,
            'status_id' => FacilityBookingStatus::BOOKED,
            'booking_date' => $weekStart,
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);

        $response = $this->getJson(route('facilities.calendar.bookings', ['week_start' => $weekStart]));

        $response->assertOk()
            ->assertJsonStructure(['bookings', 'facility_id', 'week_start', 'week_end'])
            ->assertJsonPath('week_start', $weekStart)
            ->assertJsonCount(1, 'bookings');
    }

    /** Facility filter narrows results. */
    public function test_bookings_filters_by_facility_id(): void
    {
        $weekStart = Carbon::now()->startOfWeek(Carbon::SUNDAY)->toDateString();

        $otherFacility = Facility::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'is_active' => true,
        ]);

        FacilityBooking::factory()->create([
            'facility_id' => $this->facility->id,
            'account_tenant_id' => $this->tenant->id,
            'status_id' => FacilityBookingStatus::BOOKED,
            'booking_date' => $weekStart,
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);

        FacilityBooking::factory()->create([
            'facility_id' => $otherFacility->id,
            'account_tenant_id' => $this->tenant->id,
            'status_id' => FacilityBookingStatus::BOOKED,
            'booking_date' => $weekStart,
            'start_time' => '11:00',
            'end_time' => '12:00',
        ]);

        $response = $this->getJson(route('facilities.calendar.bookings', [
            'week_start' => $weekStart,
            'facility_id' => $this->facility->id,
        ]));

        $response->assertOk()
            ->assertJsonCount(1, 'bookings')
            ->assertJsonPath('bookings.0.facility_id', $this->facility->id);
    }

    // ── show (popover detail) ─────────────────────────────────────────────────

    /** Booking detail returns JSON with policy flags. */
    public function test_show_returns_booking_detail_json(): void
    {
        $booking = FacilityBooking::factory()->create([
            'facility_id' => $this->facility->id,
            'account_tenant_id' => $this->tenant->id,
            'status_id' => FacilityBookingStatus::BOOKED,
            'booking_date' => Carbon::today()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);

        $response = $this->getJson(route('facilities.calendar.show', $booking));

        $response->assertOk()
            ->assertJsonStructure([
                'id', 'facility_id', 'facility_name',
                'booker_name', 'booker_type', 'booking_date',
                'start_time', 'end_time', 'status_id', 'status_name',
                'can_checkin', 'can_cancel', 'can_update',
            ])
            ->assertJsonPath('id', $booking->id);
    }

    // ── store ─────────────────────────────────────────────────────────────────

    /** Admin can create a booking without a resident (admin reservation). */
    public function test_store_creates_admin_booking_without_resident(): void
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        $response = $this->postJson(route('facilities.calendar.store'), [
            'facility_id' => $this->facility->id,
            'booking_date' => $tomorrow,
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['booking', 'message'])
            ->assertJsonPath('booking.facility_id', $this->facility->id)
            ->assertJsonPath('booking.start_time', '10:00');

        $this->assertDatabaseHas('rf_facility_bookings', [
            'facility_id' => $this->facility->id,
            'booking_date' => $tomorrow,
            'start_time' => '10:00',
            'end_time' => '11:00',
            'status_id' => FacilityBookingStatus::BOOKED,
        ]);
    }

    /** Admin can create a booking for a specific resident. */
    public function test_store_creates_booking_for_resident(): void
    {
        $resident = Resident::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $tomorrow = Carbon::tomorrow()->toDateString();

        $response = $this->postJson(route('facilities.calendar.store'), [
            'facility_id' => $this->facility->id,
            'booking_date' => $tomorrow,
            'start_time' => '14:00',
            'end_time' => '15:00',
            'resident_id' => $resident->id,
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('rf_facility_bookings', [
            'facility_id' => $this->facility->id,
            'booking_date' => $tomorrow,
            'start_time' => '14:00',
            'booker_id' => $resident->id,
            'booker_type' => Resident::class,
        ]);
    }

    /** Overlap returns 422 with conflict message. */
    public function test_store_returns_422_when_booking_overlaps_existing(): void
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        FacilityBooking::factory()->create([
            'facility_id' => $this->facility->id,
            'account_tenant_id' => $this->tenant->id,
            'status_id' => FacilityBookingStatus::BOOKED,
            'booking_date' => $tomorrow,
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);

        $response = $this->postJson(route('facilities.calendar.store'), [
            'facility_id' => $this->facility->id,
            'booking_date' => $tomorrow,
            'start_time' => '10:30',
            'end_time' => '11:30',
        ]);

        $response->assertUnprocessable();
    }

    /** Validation: end_time must be after start_time. */
    public function test_store_validates_end_time_after_start_time(): void
    {
        $response = $this->postJson(route('facilities.calendar.store'), [
            'facility_id' => $this->facility->id,
            'booking_date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '12:00',
            'end_time' => '11:00',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['end_time']);
    }

    /** Contract-required facility creates a PENDING_APPROVAL booking. */
    public function test_store_creates_pending_booking_for_contract_required_facility(): void
    {
        $contractFacility = Facility::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'is_active' => true,
            'contract_required' => true,
        ]);

        $tomorrow = Carbon::tomorrow()->toDateString();

        $response = $this->postJson(route('facilities.calendar.store'), [
            'facility_id' => $contractFacility->id,
            'booking_date' => $tomorrow,
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('rf_facility_bookings', [
            'facility_id' => $contractFacility->id,
            'status_id' => FacilityBookingStatus::PENDING_APPROVAL,
        ]);
    }

    // ── cross-tenant isolation ────────────────────────────────────────────────

    /** Bookings from a different tenant are not returned. */
    public function test_bookings_does_not_return_other_tenant_bookings(): void
    {
        $tenantB = Tenant::create(['name' => 'Tenant B']);
        $facilityB = Facility::factory()->create([
            'account_tenant_id' => $tenantB->id,
            'is_active' => true,
        ]);

        $weekStart = Carbon::now()->startOfWeek(Carbon::SUNDAY)->toDateString();

        FacilityBooking::factory()->create([
            'facility_id' => $facilityB->id,
            'account_tenant_id' => $tenantB->id,
            'status_id' => FacilityBookingStatus::BOOKED,
            'booking_date' => $weekStart,
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);

        $response = $this->getJson(route('facilities.calendar.bookings', ['week_start' => $weekStart]));

        $response->assertOk();
        $returnedIds = collect($response->json('bookings'))->pluck('id');
        $this->assertEmpty($returnedIds);
    }
}
