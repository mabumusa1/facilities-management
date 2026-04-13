<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\MarketplaceCustomer;
use App\Models\MarketplaceOffer;
use App\Models\MarketplaceUnit;
use App\Models\MarketplaceVisit;
use App\Models\Status;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketplaceOfferTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_marketplace_offer(): void
    {
        $offer = MarketplaceOffer::factory()->create();

        $this->assertDatabaseHas('marketplace_offers', [
            'id' => $offer->id,
        ]);
    }

    public function test_belongs_to_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $offer = MarketplaceOffer::factory()->create(['tenant_id' => $tenant->id]);

        $this->assertInstanceOf(Tenant::class, $offer->tenant);
        $this->assertEquals($tenant->id, $offer->tenant->id);
    }

    public function test_belongs_to_marketplace_unit(): void
    {
        $unit = MarketplaceUnit::factory()->create();
        $offer = MarketplaceOffer::factory()->create(['marketplace_unit_id' => $unit->id]);

        $this->assertInstanceOf(MarketplaceUnit::class, $offer->marketplaceUnit);
        $this->assertEquals($unit->id, $offer->marketplaceUnit->id);
    }

    public function test_belongs_to_customer(): void
    {
        $customer = MarketplaceCustomer::factory()->create();
        $offer = MarketplaceOffer::factory()->create(['marketplace_customer_id' => $customer->id]);

        $this->assertInstanceOf(MarketplaceCustomer::class, $offer->customer);
        $this->assertEquals($customer->id, $offer->customer->id);
    }

    public function test_belongs_to_status(): void
    {
        $status = Status::factory()->create([
            'domain' => 'marketplace_offer',
            'slug' => 'marketplace_offer_draft',
        ]);
        $offer = MarketplaceOffer::factory()->create(['status_id' => $status->id]);

        $this->assertInstanceOf(Status::class, $offer->status);
        $this->assertEquals($status->id, $offer->status->id);
    }

    public function test_belongs_to_agent(): void
    {
        $agent = Contact::factory()->create();
        $offer = MarketplaceOffer::factory()->create(['assigned_agent' => $agent->id]);

        $this->assertInstanceOf(Contact::class, $offer->agent);
        $this->assertEquals($agent->id, $offer->agent->id);
    }

    public function test_belongs_to_reviewer(): void
    {
        $reviewer = Contact::factory()->create();
        $offer = MarketplaceOffer::factory()->approved()->create(['reviewed_by' => $reviewer->id]);

        $this->assertInstanceOf(Contact::class, $offer->reviewer);
        $this->assertEquals($reviewer->id, $offer->reviewer->id);
    }

    public function test_belongs_to_approver(): void
    {
        $approver = Contact::factory()->create();
        $offer = MarketplaceOffer::factory()->approved()->create(['approved_by' => $approver->id]);

        $this->assertInstanceOf(Contact::class, $offer->approver);
        $this->assertEquals($approver->id, $offer->approver->id);
    }

    public function test_belongs_to_contract_signer(): void
    {
        $signer = Contact::factory()->create();
        $offer = MarketplaceOffer::factory()->contracted()->create(['contract_signed_by' => $signer->id]);

        $this->assertInstanceOf(Contact::class, $offer->contractSigner);
        $this->assertEquals($signer->id, $offer->contractSigner->id);
    }

    public function test_belongs_to_parent_offer(): void
    {
        $parentOffer = MarketplaceOffer::factory()->create();
        $counterOffer = MarketplaceOffer::factory()->create([
            'is_counter_offer' => true,
            'parent_offer_id' => $parentOffer->id,
        ]);

        $this->assertInstanceOf(MarketplaceOffer::class, $counterOffer->parentOffer);
        $this->assertEquals($parentOffer->id, $counterOffer->parentOffer->id);
    }

    public function test_has_many_counter_offers(): void
    {
        $offer = MarketplaceOffer::factory()->create();
        MarketplaceOffer::factory()->count(3)->create([
            'is_counter_offer' => true,
            'parent_offer_id' => $offer->id,
        ]);

        $this->assertCount(3, $offer->counterOffers);
    }

    public function test_belongs_to_visit(): void
    {
        $visit = MarketplaceVisit::factory()->create();
        $offer = MarketplaceOffer::factory()->create(['marketplace_visit_id' => $visit->id]);

        $this->assertInstanceOf(MarketplaceVisit::class, $offer->visit);
        $this->assertEquals($visit->id, $offer->visit->id);
    }

    public function test_can_submit_offer(): void
    {
        Status::factory()->create([
            'domain' => 'marketplace_offer',
            'slug' => 'marketplace_offer_submitted',
            'name' => 'Submitted',
        ]);
        $offer = MarketplaceOffer::factory()->draft()->create();

        $offer->submit();

        $this->assertNotNull($offer->fresh()->submitted_at);
    }

    public function test_can_start_negotiation(): void
    {
        Status::factory()->create([
            'domain' => 'marketplace_offer',
            'slug' => 'marketplace_offer_negotiating',
            'name' => 'Negotiating',
        ]);
        $agent = Contact::factory()->create();
        $offer = MarketplaceOffer::factory()->submitted()->create();

        $offer->startNegotiation($agent->id);

        $fresh = $offer->fresh();
        $this->assertTrue($fresh->isNegotiating());
        $this->assertEquals($agent->id, $fresh->assigned_agent);
    }

    public function test_can_create_counter_offer(): void
    {
        $offer = MarketplaceOffer::factory()->negotiating()->create([
            'offer_amount' => 500000,
        ]);

        $counterOffer = $offer->createCounterOffer(480000, 'Our best price');

        $this->assertEquals(480000, $counterOffer->offer_amount);
        $this->assertTrue($counterOffer->isCounterOffer());
        $this->assertEquals($offer->id, $counterOffer->parent_offer_id);
        $this->assertEquals(480000, $offer->fresh()->counter_offer_amount);
        $this->assertEquals('Our best price', $offer->fresh()->agent_response);
    }

    public function test_can_submit_for_review(): void
    {
        Status::factory()->create([
            'domain' => 'marketplace_offer',
            'slug' => 'marketplace_offer_review',
            'name' => 'Under Review',
        ]);
        $offer = MarketplaceOffer::factory()->negotiating()->create();

        $offer->submitForReview();

        $this->assertTrue($offer->fresh()->isUnderReview());
    }

    public function test_can_approve_offer(): void
    {
        Status::factory()->create([
            'domain' => 'marketplace_offer',
            'slug' => 'marketplace_offer_approved',
            'name' => 'Approved',
        ]);
        $approver = Contact::factory()->create();
        $offer = MarketplaceOffer::factory()->underReview()->create();

        $offer->approve($approver->id);

        $fresh = $offer->fresh();
        $this->assertTrue($fresh->isApproved());
        $this->assertEquals($approver->id, $fresh->approved_by);
        $this->assertNotNull($fresh->approved_at);
    }

    public function test_can_reject_offer(): void
    {
        Status::factory()->create([
            'domain' => 'marketplace_offer',
            'slug' => 'marketplace_offer_rejected',
            'name' => 'Rejected',
        ]);
        $reviewer = Contact::factory()->create();
        $offer = MarketplaceOffer::factory()->underReview()->create();

        $offer->reject('Price too low', $reviewer->id);

        $fresh = $offer->fresh();
        $this->assertTrue($fresh->isRejected());
        $this->assertEquals('Price too low', $fresh->rejection_reason);
        $this->assertNotNull($fresh->rejected_at);
    }

    public function test_can_accept_offer(): void
    {
        Status::factory()->create([
            'domain' => 'marketplace_offer',
            'slug' => 'marketplace_offer_accepted',
            'name' => 'Accepted',
        ]);
        $offer = MarketplaceOffer::factory()->approved()->create([
            'offer_amount' => 500000,
        ]);

        $offer->accept();

        $fresh = $offer->fresh();
        $this->assertTrue($fresh->isAccepted());
        $this->assertNotNull($fresh->accepted_at);
        $this->assertEquals(500000, $fresh->final_amount);
    }

    public function test_accept_uses_counter_offer_amount_if_present(): void
    {
        Status::factory()->create([
            'domain' => 'marketplace_offer',
            'slug' => 'marketplace_offer_accepted',
            'name' => 'Accepted',
        ]);
        $offer = MarketplaceOffer::factory()->approved()->create([
            'offer_amount' => 500000,
            'counter_offer_amount' => 480000,
        ]);

        $offer->accept();

        $this->assertEquals(480000, $offer->fresh()->final_amount);
    }

    public function test_can_cancel_offer(): void
    {
        Status::factory()->create([
            'domain' => 'marketplace_offer',
            'slug' => 'marketplace_offer_cancelled',
            'name' => 'Cancelled',
        ]);
        $offer = MarketplaceOffer::factory()->negotiating()->create();

        $offer->cancel('Customer withdrew');

        $fresh = $offer->fresh();
        $this->assertTrue($fresh->isCancelled());
        $this->assertNotNull($fresh->cancelled_at);
        $this->assertEquals('Customer withdrew', $fresh->rejection_reason);
    }

    public function test_can_mark_expired(): void
    {
        Status::factory()->create([
            'domain' => 'marketplace_offer',
            'slug' => 'marketplace_offer_expired',
            'name' => 'Expired',
        ]);
        $offer = MarketplaceOffer::factory()->submitted()->create([
            'valid_until' => now()->subDays(1),
        ]);

        $offer->markExpired();

        $fresh = $offer->fresh();
        $this->assertTrue($fresh->isExpired());
        $this->assertNotNull($fresh->expired_at);
    }

    public function test_can_record_deposit(): void
    {
        $offer = MarketplaceOffer::factory()->accepted()->create();

        $offer->recordDeposit(25000, 'PAY-123456');

        $fresh = $offer->fresh();
        $this->assertEquals(25000, $fresh->booking_deposit);
        $this->assertNotNull($fresh->deposit_paid_at);
        $this->assertEquals('PAY-123456', $fresh->deposit_payment_reference);
    }

    public function test_can_refund_deposit(): void
    {
        $offer = MarketplaceOffer::factory()->withDeposit()->create();

        $offer->refundDeposit();

        $this->assertNotNull($offer->fresh()->deposit_refunded_at);
    }

    public function test_can_sign_contract(): void
    {
        Status::factory()->create([
            'domain' => 'marketplace_offer',
            'slug' => 'marketplace_offer_contracted',
            'name' => 'Contracted',
        ]);
        $signer = Contact::factory()->create();
        $offer = MarketplaceOffer::factory()->accepted()->create();

        $offer->signContract('CNT-123456', $signer->id);

        $fresh = $offer->fresh();
        $this->assertTrue($fresh->hasContract());
        $this->assertEquals('CNT-123456', $fresh->contract_reference);
        $this->assertNotNull($fresh->contract_signed_at);
        $this->assertEquals($signer->id, $fresh->contract_signed_by);
    }

    public function test_can_complete_offer(): void
    {
        Status::factory()->create([
            'domain' => 'marketplace_offer',
            'slug' => 'marketplace_offer_completed',
            'name' => 'Completed',
        ]);
        $offer = MarketplaceOffer::factory()->contracted()->create();

        $offer->complete();

        $fresh = $offer->fresh();
        $this->assertTrue($fresh->isCompleted());
        $this->assertNotNull($fresh->completed_at);
    }

    public function test_can_assign_agent(): void
    {
        $agent = Contact::factory()->create();
        $offer = MarketplaceOffer::factory()->create(['assigned_agent' => null]);

        $offer->assignAgent($agent->id);

        $this->assertEquals($agent->id, $offer->fresh()->assigned_agent);
    }

    public function test_can_generate_reference(): void
    {
        $offer = MarketplaceOffer::factory()->create(['offer_reference' => null]);

        $offer->generateReference();

        $this->assertNotNull($offer->fresh()->offer_reference);
        $this->assertStringStartsWith('OFF-', $offer->fresh()->offer_reference);
    }

    public function test_is_draft_returns_true_for_draft(): void
    {
        $offer = MarketplaceOffer::factory()->draft()->create();

        $this->assertTrue($offer->isDraft());
    }

    public function test_is_submitted_returns_true_for_submitted(): void
    {
        $offer = MarketplaceOffer::factory()->submitted()->create();

        $this->assertTrue($offer->isSubmitted());
    }

    public function test_is_negotiating_returns_true_for_negotiating(): void
    {
        $offer = MarketplaceOffer::factory()->negotiating()->create();

        $this->assertTrue($offer->isNegotiating());
    }

    public function test_is_under_review_returns_true_for_under_review(): void
    {
        $offer = MarketplaceOffer::factory()->underReview()->create();

        $this->assertTrue($offer->isUnderReview());
    }

    public function test_is_approved_returns_true_for_approved(): void
    {
        $offer = MarketplaceOffer::factory()->approved()->create();

        $this->assertTrue($offer->isApproved());
    }

    public function test_is_rejected_returns_true_for_rejected(): void
    {
        $offer = MarketplaceOffer::factory()->rejected()->create();

        $this->assertTrue($offer->isRejected());
    }

    public function test_is_accepted_returns_true_for_accepted(): void
    {
        $offer = MarketplaceOffer::factory()->accepted()->create();

        $this->assertTrue($offer->isAccepted());
    }

    public function test_is_cancelled_returns_true_for_cancelled(): void
    {
        $offer = MarketplaceOffer::factory()->cancelled()->create();

        $this->assertTrue($offer->isCancelled());
    }

    public function test_is_expired_returns_true_for_expired(): void
    {
        $offer = MarketplaceOffer::factory()->expired()->create();

        $this->assertTrue($offer->isExpired());
    }

    public function test_is_expired_returns_true_when_valid_until_is_past(): void
    {
        $offer = MarketplaceOffer::factory()->create([
            'valid_until' => now()->subDays(1),
        ]);

        $this->assertTrue($offer->isExpired());
    }

    public function test_has_contract_returns_true_when_contracted(): void
    {
        $offer = MarketplaceOffer::factory()->contracted()->create();

        $this->assertTrue($offer->hasContract());
    }

    public function test_is_completed_returns_true_for_completed(): void
    {
        $offer = MarketplaceOffer::factory()->completed()->create();

        $this->assertTrue($offer->isCompleted());
    }

    public function test_is_deposit_paid_returns_true_when_deposit_paid(): void
    {
        $offer = MarketplaceOffer::factory()->withDeposit()->create();

        $this->assertTrue($offer->isDepositPaid());
    }

    public function test_is_deposit_paid_returns_false_when_refunded(): void
    {
        $offer = MarketplaceOffer::factory()->withDeposit()->create([
            'deposit_refunded_at' => now(),
        ]);

        $this->assertFalse($offer->isDepositPaid());
    }

    public function test_is_deposit_refunded_returns_true_when_refunded(): void
    {
        $offer = MarketplaceOffer::factory()->withDeposit()->create([
            'deposit_refunded_at' => now(),
        ]);

        $this->assertTrue($offer->isDepositRefunded());
    }

    public function test_is_counter_offer_returns_true_for_counter_offer(): void
    {
        $parentOffer = MarketplaceOffer::factory()->create();
        $offer = MarketplaceOffer::factory()->create([
            'is_counter_offer' => true,
            'parent_offer_id' => $parentOffer->id,
        ]);

        $this->assertTrue($offer->isCounterOffer());
    }

    public function test_is_active_returns_true_for_active_offer(): void
    {
        $offer = MarketplaceOffer::factory()->negotiating()->create();

        $this->assertTrue($offer->isActive());
    }

    public function test_is_active_returns_false_for_cancelled_offer(): void
    {
        $offer = MarketplaceOffer::factory()->cancelled()->create();

        $this->assertFalse($offer->isActive());
    }

    public function test_purchase_offer_type(): void
    {
        $offer = MarketplaceOffer::factory()->purchase()->create();

        $this->assertEquals('purchase', $offer->offer_type);
    }

    public function test_booking_offer_type(): void
    {
        $offer = MarketplaceOffer::factory()->booking()->create();

        $this->assertEquals('booking', $offer->offer_type);
    }

    public function test_lease_offer_type(): void
    {
        $offer = MarketplaceOffer::factory()->lease()->create();

        $this->assertEquals('lease', $offer->offer_type);
    }

    public function test_soft_deletes_marketplace_offer(): void
    {
        $offer = MarketplaceOffer::factory()->create();

        $offer->delete();

        $this->assertSoftDeleted('marketplace_offers', ['id' => $offer->id]);
    }
}
