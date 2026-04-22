<?php

namespace Tests\Feature\Feature\Contracts;

use App\Models\AccountMembership;
use App\Models\InvoiceSetting;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\Unit;
use App\Models\UnitCategory;
use App\Models\UnitType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AgentKBatchEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateUser(): Tenant
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Agent K Contract Tenant']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($user);

        return $tenant;
    }

    public function test_assigned_route_names_exist(): void
    {
        $expectedRoutes = [
            'invoice-settings.show',
            'invoice-settings.store',
            'invoice-settings.update',
            'reports.expenses',
            'reports.income',
            'reports.performance.units',
        ];

        foreach ($expectedRoutes as $routeName) {
            $this->assertTrue(Route::has($routeName), "Route [{$routeName}] must exist.");
        }
    }

    public function test_invoice_settings_endpoints_support_get_post_put_and_validation_contract(): void
    {
        $tenant = $this->authenticateUser();

        $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/invoice-settings', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['company_name', 'address', 'vat']);

        $storeResponse = $this->withSession(['tenant_id' => $tenant->id])
            ->postJson('/invoice-settings', [
                'company_name' => 'Agent K Company',
                'address' => 'Riyadh',
                'vat' => 15,
                'instructions' => 'Pay before due date.',
                'notes' => 'Thanks.',
                'vat_number' => 'VAT-123',
                'cr_number' => 'CR-456',
            ]);

        $storeResponse
            ->assertOk()
            ->assertJsonPath('data.company_name', 'Agent K Company')
            ->assertJsonPath('data.address', 'Riyadh')
            ->assertJsonPath('data.vat', '15.00')
            ->assertJsonPath('message', 'Invoice settings updated.')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'company_name',
                    'logo',
                    'address',
                    'vat',
                    'instructions',
                    'notes',
                    'vat_number',
                    'cr_number',
                ],
                'message',
            ]);

        $showResponse = $this->withSession(['tenant_id' => $tenant->id])
            ->getJson('/invoice-settings');

        $showResponse
            ->assertOk()
            ->assertJsonPath('data.company_name', 'Agent K Company')
            ->assertJsonPath('data.vat', '15.00')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'company_name',
                    'logo',
                    'address',
                    'vat',
                    'instructions',
                    'notes',
                    'vat_number',
                    'cr_number',
                ],
            ]);

        $updateResponse = $this->withSession(['tenant_id' => $tenant->id])
            ->putJson('/invoice-settings', [
                'company_name' => 'Agent K Company Updated',
                'address' => 'Jeddah',
                'vat' => 5,
                'notes' => 'Updated notes',
            ]);

        $updateResponse
            ->assertOk()
            ->assertJsonPath('data.company_name', 'Agent K Company Updated')
            ->assertJsonPath('data.address', 'Jeddah')
            ->assertJsonPath('data.vat', '5.00')
            ->assertJsonPath('data.notes', 'Updated notes');

        $this->assertDatabaseHas('rf_invoice_settings', [
            'id' => InvoiceSetting::query()->firstOrFail()->id,
            'company_name' => 'Agent K Company Updated',
            'address' => 'Jeddah',
            'account_tenant_id' => $tenant->id,
        ]);
    }

    public function test_reports_endpoints_return_expected_contract_like_payloads(): void
    {
        $tenant = $this->authenticateUser();

        $unitCategory = UnitCategory::factory()->create();
        $unitType = UnitType::factory()->create([
            'category_id' => $unitCategory->id,
        ]);

        $vacantStatus = Status::factory()->create([
            'type' => 'unit',
            'name' => 'Vacant',
            'name_en' => 'Vacant',
        ]);

        $soldStatus = Status::factory()->create([
            'type' => 'unit',
            'name' => 'Sold',
            'name_en' => 'Sold',
        ]);

        $leasedStatus = Status::factory()->create([
            'type' => 'unit',
            'name' => 'Leased',
            'name_en' => 'Leased',
        ]);

        $soldAndLeasedStatus = Status::factory()->create([
            'type' => 'unit',
            'name' => 'Sold And Leased',
            'name_en' => 'Sold And Leased',
        ]);

        Unit::factory()->create([
            'account_tenant_id' => $tenant->id,
            'category_id' => $unitCategory->id,
            'type_id' => $unitType->id,
            'status_id' => $vacantStatus->id,
            'is_buy' => false,
        ]);

        Unit::factory()->create([
            'account_tenant_id' => $tenant->id,
            'category_id' => $unitCategory->id,
            'type_id' => $unitType->id,
            'status_id' => $soldStatus->id,
            'is_buy' => true,
        ]);

        Unit::factory()->create([
            'account_tenant_id' => $tenant->id,
            'category_id' => $unitCategory->id,
            'type_id' => $unitType->id,
            'status_id' => $leasedStatus->id,
            'is_buy' => false,
        ]);

        Unit::factory()->create([
            'account_tenant_id' => $tenant->id,
            'category_id' => $unitCategory->id,
            'type_id' => $unitType->id,
            'status_id' => $soldAndLeasedStatus->id,
            'is_buy' => true,
        ]);

        $transactionCategoryIncome = Setting::factory()->create([
            'type' => 'transaction_category',
            'name' => 'Income',
            'name_en' => 'Income',
            'name_ar' => 'Income',
        ]);

        $transactionCategoryExpense = Setting::factory()->create([
            'type' => 'transaction_category',
            'name' => 'Expense',
            'name_en' => 'Expense',
            'name_ar' => 'Expense',
        ]);

        $transactionType = Setting::factory()->create([
            'type' => 'transaction_type',
            'name' => 'Invoice',
            'name_en' => 'Invoice',
            'name_ar' => 'Invoice',
        ]);

        $transactionStatus = Status::factory()->create([
            'type' => 'invoice',
            'name' => 'Pending',
            'name_en' => 'Pending',
            'name_ar' => 'Pending',
        ]);

        Transaction::query()->create([
            'category_id' => $transactionCategoryIncome->id,
            'type_id' => $transactionType->id,
            'status_id' => $transactionStatus->id,
            'amount' => 1000,
            'due_on' => now()->addWeek()->toDateString(),
            'is_paid' => true,
            'account_tenant_id' => $tenant->id,
        ]);

        Transaction::query()->create([
            'category_id' => $transactionCategoryIncome->id,
            'type_id' => $transactionType->id,
            'status_id' => $transactionStatus->id,
            'amount' => 500,
            'due_on' => now()->addWeek()->toDateString(),
            'is_paid' => false,
            'account_tenant_id' => $tenant->id,
        ]);

        Transaction::query()->create([
            'category_id' => $transactionCategoryExpense->id,
            'type_id' => $transactionType->id,
            'status_id' => $transactionStatus->id,
            'amount' => 300,
            'due_on' => now()->addWeek()->toDateString(),
            'is_paid' => true,
            'account_tenant_id' => $tenant->id,
        ]);

        $performanceResponse = $this->withSession(['tenant_id' => $tenant->id])
            ->getJson('/reports/performance/units');

        $performanceResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.vacant', 1)
            ->assertJsonPath('data.sold', 1)
            ->assertJsonPath('data.leased', 1)
            ->assertJsonPath('data.soldAndLeased', 1)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'vacant',
                    'sold',
                    'leased',
                    'soldAndLeased',
                ],
                'message',
            ]);

        $incomeResponse = $this->withSession(['tenant_id' => $tenant->id])
            ->getJson('/reports/income');

        $incomeResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.count', 2)
            ->assertJsonPath('data.total', 1500)
            ->assertJsonPath('data.paid', 1000)
            ->assertJsonPath('data.unpaid', 500)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'count',
                    'total',
                    'paid',
                    'unpaid',
                ],
                'message',
            ]);

        $expensesResponse = $this->withSession(['tenant_id' => $tenant->id])
            ->getJson('/reports/expenses');

        $expensesResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.count', 1)
            ->assertJsonPath('data.total', 300)
            ->assertJsonPath('data.paid', 300)
            ->assertJsonPath('data.unpaid', 0)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'count',
                    'total',
                    'paid',
                    'unpaid',
                ],
                'message',
            ]);
    }
}
