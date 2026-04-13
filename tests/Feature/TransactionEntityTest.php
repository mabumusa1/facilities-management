<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\TransactionType;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionEntityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure statuses exist
        Status::factory()->create(['id' => 1, 'name' => 'Paid']);
        Status::factory()->create(['id' => 2, 'name' => 'Due']);
    }

    public function test_can_create_transaction(): void
    {
        $tenant = Tenant::factory()->create();
        $assignee = Contact::factory()->tenant()->create(['tenant_id' => $tenant->id]);

        $transaction = Transaction::factory()->create([
            'tenant_id' => $tenant->id,
            'assignee_id' => $assignee->id,
        ]);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'tenant_id' => $tenant->id,
            'assignee_id' => $assignee->id,
        ]);
    }

    public function test_transaction_belongs_to_category(): void
    {
        $transaction = Transaction::factory()->create();

        $this->assertInstanceOf(TransactionCategory::class, $transaction->category);
    }

    public function test_transaction_belongs_to_type(): void
    {
        $transaction = Transaction::factory()->create();

        $this->assertInstanceOf(TransactionType::class, $transaction->type);
    }

    public function test_transaction_belongs_to_assignee(): void
    {
        $transaction = Transaction::factory()->create();

        $this->assertInstanceOf(Contact::class, $transaction->assignee);
    }

    public function test_can_create_paid_transaction(): void
    {
        $transaction = Transaction::factory()->paid()->create();

        $this->assertTrue($transaction->is_paid);
        $this->assertEquals($transaction->amount, $transaction->paid);
        $this->assertEquals(0, $transaction->left);
    }

    public function test_can_create_unpaid_transaction(): void
    {
        $transaction = Transaction::factory()->unpaid()->create();

        $this->assertFalse($transaction->is_paid);
        $this->assertEquals(0, $transaction->paid);
        $this->assertEquals($transaction->amount, $transaction->left);
    }

    public function test_can_create_overdue_transaction(): void
    {
        $transaction = Transaction::factory()->overdue()->create();

        $this->assertTrue($transaction->isOverdue());
        $this->assertFalse($transaction->is_paid);
    }

    public function test_can_mark_transaction_as_paid(): void
    {
        $transaction = Transaction::factory()->unpaid()->create();

        $transaction->markAsPaid();

        $this->assertTrue($transaction->fresh()->is_paid);
        $this->assertEquals($transaction->amount, $transaction->fresh()->paid);
    }

    public function test_paid_scope_returns_only_paid_transactions(): void
    {
        Transaction::factory()->paid()->count(3)->create();
        Transaction::factory()->unpaid()->count(2)->create();

        $paidTransactions = Transaction::paid()->get();

        $this->assertCount(3, $paidTransactions);
        $this->assertTrue($paidTransactions->every(fn ($t) => $t->is_paid));
    }

    public function test_unpaid_scope_returns_only_unpaid_transactions(): void
    {
        Transaction::factory()->paid()->count(3)->create();
        Transaction::factory()->unpaid()->count(2)->create();

        $unpaidTransactions = Transaction::unpaid()->get();

        $this->assertCount(2, $unpaidTransactions);
        $this->assertTrue($unpaidTransactions->every(fn ($t) => ! $t->is_paid));
    }

    public function test_transaction_can_be_linked_to_unit(): void
    {
        $unit = Unit::factory()->create();
        $transaction = Transaction::factory()->forUnit($unit->id)->create();

        $this->assertInstanceOf(Unit::class, $transaction->unit);
        $this->assertEquals($unit->id, $transaction->unit_id);
    }

    public function test_transaction_formats_amounts_correctly(): void
    {
        $transaction = Transaction::factory()->create(['amount' => 5000.50]);

        $this->assertEquals('5,000.50', $transaction->getAmountFormatted());
    }

    public function test_transaction_calculates_payment_percentage(): void
    {
        $transaction = Transaction::factory()->create([
            'amount' => 1000,
            'paid' => 250,
        ]);

        $this->assertEquals(25.0, $transaction->getPaymentPercentage());
    }
}
