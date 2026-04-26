<?php

namespace Tests\Feature\Http\Accounting;

use App\Models\AccountMembership;
use App\Models\BankAccount;
use App\Models\Community;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use DB;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AccountingApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    private Community $community;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->tenant = Tenant::create(['name' => 'Acct Test']);
        $this->tenant->makeCurrent();

        AccountMembership::create([
            'user_id' => $this->user->id, 'account_tenant_id' => $this->tenant->id, 'role' => 'account_admins',
        ]);

        $this->ensureAccountAdminsRoleExists();
        $this->user->assignRole('accountAdmins');

        $this->actingAs($this->user);
        $this->withSession(['tenant_id' => $this->tenant->id]);

        $this->community = Community::factory()->create(['account_tenant_id' => $this->tenant->id]);
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    private function ensureAccountAdminsRoleExists(): void
    {
        if (! DB::table('roles')->where('name', 'accountAdmins')->where('guard_name', 'web')->exists()) {
            DB::table('roles')->insert([
                'name' => 'accountAdmins', 'guard_name' => 'web',
                'name_en' => 'Account Admins', 'name_ar' => 'مدراء الحسابات',
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    private function createTx(array $overrides = []): Transaction
    {
        return Transaction::create(array_merge([
            'account_tenant_id' => $this->tenant->id,
            'direction' => 'money_in',
            'amount' => 5000,
            'category_id' => null,
            'type_id' => null,
            'status_id' => null,
            'due_on' => now(),
        ], $overrides));
    }

    public function test_store_bank_account(): void
    {
        $response = $this->postJson('/rf/bank-accounts', [
            'bank_name' => 'Al Rajhi Bank',
            'account_name' => 'Main Operating',
            'account_number' => '1234567890',
            'community_id' => $this->community->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('rf_bank_accounts', ['bank_name' => 'Al Rajhi Bank']);
    }

    public function test_bank_account_default_enforces_single(): void
    {
        BankAccount::create([
            'account_tenant_id' => $this->tenant->id,
            'bank_name' => 'Old Default', 'account_name' => 'Old', 'account_number' => '111', 'is_default' => true,
        ]);

        $this->postJson('/rf/bank-accounts', [
            'bank_name' => 'New Default', 'account_name' => 'New', 'account_number' => '222', 'is_default' => true,
        ]);

        $this->assertFalse(BankAccount::where('bank_name', 'Old Default')->value('is_default'));
        $this->assertTrue(BankAccount::where('bank_name', 'New Default')->value('is_default'));
    }

    public function test_list_bank_accounts(): void
    {
        BankAccount::create(['account_tenant_id' => $this->tenant->id, 'bank_name' => 'Test Bank', 'account_name' => 'Test', 'account_number' => '999']);
        $response = $this->getJson('/rf/bank-accounts');
        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('data'));
    }

    public function test_delete_bank_account(): void
    {
        $ba = BankAccount::create(['account_tenant_id' => $this->tenant->id, 'bank_name' => 'X', 'account_name' => 'X', 'account_number' => '0']);
        $response = $this->deleteJson("/rf/bank-accounts/{$ba->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('rf_bank_accounts', ['id' => $ba->id]);
    }

    public function test_reconcile_transaction(): void
    {
        $tx = $this->createTx();
        $response = $this->postJson("/rf/transactions/{$tx->id}/reconcile");
        $response->assertStatus(200);
        $this->assertTrue((bool) $tx->fresh()->is_reconciled);
    }

    public function test_double_reconcile_is_prevented(): void
    {
        $tx = $this->createTx(['is_reconciled' => 1]);

        $response = $this->postJson("/rf/transactions/{$tx->id}/reconcile");
        $response->assertStatus(422);
    }

    public function test_reconciliation_summary(): void
    {
        $this->createTx();
        $this->createTx(['direction' => 'money_out', 'is_reconciled' => 1]);
        $response = $this->getJson('/rf/reconciliation-summary');
        $response->assertStatus(200);
        $this->assertSame(2, $response->json('data.total_transactions'));
        $this->assertSame(1, $response->json('data.reconciled'));
    }

    public function test_aging_report_returns_buckets(): void
    {
        $this->createTx(['amount' => 1000]);
        Transaction::where('account_tenant_id', $this->tenant->id)->update(['due_on' => now()->subDays(45)]);
        $response = $this->getJson('/rf/aging-report');
        $response->assertStatus(200);
        $this->assertArrayHasKey('31_60', $response->json('data'));
    }

    public function test_financial_summary(): void
    {
        $this->createTx(['direction' => 'money_in', 'amount' => 10000]);
        $response = $this->getJson('/rf/financial-summary');
        $response->assertStatus(200);
        $this->assertEquals(10000.0, (float) $response->json('data.total_income'));
    }
}
