<?php

namespace Tests\Feature\Facilities;

use App\Models\AccountMembership;
use App\Models\Facility;
use App\Models\FacilityAvailabilityRule;
use App\Models\FacilityBooking;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ResidentBookingFlowTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    private Facility $facility;

    private Status $confirmedStatus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->tenant = Tenant::create(['name' => 'Test Community']);
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
            'pricing_mode' => 'per_hour',
            'booking_fee' => '50.00',
            'currency' => 'SAR',
            'contract_required' => false,
            'booking_horizon_days' => 14,
        ]);

        $this->confirmedStatus = Status::factory()->create([
            'type' => 'facility_booking',
            'name' => 'confirmed',
            'name_en' => 'confirmed',
        ]);
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    // ── Resident Index ────────────────────────────────────────────────────────

    public function test_resident_can_view_facilities_index(): void
    {
        $this->get(route('facilities.resident.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('facilities/ResidentIndex')
                ->has('facilities.data')
            );
    }

    public function test_guest_is_redirected_from_facilities_index(): void
    {
        auth()->logout();

        $this->get(route('facilities.resident.index'))
            ->assertRedirect();
    }

    public function test_only_active_facilities_appear_on_index(): void
    {
        $inactive = Facility::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'is_active' => false,
        ]);

        $response = $this->get(route('facilities.resident.index'));
        $response->assertOk();

        $facilityIds = collect($response->json('props.facilities.data'))->pluck('id');
        $this->assertContains($this->facility->id, $facilityIds->all());
        $this->assertNotContains($inactive->id, $facilityIds->all());
    }

    // ── Slot Picker page ──────────────────────────────────────────────────────

    public function test_resident_can_view_slot_picker_page(): void
    {
        $this->get(route('facilities.resident.slot-picker', $this->facility))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('facilities/SlotPicker')
                ->where('facility.id', $this->facility->id)
            );
    }

    // ── Slots AJAX endpoint ───────────────────────────────────────────────────

    public function test_slots_returns_closed_when_no_availability_rule(): void
    {
        $date = Carbon::tomorrow()->toDateString();

        $response = $this->getJson(route('facilities.resident.slots', $this->facility) . "?date={$date}");

        $response->assertOk()
            ->assertJsonPath('closed', true);
    }

    public function test_slots_returns_grid_when_availability_rule_exists(): void
    {
        $date = Carbon::now()->next(Carbon::MONDAY);
        $dayOfWeek = (int) $date->dayOfWeek;

        FacilityAvailabilityRule::factory()->create([
            'facility_id' => $this->facility->id,
            'day_of_week' => $dayOfWeek,
            'open_time' => '08:00',
            'close_time' => '12:00',
            'slot_duration_minutes' => 60,
            'max_concurrent_bookings' => 1,
            'is_active' => true,
        ]);

        $response = $this->getJson(route('facilities.resident.slots', $this->facility) . "?date={$date->toDateString()}");

        $response->assertOk()
            ->assertJsonPath('closed', false)
            ->assertJsonStructure([
                'slots' => [
                    ['start', 'end', 'status', 'remaining_capacity'],
                ],
            ]);

        $slots = $response->json('slots');
        $this->assertNotEmpty($slots);
        $this->assertEquals('08:00', $slots[0]['start']);
        $this->assertEquals('09:00', $slots[0]['end']);
        $this->assertEquals('available', $slots[0]['status']);
    }

    public function test_slots_marks_full_when_max_concurrent_bookings_reached(): void
    {
        $date = Carbon::now()->next(Carbon::MONDAY);
        $dayOfWeek = (int) $date->dayOfWeek;

        FacilityAvailabilityRule::factory()->create([
            'facility_id' => $this->facility->id,
            'day_of_week' => $dayOfWeek,
            'open_time' => '08:00',
            'close_time' => '12:00',
            'slot_duration_minutes' => 60,
            'max_concurrent_bookings' => 1,
            'is_active' => true,
        ]);

        // Book the 08:00 slot
        FacilityBooking::factory()->create([
            'facility_id' => $this->facility->id,
            'status_id' => $this->confirmedStatus->id,
            'booking_date' => $date->toDateString(),
            'start_time' => '08:00',
            'end_time' => '09:00',
        ]);

        $response = $this->getJson(route('facilities.resident.slots', $this->facility) . "?date={$date->toDateString()}");

        $response->assertOk();
        $slots = $response->json('slots');
        $bookedSlot = collect($slots)->firstWhere('start', '08:00');

        $this->assertNotNull($bookedSlot);
        $this->assertEquals('full', $bookedSlot['status']);
        $this->assertEquals(0, $bookedSlot['remaining_capacity']);
    }

    // ── Book endpoint ─────────────────────────────────────────────────────────

    public function test_resident_can_book_an_available_slot(): void
    {
        $date = Carbon::now()->next(Carbon::TUESDAY);
        $dayOfWeek = (int) $date->dayOfWeek;

        FacilityAvailabilityRule::factory()->create([
            'facility_id' => $this->facility->id,
            'day_of_week' => $dayOfWeek,
            'open_time' => '08:00',
            'close_time' => '12:00',
            'slot_duration_minutes' => 60,
            'max_concurrent_bookings' => 2,
            'is_active' => true,
        ]);

        $response = $this->postJson(route('facilities.resident.book', $this->facility), [
            'date' => $date->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['booking', 'contract_required', 'message']);

        $this->assertDatabaseHas('rf_facility_bookings', [
            'facility_id' => $this->facility->id,
            'booking_date' => $date->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);
    }

    public function test_booking_returns_409_when_slot_is_full(): void
    {
        $date = Carbon::now()->next(Carbon::WEDNESDAY);
        $dayOfWeek = (int) $date->dayOfWeek;

        FacilityAvailabilityRule::factory()->create([
            'facility_id' => $this->facility->id,
            'day_of_week' => $dayOfWeek,
            'open_time' => '08:00',
            'close_time' => '12:00',
            'slot_duration_minutes' => 60,
            'max_concurrent_bookings' => 1,
            'is_active' => true,
        ]);

        // Fill the slot
        FacilityBooking::factory()->create([
            'facility_id' => $this->facility->id,
            'status_id' => $this->confirmedStatus->id,
            'booking_date' => $date->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);

        $response = $this->postJson(route('facilities.resident.book', $this->facility), [
            'date' => $date->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);

        $response->assertStatus(409)
            ->assertJsonPath('error', 'slot_unavailable');
    }

    public function test_booking_returns_422_when_facility_is_closed_on_date(): void
    {
        // No availability rule → facility is closed all week
        $date = Carbon::now()->next(Carbon::THURSDAY);

        $response = $this->postJson(route('facilities.resident.book', $this->facility), [
            'date' => $date->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);

        $response->assertStatus(422);
    }

    public function test_booking_validation_requires_date_and_times(): void
    {
        $response = $this->postJson(route('facilities.resident.book', $this->facility), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['date', 'start_time', 'end_time']);
    }

    public function test_guest_cannot_book(): void
    {
        auth()->logout();

        $date = Carbon::now()->next(Carbon::FRIDAY);

        $response = $this->postJson(route('facilities.resident.book', $this->facility), [
            'date' => $date->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);

        $response->assertStatus(401);
    }

    public function test_booking_contract_required_facility_creates_pending_booking(): void
    {
        $contractFacility = Facility::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'is_active' => true,
            'contract_required' => true,
            'booking_horizon_days' => 14,
        ]);

        $date = Carbon::now()->next(Carbon::SATURDAY);
        $dayOfWeek = (int) $date->dayOfWeek;

        FacilityAvailabilityRule::factory()->create([
            'facility_id' => $contractFacility->id,
            'day_of_week' => $dayOfWeek,
            'open_time' => '08:00',
            'close_time' => '22:00',
            'slot_duration_minutes' => 60,
            'max_concurrent_bookings' => 2,
            'is_active' => true,
        ]);

        Status::factory()->create([
            'type' => 'facility_booking',
            'name' => 'pending',
            'name_en' => 'pending',
        ]);

        $response = $this->postJson(route('facilities.resident.book', $contractFacility), [
            'date' => $date->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('contract_required', true);
    }
}
