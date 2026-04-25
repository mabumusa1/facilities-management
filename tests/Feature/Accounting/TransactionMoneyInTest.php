<?php

namespace Tests\Feature\Accounting;

use App\Models\AccountMembership;
use App\Models\InvoiceSetting;
use App\Models\Resident;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\Unit;
use App\Models\User;
use App\Services\Accounting\InvoiceSettingGate;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TransactionMoneyInTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create(['name' => 'Accounting Test Tenant']);
        $this->tenant->makeCurrent();

        $this->user = User::factory()->create();

        AccountMembership::create([
            'user_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);

        // Grant required permissions for account_admins
        $this->actingAs($this->user);
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    // -----------------------------------------------------------------------
    // InvoiceSettingGate tests
    // -----------------------------------------------------------------------

    public function test_invoice_setting_gate_returns_false_when_no_setting_exists(): void
    {
        $gate = new InvoiceSettingGate;

        $this->assertFalse($gate->isComplete());
    }

    public function test_invoice_setting_gate_returns_false_when_company_name_empty(): void
    {
        InvoiceSetting::factory()->create([
            'company_name' => null,
            'instructions' => 'Some instructions',
            'account_tenant_id' => $this->tenant->id,
        ]);

        $gate = new InvoiceSettingGate;

        $this->assertFalse($gate->isComplete());
    }

    public function test_invoice_setting_gate_returns_false_when_instructions_empty(): void
    {
        InvoiceSetting::factory()->create([
            'company_name' => 'Acme Corp',
            'instructions' => null,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $gate = new InvoiceSettingGate;

        $this->assertFalse($gate->isComplete());
    }

    public function test_invoice_setting_gate_returns_true_when_complete(): void
    {
        InvoiceSetting::factory()->create([
            'company_name' => 'Acme Corp',
            'instructions' => 'Pay within 30 days',
            'account_tenant_id' => $this->tenant->id,
        ]);

        $gate = new InvoiceSettingGate;

        $this->assertTrue($gate->isComplete());
    }

    // -----------------------------------------------------------------------
    // Transaction store — happy path with InvoiceSetting complete
    // -----------------------------------------------------------------------

    public function test_store_money_in_transaction_creates_transaction_and_receipt_when_settings_complete(): void
    {
        $this->withoutVite();

        InvoiceSetting::factory()->create([
            'company_name' => 'Acme Corp',
            'instructions' => 'Pay within 30 days',
            'account_tenant_id' => $this->tenant->id,
        ]);

        $status = Status::factory()->create([
            'type' => 'invoice',
            'account_tenant_id' => $this->tenant->id,
        ]);

        $category = Setting::create([
            'name' => 'Rent',
            'name_en' => 'Rent',
            'type' => 'transaction_category',
            'subtype' => 'income',
            'account_tenant_id' => $this->tenant->id,
        ]);

        $unit = Unit::factory()->create([
            'account_tenant_id' => $this->tenant->id,
        ]);

        $resident = Resident::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'email' => 'tenant@example.com',
        ]);

        $payload = [
            'category_id' => $category->id,
            'type_id' => 1,
            'status_id' => $status->id,
            'unit_id' => $unit->id,
            'assignee_id' => "resident:{$resident->id}",
            'amount' => '5000.00',
            'tax_amount' => '750.00',
            'due_date' => '2026-04-25',
            'direction' => 'money_in',
            'payment_method' => 'cash',
            'generate_receipt' => true,
        ];

        $response = $this->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('transactions.store'), $payload);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $transaction = Transaction::query()
            ->where('account_tenant_id', $this->tenant->id)
            ->where('category_id', $category->id)
            ->first();

        $this->assertNotNull($transaction);
        $this->assertEquals('money_in', $transaction->direction);
        $this->assertEquals('cash', $transaction->payment_method);
        $this->assertEquals('5000.00', $transaction->amount);

        $receipt = $transaction->receipt;
        $this->assertNotNull($receipt);
        $this->assertEquals('generated', $receipt->status);
    }

    // -----------------------------------------------------------------------
    // Transaction store — InvoiceSetting incomplete → receipt blocked
    // -----------------------------------------------------------------------

    public function test_store_money_in_transaction_creates_receipt_with_settings_incomplete_status(): void
    {
        $this->withoutVite();

        // InvoiceSetting exists but incomplete (no instructions)
        InvoiceSetting::factory()->create([
            'company_name' => 'Acme Corp',
            'instructions' => null,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $status = Status::factory()->create([
            'type' => 'invoice',
            'account_tenant_id' => $this->tenant->id,
        ]);

        $category = Setting::create([
            'name' => 'Rent',
            'name_en' => 'Rent',
            'type' => 'transaction_category',
            'subtype' => 'income',
            'account_tenant_id' => $this->tenant->id,
        ]);

        $unit = Unit::factory()->create([
            'account_tenant_id' => $this->tenant->id,
        ]);

        $resident = Resident::factory()->create([
            'account_tenant_id' => $this->tenant->id,
        ]);

        $payload = [
            'category_id' => $category->id,
            'type_id' => 1,
            'status_id' => $status->id,
            'unit_id' => $unit->id,
            'assignee_id' => "resident:{$resident->id}",
            'amount' => '3000.00',
            'due_date' => '2026-04-25',
            'direction' => 'money_in',
            'payment_method' => 'bank_transfer',
            'reference_number' => 'TRF-20260425',
            'generate_receipt' => false,
        ];

        $response = $this->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('transactions.store'), $payload);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $transaction = Transaction::query()
            ->where('account_tenant_id', $this->tenant->id)
            ->where('reference_number', 'TRF-20260425')
            ->first();

        $this->assertNotNull($transaction);
        $this->assertEquals('bank_transfer', $transaction->payment_method);
        $this->assertEquals('TRF-20260425', $transaction->reference_number);

        $receipt = $transaction->receipt;
        $this->assertNotNull($receipt);
        $this->assertEquals('settings_incomplete', $receipt->status);
    }

    // -----------------------------------------------------------------------
    // Validation — missing required money-in fields
    // -----------------------------------------------------------------------

    public function test_store_fails_validation_when_payment_method_missing(): void
    {
        $this->withoutVite();

        $status = Status::factory()->create([
            'type' => 'invoice',
            'account_tenant_id' => $this->tenant->id,
        ]);

        $category = Setting::create([
            'name' => 'Rent',
            'type' => 'transaction_category',
            'subtype' => 'income',
            'account_tenant_id' => $this->tenant->id,
        ]);

        $unit = Unit::factory()->create([
            'account_tenant_id' => $this->tenant->id,
        ]);

        $resident = Resident::factory()->create([
            'account_tenant_id' => $this->tenant->id,
        ]);

        $payload = [
            'category_id' => $category->id,
            'type_id' => 1,
            'status_id' => $status->id,
            'unit_id' => $unit->id,
            'assignee_id' => "resident:{$resident->id}",
            'amount' => '5000.00',
            'due_date' => '2026-04-25',
            'direction' => 'money_in',
            // payment_method intentionally omitted
        ];

        $response = $this->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('transactions.store'), $payload);

        $response->assertSessionHasErrors('payment_method');
    }
}
