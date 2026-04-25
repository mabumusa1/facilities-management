<?php

namespace Tests\Feature\Facilities;

use App\Models\Facility;
use App\Models\FacilityAvailabilityRule;
use App\Models\FacilityBooking;
use App\Models\FacilityWaitlist;
use App\Models\Resident;
use App\Models\Status;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class FacilityDataModelTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create(['name' => 'Test Account']);
        $this->tenant->makeCurrent();
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    public function test_facility_has_new_schema_extension_columns(): void
    {
        $facility = Facility::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'currency' => 'SAR',
            'type' => 'gym',
            'pricing_mode' => 'per_hour',
            'requires_booking' => true,
            'booking_horizon_days' => 30,
            'cancellation_hours_before' => 4,
            'min_booking_duration_minutes' => 60,
            'max_booking_duration_minutes' => 120,
            'contract_required' => false,
            'notes' => 'Open to all residents.',
        ]);

        $this->assertDatabaseHas('rf_facilities', [
            'id' => $facility->id,
            'currency' => 'SAR',
            'type' => 'gym',
            'pricing_mode' => 'per_hour',
            'requires_booking' => true,
            'booking_horizon_days' => 30,
            'cancellation_hours_before' => 4,
            'min_booking_duration_minutes' => 60,
            'max_booking_duration_minutes' => 120,
            'contract_required' => false,
            'notes' => 'Open to all residents.',
        ]);

        $this->assertEquals('SAR', $facility->currency);
        $this->assertEquals('gym', $facility->type);
        $this->assertTrue($facility->requires_booking);
        $this->assertFalse($facility->contract_required);
    }

    public function test_facility_availability_rule_can_be_created_and_retrieved(): void
    {
        $facility = Facility::factory()->create([
            'account_tenant_id' => $this->tenant->id,
        ]);

        $rule = FacilityAvailabilityRule::factory()->create([
            'facility_id' => $facility->id,
            'day_of_week' => 1, // Monday
            'open_time' => '06:00',
            'close_time' => '22:00',
            'slot_duration_minutes' => 60,
            'max_concurrent_bookings' => 2,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('rf_facility_availability_rules', [
            'facility_id' => $facility->id,
            'day_of_week' => 1,
            'open_time' => '06:00',
            'close_time' => '22:00',
            'slot_duration_minutes' => 60,
            'max_concurrent_bookings' => 2,
            'is_active' => true,
        ]);

        $this->assertEquals($facility->id, $rule->facility->id);
        $this->assertCount(1, $facility->availabilityRules);
    }

    public function test_facility_availability_rules_unique_per_day_of_week(): void
    {
        $facility = Facility::factory()->create([
            'account_tenant_id' => $this->tenant->id,
        ]);

        FacilityAvailabilityRule::factory()->create([
            'facility_id' => $facility->id,
            'day_of_week' => 1,
        ]);

        $this->expectException(UniqueConstraintViolationException::class);

        FacilityAvailabilityRule::factory()->create([
            'facility_id' => $facility->id,
            'day_of_week' => 1,
        ]);
    }

    public function test_facility_booking_has_new_cancellation_columns(): void
    {
        $facility = Facility::factory()->create([
            'account_tenant_id' => $this->tenant->id,
        ]);

        $status = Status::factory()->create(['type' => 'facility_booking']);

        $booking = FacilityBooking::factory()->create([
            'facility_id' => $facility->id,
            'account_tenant_id' => $this->tenant->id,
            'status_id' => $status->id,
            'start_at' => '2026-05-01 10:00:00',
            'end_at' => '2026-05-01 11:00:00',
            'cancelled_at' => null,
            'cancellation_reason' => null,
            'cancellation_by_type' => null,
        ]);

        $this->assertDatabaseHas('rf_facility_bookings', [
            'id' => $booking->id,
            'account_tenant_id' => $this->tenant->id,
            'cancelled_at' => null,
            'cancellation_reason' => null,
        ]);

        $booking->update([
            'cancelled_at' => now(),
            'cancellation_reason' => 'Resident requested cancellation',
            'cancellation_by_type' => 'resident',
        ]);

        $this->assertNotNull($booking->fresh()->cancelled_at);
        $this->assertEquals('resident', $booking->fresh()->cancellation_by_type);
    }

    public function test_facility_booking_overlapping_scope(): void
    {
        $facility = Facility::factory()->create([
            'account_tenant_id' => $this->tenant->id,
        ]);

        $status = Status::factory()->create(['type' => 'facility_booking']);

        FacilityBooking::factory()->create([
            'facility_id' => $facility->id,
            'account_tenant_id' => $this->tenant->id,
            'status_id' => $status->id,
            'start_at' => '2026-05-01 10:00:00',
            'end_at' => '2026-05-01 12:00:00',
        ]);

        // Overlapping booking
        $overlapping = FacilityBooking::overlapping(
            $facility->id,
            '2026-05-01 11:00:00',
            '2026-05-01 13:00:00'
        )->get();

        $this->assertCount(1, $overlapping);

        // Non-overlapping booking
        $nonOverlapping = FacilityBooking::overlapping(
            $facility->id,
            '2026-05-01 12:00:00',
            '2026-05-01 14:00:00'
        )->get();

        $this->assertCount(0, $nonOverlapping);
    }

    public function test_facility_waitlist_can_be_created(): void
    {
        $facility = Facility::factory()->create([
            'account_tenant_id' => $this->tenant->id,
        ]);

        $resident = Resident::factory()->create([
            'account_tenant_id' => $this->tenant->id,
        ]);

        $waitlistEntry = FacilityWaitlist::factory()->create([
            'facility_id' => $facility->id,
            'resident_id' => $resident->id,
            'requested_start_at' => '2026-05-01 10:00:00',
            'requested_end_at' => '2026-05-01 11:00:00',
        ]);

        $this->assertDatabaseHas('rf_facility_waitlist', [
            'facility_id' => $facility->id,
            'resident_id' => $resident->id,
        ]);

        $this->assertEquals($facility->id, $waitlistEntry->facility->id);
        $this->assertEquals($resident->id, $waitlistEntry->resident->id);
    }

    public function test_facility_waitlist_for_slot_scope_orders_fifo(): void
    {
        $facility = Facility::factory()->create([
            'account_tenant_id' => $this->tenant->id,
        ]);

        $residentA = Resident::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $residentB = Resident::factory()->create(['account_tenant_id' => $this->tenant->id]);

        $firstEntry = FacilityWaitlist::factory()->create([
            'facility_id' => $facility->id,
            'resident_id' => $residentA->id,
            'requested_start_at' => '2026-05-01 10:00:00',
            'requested_end_at' => '2026-05-01 11:00:00',
            'created_at' => Carbon::parse('2026-04-20 08:00:00'),
        ]);

        $secondEntry = FacilityWaitlist::factory()->create([
            'facility_id' => $facility->id,
            'resident_id' => $residentB->id,
            'requested_start_at' => '2026-05-01 10:00:00',
            'requested_end_at' => '2026-05-01 11:00:00',
            'created_at' => Carbon::parse('2026-04-20 09:00:00'),
        ]);

        $entries = FacilityWaitlist::forSlot(
            $facility->id,
            '2026-05-01 10:00:00',
            '2026-05-01 11:00:00'
        )->get();

        $this->assertEquals($firstEntry->id, $entries->first()->id);
        $this->assertEquals($secondEntry->id, $entries->last()->id);
    }

    public function test_facility_scope_active_filters_inactive_facilities(): void
    {
        $activeFacility = Facility::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'is_active' => true,
        ]);

        Facility::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'is_active' => false,
        ]);

        $active = Facility::active()->get();

        $this->assertCount(1, $active);
        $this->assertEquals($activeFacility->id, $active->first()->id);
    }

    public function test_availability_rule_cascades_delete_with_facility(): void
    {
        $facility = Facility::factory()->create([
            'account_tenant_id' => $this->tenant->id,
        ]);

        FacilityAvailabilityRule::factory()->create([
            'facility_id' => $facility->id,
            'day_of_week' => 1,
        ]);

        $facilityId = $facility->id;

        // Hard delete (force) to bypass soft-delete
        $facility->forceDelete();

        $this->assertDatabaseMissing('rf_facility_availability_rules', [
            'facility_id' => $facilityId,
        ]);
    }
}
