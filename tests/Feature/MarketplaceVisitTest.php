<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\MarketplaceCustomer;
use App\Models\MarketplaceUnit;
use App\Models\MarketplaceVisit;
use App\Models\Status;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketplaceVisitTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_marketplace_visit(): void
    {
        $visit = MarketplaceVisit::factory()->create();

        $this->assertDatabaseHas('marketplace_visits', [
            'id' => $visit->id,
        ]);
    }

    public function test_belongs_to_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $visit = MarketplaceVisit::factory()->create(['tenant_id' => $tenant->id]);

        $this->assertInstanceOf(Tenant::class, $visit->tenant);
        $this->assertEquals($tenant->id, $visit->tenant->id);
    }

    public function test_belongs_to_marketplace_unit(): void
    {
        $unit = MarketplaceUnit::factory()->create();
        $visit = MarketplaceVisit::factory()->create(['marketplace_unit_id' => $unit->id]);

        $this->assertInstanceOf(MarketplaceUnit::class, $visit->marketplaceUnit);
        $this->assertEquals($unit->id, $visit->marketplaceUnit->id);
    }

    public function test_belongs_to_customer(): void
    {
        $customer = MarketplaceCustomer::factory()->create();
        $visit = MarketplaceVisit::factory()->create(['marketplace_customer_id' => $customer->id]);

        $this->assertInstanceOf(MarketplaceCustomer::class, $visit->customer);
        $this->assertEquals($customer->id, $visit->customer->id);
    }

    public function test_belongs_to_status(): void
    {
        $status = Status::factory()->create([
            'domain' => 'marketplace_visit',
            'slug' => 'marketplace_visit_pending',
        ]);
        $visit = MarketplaceVisit::factory()->create(['status_id' => $status->id]);

        $this->assertInstanceOf(Status::class, $visit->status);
        $this->assertEquals($status->id, $visit->status->id);
    }

    public function test_belongs_to_agent(): void
    {
        $agent = Contact::factory()->create();
        $visit = MarketplaceVisit::factory()->withAgent()->create(['assigned_agent' => $agent->id]);

        $this->assertInstanceOf(Contact::class, $visit->agent);
        $this->assertEquals($agent->id, $visit->agent->id);
    }

    public function test_belongs_to_confirmed_by_contact(): void
    {
        $contact = Contact::factory()->create();
        $visit = MarketplaceVisit::factory()->confirmed()->create(['confirmed_by' => $contact->id]);

        $this->assertInstanceOf(Contact::class, $visit->confirmedByContact);
        $this->assertEquals($contact->id, $visit->confirmedByContact->id);
    }

    public function test_belongs_to_rescheduled_from_visit(): void
    {
        $originalVisit = MarketplaceVisit::factory()->canceled()->create();
        $newVisit = MarketplaceVisit::factory()->create(['rescheduled_from' => $originalVisit->id]);

        $this->assertInstanceOf(MarketplaceVisit::class, $newVisit->rescheduledFromVisit);
        $this->assertEquals($originalVisit->id, $newVisit->rescheduledFromVisit->id);
    }

    public function test_can_confirm_visit(): void
    {
        Status::factory()->create([
            'domain' => 'marketplace_visit',
            'slug' => 'marketplace_visit_confirmed',
            'name' => 'Confirmed',
        ]);
        $contact = Contact::factory()->create();
        $visit = MarketplaceVisit::factory()->pending()->create();

        $visit->confirm($contact->id);

        $fresh = $visit->fresh();
        $this->assertNotNull($fresh->confirmed_at);
        $this->assertEquals($contact->id, $fresh->confirmed_by);
    }

    public function test_can_complete_visit(): void
    {
        Status::factory()->create([
            'domain' => 'marketplace_visit',
            'slug' => 'marketplace_visit_completed',
            'name' => 'Completed',
        ]);
        $visit = MarketplaceVisit::factory()->confirmed()->create();

        $visit->complete('interested', 8);

        $fresh = $visit->fresh();
        $this->assertNotNull($fresh->completed_at);
        $this->assertEquals('interested', $fresh->outcome);
        $this->assertEquals(8, $fresh->interest_level);
    }

    public function test_can_cancel_visit(): void
    {
        Status::factory()->create([
            'domain' => 'marketplace_visit',
            'slug' => 'marketplace_visit_canceled',
            'name' => 'Canceled',
        ]);
        $visit = MarketplaceVisit::factory()->pending()->create();

        $visit->cancel('Customer request');

        $fresh = $visit->fresh();
        $this->assertNotNull($fresh->canceled_at);
        $this->assertEquals('Customer request', $fresh->cancellation_reason);
    }

    public function test_can_mark_as_no_show(): void
    {
        Status::factory()->create([
            'domain' => 'marketplace_visit',
            'slug' => 'marketplace_visit_no_show',
            'name' => 'No Show',
        ]);
        $visit = MarketplaceVisit::factory()->confirmed()->create();

        $visit->markAsNoShow();

        $this->assertTrue($visit->fresh()->isNoShow());
    }

    public function test_can_reschedule_visit(): void
    {
        Status::factory()->create([
            'domain' => 'marketplace_visit',
            'slug' => 'marketplace_visit_canceled',
            'name' => 'Canceled',
        ]);
        $visit = MarketplaceVisit::factory()->pending()->create();
        $newDate = now()->addDays(7)->format('Y-m-d');

        $newVisit = $visit->reschedule($newDate, '14:00:00');

        // Original visit should be canceled
        $this->assertNotNull($visit->fresh()->canceled_at);
        $this->assertEquals('Rescheduled', $visit->fresh()->cancellation_reason);

        // New visit should be created
        $this->assertEquals($newDate, $newVisit->visit_date->format('Y-m-d'));
        $this->assertEquals('14:00:00', $newVisit->visit_time);
        $this->assertEquals($visit->id, $newVisit->rescheduled_from);
        $this->assertNotNull($newVisit->rescheduled_at);
    }

    public function test_can_assign_agent(): void
    {
        $agent = Contact::factory()->create();
        $visit = MarketplaceVisit::factory()->create(['assigned_agent' => null]);

        $visit->assignAgent($agent->id);

        $this->assertEquals($agent->id, $visit->fresh()->assigned_agent);
    }

    public function test_can_add_feedback(): void
    {
        $visit = MarketplaceVisit::factory()->confirmed()->create();

        $visit->addFeedback('Great property, interested in making an offer', 9);

        $fresh = $visit->fresh();
        $this->assertEquals('Great property, interested in making an offer', $fresh->feedback);
        $this->assertEquals(9, $fresh->interest_level);
    }

    public function test_is_pending_returns_true_for_pending(): void
    {
        $status = Status::factory()->create([
            'domain' => 'marketplace_visit',
            'slug' => 'marketplace_visit_pending',
        ]);
        $visit = MarketplaceVisit::factory()->create(['status_id' => $status->id]);

        $this->assertTrue($visit->isPending());
    }

    public function test_is_confirmed_returns_true_for_confirmed(): void
    {
        $visit = MarketplaceVisit::factory()->confirmed()->create();

        $this->assertTrue($visit->isConfirmed());
    }

    public function test_is_completed_returns_true_for_completed(): void
    {
        $visit = MarketplaceVisit::factory()->completed()->create();

        $this->assertTrue($visit->isCompleted());
    }

    public function test_is_canceled_returns_true_for_canceled(): void
    {
        $visit = MarketplaceVisit::factory()->canceled()->create();

        $this->assertTrue($visit->isCanceled());
    }

    public function test_is_no_show_returns_true_for_no_show(): void
    {
        $visit = MarketplaceVisit::factory()->noShow()->create();

        $this->assertTrue($visit->isNoShow());
    }

    public function test_is_rescheduled_returns_true_for_rescheduled(): void
    {
        $originalVisit = MarketplaceVisit::factory()->create();
        $visit = MarketplaceVisit::factory()->create(['rescheduled_from' => $originalVisit->id]);

        $this->assertTrue($visit->isRescheduled());
    }

    public function test_is_upcoming_returns_true_for_future_visit(): void
    {
        $visit = MarketplaceVisit::factory()->pending()->create([
            'visit_date' => now()->addDays(5),
        ]);

        $this->assertTrue($visit->isUpcoming());
    }

    public function test_is_upcoming_returns_false_for_completed_visit(): void
    {
        $visit = MarketplaceVisit::factory()->completed()->create([
            'visit_date' => now()->addDays(5),
        ]);

        $this->assertFalse($visit->isUpcoming());
    }

    public function test_is_today_returns_true_for_today_visit(): void
    {
        $visit = MarketplaceVisit::factory()->today()->create();

        $this->assertTrue($visit->isToday());
    }

    public function test_all_day_visit(): void
    {
        $visit = MarketplaceVisit::factory()->allDay()->create();

        $this->assertTrue($visit->is_all_day);
        $this->assertNull($visit->visit_time);
        $this->assertNull($visit->visit_end_time);
    }

    public function test_soft_deletes_marketplace_visit(): void
    {
        $visit = MarketplaceVisit::factory()->create();

        $visit->delete();

        $this->assertSoftDeleted('marketplace_visits', ['id' => $visit->id]);
    }
}
