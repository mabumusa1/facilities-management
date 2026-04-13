<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilityBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_facility_booking(): void
    {
        $tenant = Tenant::factory()->create();
        $facility = Facility::factory()->create(['tenant_id' => $tenant->id]);
        $contact = Contact::factory()->create(['tenant_id' => $tenant->id]);
        $status = Status::factory()->create([
            'domain' => 'facility_booking',
            'slug' => 'facility_booking_pending',
        ]);

        $booking = FacilityBooking::factory()->create([
            'tenant_id' => $tenant->id,
            'facility_id' => $facility->id,
            'contact_id' => $contact->id,
            'status_id' => $status->id,
        ]);

        $this->assertDatabaseHas('facility_bookings', [
            'id' => $booking->id,
            'facility_id' => $facility->id,
        ]);
    }

    public function test_belongs_to_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $booking = FacilityBooking::factory()->create(['tenant_id' => $tenant->id]);

        $this->assertInstanceOf(Tenant::class, $booking->tenant);
        $this->assertEquals($tenant->id, $booking->tenant->id);
    }

    public function test_belongs_to_facility(): void
    {
        $facility = Facility::factory()->create();
        $booking = FacilityBooking::factory()->create(['facility_id' => $facility->id]);

        $this->assertInstanceOf(Facility::class, $booking->facility);
        $this->assertEquals($facility->id, $booking->facility->id);
    }

    public function test_belongs_to_contact(): void
    {
        $contact = Contact::factory()->create();
        $booking = FacilityBooking::factory()->create(['contact_id' => $contact->id]);

        $this->assertInstanceOf(Contact::class, $booking->contact);
        $this->assertEquals($contact->id, $booking->contact->id);
    }

    public function test_belongs_to_unit(): void
    {
        $unit = Unit::factory()->create();
        $booking = FacilityBooking::factory()->create(['unit_id' => $unit->id]);

        $this->assertInstanceOf(Unit::class, $booking->unit);
        $this->assertEquals($unit->id, $booking->unit->id);
    }

    public function test_belongs_to_status(): void
    {
        $status = Status::factory()->create();
        $booking = FacilityBooking::factory()->create(['status_id' => $status->id]);

        $this->assertInstanceOf(Status::class, $booking->status);
        $this->assertEquals($status->id, $booking->status->id);
    }

    public function test_can_approve_booking(): void
    {
        Status::factory()->create([
            'domain' => 'facility_booking',
            'slug' => 'facility_booking_booked',
            'name' => 'Booked',
        ]);

        $approver = Contact::factory()->create();
        $booking = FacilityBooking::factory()->pending()->create();

        $booking->approve($approver->id);

        $this->assertDatabaseHas('facility_bookings', [
            'id' => $booking->id,
            'approved_by' => $approver->id,
        ]);
        $this->assertNotNull($booking->fresh()->approved_at);
    }

    public function test_can_reject_booking(): void
    {
        Status::factory()->create([
            'domain' => 'facility_booking',
            'slug' => 'facility_booking_rejected',
            'name' => 'Rejected',
        ]);

        $booking = FacilityBooking::factory()->pending()->create();

        $booking->reject('Facility not available');

        $this->assertDatabaseHas('facility_bookings', [
            'id' => $booking->id,
            'cancellation_reason' => 'Facility not available',
        ]);
    }

    public function test_can_schedule_booking(): void
    {
        Status::factory()->create([
            'domain' => 'facility_booking',
            'slug' => 'facility_booking_scheduled',
            'name' => 'Scheduled',
        ]);

        $booking = FacilityBooking::factory()->booked()->create();

        $booking->schedule();

        $this->assertTrue($booking->fresh()->isScheduled());
    }

    public function test_can_complete_booking(): void
    {
        Status::factory()->create([
            'domain' => 'facility_booking',
            'slug' => 'facility_booking_completed',
            'name' => 'Completed',
        ]);

        $booking = FacilityBooking::factory()->scheduled()->create();

        $booking->complete();

        $this->assertTrue($booking->fresh()->isCompleted());
    }

    public function test_can_cancel_booking(): void
    {
        Status::factory()->create([
            'domain' => 'facility_booking',
            'slug' => 'facility_booking_canceled',
            'name' => 'Canceled',
        ]);

        $booking = FacilityBooking::factory()->pending()->create();

        $booking->cancel('User requested cancellation');

        $this->assertTrue($booking->fresh()->isCanceled());
        $this->assertNotNull($booking->fresh()->canceled_at);
    }

    public function test_can_check_in(): void
    {
        $contact = Contact::factory()->create();
        $booking = FacilityBooking::factory()->scheduled()->create();

        $booking->checkIn($contact->id);

        $this->assertTrue($booking->fresh()->isCheckedIn());
        $this->assertEquals($contact->id, $booking->fresh()->checked_in_by);
    }

    public function test_can_check_out(): void
    {
        $contact = Contact::factory()->create();
        $booking = FacilityBooking::factory()->scheduled()->create([
            'checked_in_at' => now()->subHour(),
        ]);

        $booking->checkOut($contact->id);

        $this->assertTrue($booking->fresh()->isCheckedOut());
        $this->assertEquals($contact->id, $booking->fresh()->checked_out_by);
    }

    public function test_checkout_auto_completes_scheduled_booking(): void
    {
        Status::factory()->create([
            'domain' => 'facility_booking',
            'slug' => 'facility_booking_completed',
            'name' => 'Completed',
        ]);

        $booking = FacilityBooking::factory()->scheduled()->create([
            'checked_in_at' => now()->subHour(),
        ]);

        $booking->checkOut();

        $this->assertTrue($booking->fresh()->isCompleted());
    }

    public function test_is_pending_returns_true_for_pending_booking(): void
    {
        $booking = FacilityBooking::factory()->pending()->create();

        $this->assertTrue($booking->isPending());
    }

    public function test_is_booked_returns_true_for_booked_booking(): void
    {
        $booking = FacilityBooking::factory()->booked()->create();

        $this->assertTrue($booking->isBooked());
    }

    public function test_is_scheduled_returns_true_for_scheduled_booking(): void
    {
        $booking = FacilityBooking::factory()->scheduled()->create();

        $this->assertTrue($booking->isScheduled());
    }

    public function test_is_completed_returns_true_for_completed_booking(): void
    {
        $booking = FacilityBooking::factory()->completed()->create();

        $this->assertTrue($booking->isCompleted());
    }

    public function test_is_rejected_returns_true_for_rejected_booking(): void
    {
        $booking = FacilityBooking::factory()->rejected()->create();

        $this->assertTrue($booking->isRejected());
    }

    public function test_is_canceled_returns_true_for_canceled_booking(): void
    {
        $booking = FacilityBooking::factory()->canceled()->create();

        $this->assertTrue($booking->isCanceled());
    }

    public function test_soft_deletes_facility_booking(): void
    {
        $booking = FacilityBooking::factory()->create();

        $booking->delete();

        $this->assertSoftDeleted('facility_bookings', ['id' => $booking->id]);
    }
}
