<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Community;
use App\Models\Country;
use App\Models\Currency;
use App\Models\District;
use App\Models\Lease;
use App\Models\LeaseAdditionalFee;
use App\Models\LeaseEscalation;
use App\Models\Payment;
use App\Models\Resident;
use App\Models\Status;
use App\Models\Transaction;
use App\Models\TransactionAdditionalFee;
use App\Models\Unit;
use App\Models\UnitCategory;
use App\Models\UnitType;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class LeaseFinanceTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function createLease(): Lease
    {
        return Lease::factory()->create();
    }

    private function createTransactionForLease(Lease $lease, float $amount): Transaction
    {
        $status = Status::factory()->create(['type' => 'transaction']);

        return Transaction::factory()->create([
            'lease_id' => $lease->id,
            'amount' => $amount,
            'status_id' => $status->id,
        ]);
    }

    public function test_lease_factory_creates_valid_model(): void
    {
        $lease = $this->createLease();

        $this->assertModelExists($lease);
        $this->assertNotNull($lease->contract_number);
        $this->assertNotNull($lease->tenant);
        $this->assertNotNull($lease->status);
    }

    public function test_lease_has_many_transactions(): void
    {
        $lease = $this->createLease();
        $status = Status::factory()->create(['type' => 'transaction']);

        Transaction::factory()->count(3)->create([
            'lease_id' => $lease->id,
            'status_id' => $status->id,
        ]);

        $this->assertCount(3, $lease->transactions);
    }

    public function test_lease_units_pivot_with_data(): void
    {
        $lease = $this->createLease();
        $community = Community::factory()->create([
            'country_id' => Country::factory(),
            'currency_id' => Currency::factory(),
            'city_id' => City::factory(),
            'district_id' => District::factory(),
        ]);
        $category = UnitCategory::factory()->create();
        $type = UnitType::factory()->recycle($category)->create();
        $status = Status::factory()->create(['type' => 'unit']);

        $unit = Unit::factory()->create([
            'rf_community_id' => $community->id,
            'category_id' => $category->id,
            'type_id' => $type->id,
            'status_id' => $status->id,
        ]);

        $lease->units()->attach($unit, [
            'rental_annual_type' => 'fixed',
            'annual_rental_amount' => 50000.00,
            'net_area' => 120.50,
            'meter_cost' => 415.00,
        ]);

        $this->assertCount(1, $lease->units);
        $this->assertEquals('50000.00', $lease->units->first()->pivot->annual_rental_amount);
    }

    public function test_transaction_partial_payment(): void
    {
        $lease = $this->createLease();
        $transaction = $this->createTransactionForLease($lease, 10000.00);

        Payment::factory()->create([
            'transaction_id' => $transaction->id,
            'amount' => 3000.00,
            'payment_date' => now(),
        ]);

        $transaction->refresh();

        $this->assertEquals('3000.00', $transaction->paid);
        $this->assertEquals('7000.00', $transaction->left);
    }

    public function test_transaction_full_settlement(): void
    {
        $lease = $this->createLease();
        $transaction = $this->createTransactionForLease($lease, 10000.00);

        Payment::factory()->create([
            'transaction_id' => $transaction->id,
            'amount' => 10000.00,
            'payment_date' => now(),
        ]);

        $transaction->refresh();

        $this->assertEquals('10000.00', $transaction->paid);
        $this->assertEquals('0.00', $transaction->left);
    }

    public function test_transaction_multiple_payments(): void
    {
        $lease = $this->createLease();
        $transaction = $this->createTransactionForLease($lease, 10000.00);

        Payment::factory()->create([
            'transaction_id' => $transaction->id,
            'amount' => 4000.00,
            'payment_date' => now()->subDays(10),
        ]);

        Payment::factory()->create([
            'transaction_id' => $transaction->id,
            'amount' => 6000.00,
            'payment_date' => now(),
        ]);

        $transaction->refresh();

        $this->assertEquals('10000.00', $transaction->paid);
        $this->assertEquals('0.00', $transaction->left);
    }

    public function test_lease_total_unpaid_amount(): void
    {
        $lease = $this->createLease();
        $status = Status::factory()->create(['type' => 'transaction']);

        $t1 = Transaction::factory()->create([
            'lease_id' => $lease->id,
            'amount' => 5000.00,
            'status_id' => $status->id,
        ]);

        $t2 = Transaction::factory()->create([
            'lease_id' => $lease->id,
            'amount' => 3000.00,
            'status_id' => $status->id,
        ]);

        Payment::factory()->create([
            'transaction_id' => $t1->id,
            'amount' => 2000.00,
            'payment_date' => now(),
        ]);

        // Total: 8000, Paid: 2000, Unpaid: 6000
        $this->assertEquals('6000.00', $lease->total_unpaid_amount);
    }

    public function test_lease_unpaid_transactions_count(): void
    {
        $lease = $this->createLease();
        $status = Status::factory()->create(['type' => 'transaction']);

        Transaction::factory()->create([
            'lease_id' => $lease->id,
            'is_paid' => false,
            'status_id' => $status->id,
        ]);

        Transaction::factory()->create([
            'lease_id' => $lease->id,
            'is_paid' => true,
            'status_id' => $status->id,
        ]);

        Transaction::factory()->create([
            'lease_id' => $lease->id,
            'is_paid' => false,
            'status_id' => $status->id,
        ]);

        $this->assertEquals(2, $lease->unpaid_transactions_count);
    }

    public function test_lease_additional_fees(): void
    {
        $lease = $this->createLease();

        LeaseAdditionalFee::factory()->count(2)->create(['lease_id' => $lease->id]);

        $this->assertCount(2, $lease->additionalFees);
    }

    public function test_lease_escalations(): void
    {
        $lease = $this->createLease();

        LeaseEscalation::factory()->count(3)->create(['lease_id' => $lease->id]);

        $this->assertCount(3, $lease->escalations);
    }

    public function test_transaction_additional_fees(): void
    {
        $lease = $this->createLease();
        $transaction = $this->createTransactionForLease($lease, 10000.00);

        TransactionAdditionalFee::factory()->count(2)->create([
            'transaction_id' => $transaction->id,
        ]);

        $this->assertCount(2, $transaction->additionalFees);
    }

    public function test_transaction_polymorphic_assignee(): void
    {
        $resident = Resident::factory()->create();
        $status = Status::factory()->create(['type' => 'transaction']);

        $transaction = Transaction::factory()->create([
            'assignee_type' => Resident::class,
            'assignee_id' => $resident->id,
            'status_id' => $status->id,
        ]);

        $this->assertTrue($transaction->assignee->is($resident));
    }

    public function test_lease_soft_deletes(): void
    {
        $lease = $this->createLease();
        $lease->delete();

        $this->assertSoftDeleted($lease);
    }

    public function test_lease_sublease_relationship(): void
    {
        $parentLease = $this->createLease();
        $subLease = Lease::factory()->create([
            'parent_lease_id' => $parentLease->id,
            'is_sub_lease' => true,
        ]);

        $this->assertTrue($subLease->parentLease->is($parentLease));
        $this->assertTrue($parentLease->subleases->contains($subLease));
    }
}
