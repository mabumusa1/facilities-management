<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\MarketplaceCustomer;
use App\Models\MarketplaceUnit;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketplaceCustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_marketplace_customer(): void
    {
        $customer = MarketplaceCustomer::factory()->create();

        $this->assertDatabaseHas('marketplace_customers', [
            'id' => $customer->id,
            'email' => $customer->email,
        ]);
    }

    public function test_belongs_to_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $customer = MarketplaceCustomer::factory()->create(['tenant_id' => $tenant->id]);

        $this->assertInstanceOf(Tenant::class, $customer->tenant);
        $this->assertEquals($tenant->id, $customer->tenant->id);
    }

    public function test_belongs_to_contact(): void
    {
        $contact = Contact::factory()->create();
        $customer = MarketplaceCustomer::factory()->create(['contact_id' => $contact->id]);

        $this->assertInstanceOf(Contact::class, $customer->contact);
        $this->assertEquals($contact->id, $customer->contact->id);
    }

    public function test_belongs_to_agent(): void
    {
        $agent = Contact::factory()->create();
        $customer = MarketplaceCustomer::factory()->create(['assigned_agent' => $agent->id]);

        $this->assertInstanceOf(Contact::class, $customer->agent);
        $this->assertEquals($agent->id, $customer->agent->id);
    }

    public function test_belongs_to_converted_unit(): void
    {
        $unit = MarketplaceUnit::factory()->create();
        $customer = MarketplaceCustomer::factory()->converted()->create(['converted_unit_id' => $unit->id]);

        $this->assertInstanceOf(MarketplaceUnit::class, $customer->convertedUnit);
        $this->assertEquals($unit->id, $customer->convertedUnit->id);
    }

    public function test_full_name_attribute(): void
    {
        $customer = MarketplaceCustomer::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->assertEquals('John Doe', $customer->full_name);
    }

    public function test_can_qualify_customer(): void
    {
        $customer = MarketplaceCustomer::factory()->lead()->create();

        $customer->qualify();

        $this->assertTrue($customer->fresh()->isQualified());
    }

    public function test_can_start_negotiation(): void
    {
        $customer = MarketplaceCustomer::factory()->qualified()->create();

        $customer->startNegotiation();

        $this->assertTrue($customer->fresh()->isNegotiating());
    }

    public function test_can_convert_customer(): void
    {
        $unit = MarketplaceUnit::factory()->create();
        $customer = MarketplaceCustomer::factory()->negotiating()->create();

        $customer->convert($unit->id);

        $fresh = $customer->fresh();
        $this->assertTrue($fresh->isConverted());
        $this->assertEquals($unit->id, $fresh->converted_unit_id);
        $this->assertNotNull($fresh->converted_at);
    }

    public function test_can_deactivate_customer(): void
    {
        $customer = MarketplaceCustomer::factory()->active()->create();

        $customer->deactivate();

        $this->assertTrue($customer->fresh()->isInactive());
    }

    public function test_can_reactivate_customer(): void
    {
        $customer = MarketplaceCustomer::factory()->inactive()->create();

        $customer->reactivate();

        $this->assertTrue($customer->fresh()->isActive());
    }

    public function test_can_assign_agent(): void
    {
        $agent = Contact::factory()->create();
        $customer = MarketplaceCustomer::factory()->create(['assigned_agent' => null]);

        $customer->assignAgent($agent->id);

        $this->assertEquals($agent->id, $customer->fresh()->assigned_agent);
    }

    public function test_can_update_lead_score(): void
    {
        $customer = MarketplaceCustomer::factory()->create(['lead_score' => 10]);

        $customer->updateLeadScore(75);

        $this->assertEquals(75, $customer->fresh()->lead_score);
    }

    public function test_can_increment_lead_score(): void
    {
        $customer = MarketplaceCustomer::factory()->create(['lead_score' => 50]);

        $customer->incrementLeadScore(10);

        $this->assertEquals(60, $customer->fresh()->lead_score);
    }

    public function test_is_lead_returns_true_for_lead(): void
    {
        $customer = MarketplaceCustomer::factory()->lead()->create();

        $this->assertTrue($customer->isLead());
    }

    public function test_is_active_returns_true_for_active(): void
    {
        $customer = MarketplaceCustomer::factory()->active()->create();

        $this->assertTrue($customer->isActive());
    }

    public function test_is_qualified_returns_true_for_qualified(): void
    {
        $customer = MarketplaceCustomer::factory()->qualified()->create();

        $this->assertTrue($customer->isQualified());
    }

    public function test_is_negotiating_returns_true_for_negotiating(): void
    {
        $customer = MarketplaceCustomer::factory()->negotiating()->create();

        $this->assertTrue($customer->isNegotiating());
    }

    public function test_is_converted_returns_true_for_converted(): void
    {
        $customer = MarketplaceCustomer::factory()->converted()->create();

        $this->assertTrue($customer->isConverted());
    }

    public function test_is_inactive_returns_true_for_inactive(): void
    {
        $customer = MarketplaceCustomer::factory()->inactive()->create();

        $this->assertTrue($customer->isInactive());
    }

    public function test_is_buyer_returns_true_for_buyer(): void
    {
        $customer = MarketplaceCustomer::factory()->buyer()->create();

        $this->assertTrue($customer->isBuyer());
    }

    public function test_is_renter_returns_true_for_renter(): void
    {
        $customer = MarketplaceCustomer::factory()->renter()->create();

        $this->assertTrue($customer->isRenter());
    }

    public function test_is_investor_returns_true_for_investor(): void
    {
        $customer = MarketplaceCustomer::factory()->investor()->create();

        $this->assertTrue($customer->isInvestor());
    }

    public function test_is_within_budget_returns_true_for_matching_price(): void
    {
        $customer = MarketplaceCustomer::factory()->create([
            'budget_min' => 100000,
            'budget_max' => 500000,
        ]);

        $this->assertTrue($customer->isWithinBudget(300000));
    }

    public function test_is_within_budget_returns_false_for_too_low_price(): void
    {
        $customer = MarketplaceCustomer::factory()->create([
            'budget_min' => 100000,
            'budget_max' => 500000,
        ]);

        $this->assertFalse($customer->isWithinBudget(50000));
    }

    public function test_is_within_budget_returns_false_for_too_high_price(): void
    {
        $customer = MarketplaceCustomer::factory()->create([
            'budget_min' => 100000,
            'budget_max' => 500000,
        ]);

        $this->assertFalse($customer->isWithinBudget(600000));
    }

    public function test_soft_deletes_marketplace_customer(): void
    {
        $customer = MarketplaceCustomer::factory()->create();

        $customer->delete();

        $this->assertSoftDeleted('marketplace_customers', ['id' => $customer->id]);
    }
}
