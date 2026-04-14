<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    private const INERTIA_COMPONENT = 'transactions/index';

    protected User $user;

    protected Tenant $tenant;

    protected Contact $assignee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        Status::factory()->create([
            'id' => 1,
            'name' => 'Paid',
            'domain' => 'transaction',
            'slug' => 'transaction_paid',
        ]);

        Status::factory()->create([
            'id' => 2,
            'name' => 'Due',
            'domain' => 'transaction',
            'slug' => 'transaction_due',
        ]);

        $this->assignee = Contact::factory()->forTenant($this->tenant)->create();
    }

    public function test_index_displays_transactions_page(): void
    {
        $this->createTransaction(['amount' => 2000.00, 'left' => 2000.00, 'is_paid' => false]);
        $this->createTransaction(['amount' => -450.00, 'left' => -450.00, 'is_paid' => false]);

        $response = $this->actingAs($this->user)->get(route('transactions.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component(self::INERTIA_COMPONENT)
            ->has('transactions.data', 2)
            ->has('tabs', 3)
            ->where('filters.filter_type', 'all')
            ->where('currency', 'SAR')
        );
    }

    public function test_index_scopes_results_to_authenticated_users_tenant(): void
    {
        $tenantTransaction = $this->createTransaction([
            'lease_number' => 'TENANT-ONLY',
            'amount' => 900.00,
        ]);

        $otherTenant = Tenant::factory()->create();
        $otherAssignee = Contact::factory()->forTenant($otherTenant)->create();
        Transaction::factory()->create([
            'tenant_id' => $otherTenant->id,
            'assignee_id' => $otherAssignee->id,
            'lease_number' => 'OTHER-TENANT',
            'amount' => 700.00,
            'left' => 700.00,
            'is_paid' => false,
        ]);

        $response = $this->actingAs($this->user)->get(route('transactions.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component(self::INERTIA_COMPONENT)
            ->has('transactions.data', 1)
            ->where('transactions.data.0.id', $tenantTransaction->id)
        );
    }

    public function test_index_supports_money_in_and_money_out_filters(): void
    {
        $this->createTransaction(['amount' => 1700.00, 'left' => 1700.00, 'is_paid' => false]);
        $this->createTransaction(['amount' => -320.00, 'left' => -320.00, 'is_paid' => false]);

        $moneyInResponse = $this->actingAs($this->user)->get(route('transactions.index', [
            'filter_type' => 'money_in',
        ]));

        $moneyInResponse->assertOk();
        $moneyInResponse->assertInertia(fn (Assert $page) => $page
            ->component(self::INERTIA_COMPONENT)
            ->where('filters.filter_type', 'money_in')
            ->has('transactions.data', 1)
            ->where('transactions.data.0.direction', 'money_in')
        );

        $moneyOutResponse = $this->actingAs($this->user)->get(route('transactions.money-out'));

        $moneyOutResponse->assertOk();
        $moneyOutResponse->assertInertia(fn (Assert $page) => $page
            ->component(self::INERTIA_COMPONENT)
            ->where('filters.filter_type', 'money_out')
            ->has('transactions.data', 1)
            ->where('transactions.data.0.direction', 'money_out')
        );
    }

    public function test_index_applies_search_filter(): void
    {
        $this->createTransaction([
            'lease_number' => 'TX-RL-1000',
            'details' => 'Water utility invoice',
        ]);

        $this->createTransaction([
            'lease_number' => 'TX-RL-2000',
            'details' => 'Parking fee',
        ]);

        $response = $this->actingAs($this->user)->get(route('transactions.index', [
            'search' => 'TX-RL-1000',
        ]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component(self::INERTIA_COMPONENT)
            ->where('filters.search', 'TX-RL-1000')
            ->has('transactions.data', 1)
            ->where('transactions.data.0.lease_number', 'TX-RL-1000')
        );
    }

    public function test_list_route_returns_json_payload(): void
    {
        $this->createTransaction([
            'amount' => 600.00,
            'left' => 600.00,
            'is_paid' => false,
        ]);

        $response = $this->actingAs($this->user)->getJson(route('transactions.list', [
            'filter_type' => 'money_in',
            'per_page' => 5,
        ]));

        $response->assertOk();
        $response->assertJsonPath('filters.filter_type', 'money_in');
        $response->assertJsonCount(1, 'transactions.data');
        $response->assertJsonStructure([
            'transactions' => [
                'current_page',
                'data',
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total',
            ],
            'filters' => ['filter_type', 'from', 'to', 'search'],
        ]);
    }

    public function test_list_route_rejects_invalid_filter_type(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('transactions.list', [
            'filter_type' => 'unsupported',
        ]));

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('filter_type');
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    protected function createTransaction(array $overrides = []): Transaction
    {
        return Transaction::factory()->create(array_merge([
            'tenant_id' => $this->tenant->id,
            'assignee_id' => $this->assignee->id,
            'status_id' => 2,
            'type_id' => 2,
            'category_id' => 1,
            'amount' => 1500.00,
            'paid' => 0,
            'left' => 1500.00,
            'is_paid' => false,
        ], $overrides));
    }
}
