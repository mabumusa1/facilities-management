<?php

namespace Tests\Feature\Feature\Contracts;

use App\Models\AccountMembership;
use App\Models\Community;
use App\Models\MarketplaceUnit;
use App\Models\Request as ServiceRequest;
use App\Models\RequestCategory;
use App\Models\RequestSubcategory;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SystemStabilizationQualityGateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    /**
     * @return array{0: User, 1: Tenant}
     */
    private function authenticateUser(): array
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Stabilization Account']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($user);

        return [$user, $tenant];
    }

    /**
     * @param  array<int, int>  $statuses
     */
    private function assertModuleCaptureRate(string $module, array $statuses, float $minimum = 0.80): void
    {
        $total = count($statuses);
        $successful = count(array_filter($statuses, fn (int $status): bool => $status < 400));

        $rate = $total > 0 ? $successful / $total : 0.0;

        $this->assertGreaterThanOrEqual(
            $minimum,
            $rate,
            sprintf(
                '%s capture success rate %.2f is below %.2f. Statuses: [%s]',
                $module,
                $rate,
                $minimum,
                implode(', ', $statuses),
            ),
        );
    }

    private function notify(User $user, string $text): void
    {
        $user->notify(new class($text) extends Notification
        {
            public function __construct(private string $text) {}

            public function via(object $notifiable): array
            {
                return ['database'];
            }

            public function toDatabase(object $notifiable): array
            {
                return ['text' => $this->text];
            }
        });
    }

    public function test_openapi_drift_check_for_critical_routes_and_payloads(): void
    {
        $criticalRoutes = [
            ['name' => 'dashboard.requires-attention', 'method' => 'GET'],
            ['name' => 'notifications.index', 'method' => 'GET'],
            ['name' => 'notifications.unread-count', 'method' => 'GET'],
            ['name' => 'rf.modules', 'method' => 'GET'],
            ['name' => 'rf.statuses', 'method' => 'GET'],
            ['name' => 'rf.common-lists', 'method' => 'GET'],
            ['name' => 'rf.leads', 'method' => 'GET'],
            ['name' => 'rf.files.store', 'method' => 'POST'],
            ['name' => 'rf.excel-sheets.store', 'method' => 'POST'],
            ['name' => 'rf.excel-sheets.land', 'method' => 'POST'],
            ['name' => 'rf.excel-sheets.leads', 'method' => 'POST'],
            ['name' => 'marketplace-admin.settings.banks', 'method' => 'GET'],
            ['name' => 'marketplace-admin.settings.sales', 'method' => 'GET'],
            ['name' => 'marketplace-admin.settings.visits', 'method' => 'GET'],
            ['name' => 'marketplace-admin.units', 'method' => 'GET'],
            ['name' => 'marketplace-admin.visits', 'method' => 'GET'],
            ['name' => 'marketplace-admin.settings.banks.store', 'method' => 'POST'],
            ['name' => 'marketplace-admin.settings.sales.store', 'method' => 'POST'],
            ['name' => 'marketplace-admin.settings.visits.store', 'method' => 'POST'],
            ['name' => 'marketplace-admin.offers.store', 'method' => 'POST'],
            ['name' => 'marketplace-admin.offers.update', 'method' => 'PUT'],
            ['name' => 'marketplace-admin.offers.destroy', 'method' => 'DELETE'],
        ];

        foreach ($criticalRoutes as $route) {
            $this->assertTrue(Route::has($route['name']), sprintf('Missing route [%s].', $route['name']));

            $routeDefinition = app('router')->getRoutes()->getByName($route['name']);

            $this->assertNotNull($routeDefinition, sprintf('Missing route definition for [%s].', $route['name']));
            $this->assertContains(
                $route['method'],
                $routeDefinition->methods(),
                sprintf('Route [%s] does not allow method [%s].', $route['name'], $route['method']),
            );
        }
    }

    public function test_validation_schema_drift_against_docs_for_documents_and_marketplace(): void
    {
        [, $tenant] = $this->authenticateUser();

        $community = Community::factory()->create([
            'name' => 'Validation Community',
            'account_tenant_id' => $tenant->id,
        ]);

        $assertRequiredFields = function (string $contractName, array $expectedFields, callable $request): void {
            sort($expectedFields);

            $response = $request();

            $response->assertStatus(422);

            /** @var array<string, mixed> $errors */
            $errors = $response->json('errors') ?? [];
            $actualFields = array_keys($errors);
            sort($actualFields);

            $this->assertSame(
                $expectedFields,
                $actualFields,
                sprintf('Validation drift detected for [%s].', $contractName),
            );
        };

        $assertRequiredFields(
            'POST rf/files',
            ['image'],
            fn () => $this->withSession(['tenant_id' => $tenant->id])->postJson(route('rf.files.store'), []),
        );

        $assertRequiredFields(
            'POST rf/excel-sheets',
            ['file', 'rf_community_id'],
            fn () => $this->withSession(['tenant_id' => $tenant->id])->postJson(route('rf.excel-sheets.store'), []),
        );

        $assertRequiredFields(
            'POST marketplace/admin/settings/banks/store',
            ['beneficiary_name', 'bank_name', 'account_number', 'iban'],
            fn () => $this->withSession(['tenant_id' => $tenant->id])->postJson(route('marketplace-admin.settings.banks.store'), []),
        );

        $assertRequiredFields(
            'POST marketplace/admin/settings/sales/store',
            ['deposit_time_limit_days', 'cash_contract_signing_days', 'bank_contract_signing_days'],
            fn () => $this->withSession(['tenant_id' => $tenant->id])->postJson(route('marketplace-admin.settings.sales.store'), []),
        );

        $assertRequiredFields(
            'POST marketplace/admin/settings/visits/store',
            ['is_all_day', 'days'],
            fn () => $this->withSession(['tenant_id' => $tenant->id])->postJson(route('marketplace-admin.settings.visits.store'), []),
        );

        $assertRequiredFields(
            'POST marketplace/admin/communities/list/{community}',
            ['allow_cash_sale'],
            fn () => $this->withSession(['tenant_id' => $tenant->id])->postJson(route('marketplace-admin.communities.list', $community), []),
        );
    }

    public function test_smoke_flows_for_invoice_notifications_and_dashboard(): void
    {
        [$user, $tenant] = $this->authenticateUser();

        $createPayload = [
            'company_name' => 'Alpha Holdings',
            'address' => 'Riyadh',
            'vat' => 15,
            'vat_number' => '300123456700003',
            'cr_number' => '1010123456',
            'instructions' => 'Initial invoice instructions',
            'notes' => 'Initial invoice notes',
        ];

        $updatePayload = [
            ...$createPayload,
            'company_name' => 'Alpha Holdings Updated',
            'instructions' => 'Updated instructions',
        ];

        $this
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('settings.invoice.store'), $createPayload)
            ->assertRedirect(route('settings.invoice'));

        $this
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('settings.invoice'))
            ->assertOk()
            ->assertInertia(fn (Assert $inertia) => $inertia
                ->component('app-settings/settings/Index')
                ->where('invoiceSetting.company_name', 'Alpha Holdings')
                ->where('activeTab', 'invoice')
            );

        $this
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('settings.invoice.store'), $updatePayload)
            ->assertRedirect(route('settings.invoice'));

        $this
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('settings.invoice'))
            ->assertOk()
            ->assertInertia(fn (Assert $inertia) => $inertia
                ->component('app-settings/settings/Index')
                ->where('invoiceSetting.company_name', 'Alpha Holdings Updated')
            );

        $this->notify($user, 'Smoke notification A');
        $this->notify($user, 'Smoke notification B');

        $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('notifications.unread-count'))
            ->assertOk()
            ->assertJsonPath('data.count', 2);

        $notifications = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('notifications.index'))
            ->assertOk();

        $notificationId = (string) data_get($notifications->json(), 'data.0.id', '');

        $this->assertNotSame('', $notificationId);

        $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson(route('notifications.mark-as-read', $notificationId))
            ->assertOk();

        $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('notifications.unread-count'))
            ->assertOk()
            ->assertJsonPath('data.count', 1);

        $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson(route('notifications.mark-all-as-read'))
            ->assertOk();

        $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('notifications.unread-count'))
            ->assertOk()
            ->assertJsonPath('data.count', 0);

        $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('dashboard.requires-attention'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    ['key', 'title', 'count', 'href'],
                ],
            ]);
    }

    public function test_capture_success_rates_are_at_least_eighty_percent_for_key_modules(): void
    {
        [, $tenant] = $this->authenticateUser();

        $settingsStatuses = [];
        $settingsStatuses[] = $this->withSession(['tenant_id' => $tenant->id])->get(route('settings.invoice'))->status();
        $settingsStatuses[] = $this->withSession(['tenant_id' => $tenant->id])->post(route('settings.invoice.store'), [
            'company_name' => 'Capture Co',
            'address' => 'Riyadh',
            'vat' => 15,
            'vat_number' => '300123456700003',
            'cr_number' => '1010123456',
            'instructions' => 'Capture settings',
            'notes' => 'Capture notes',
        ])->status();
        $settingsStatuses[] = $this->withSession(['tenant_id' => $tenant->id])->post(route('settings.visitor-request.store'), [
            'enabled' => true,
            'require_pre_approval' => true,
            'max_visitors_per_request' => 3,
            'allowed_visit_duration_minutes' => 120,
            'notes' => 'Capture visitor settings',
        ])->status();
        $settingsStatuses[] = $this->withSession(['tenant_id' => $tenant->id])->postJson(route('marketplace-admin.settings.banks.store'), [
            'beneficiary_name' => 'Capture Beneficiary',
            'bank_name' => 'Capture Bank',
            'account_number' => '12345678901234',
            'iban' => 'SA0380000000608010167519',
        ])->status();
        $settingsStatuses[] = $this->withSession(['tenant_id' => $tenant->id])->postJson(route('marketplace-admin.settings.sales.store'), [
            'deposit_time_limit_days' => 7,
            'cash_contract_signing_days' => 14,
            'bank_contract_signing_days' => 30,
        ])->status();

        $marketplaceStatuses = [];

        $community = Community::factory()->create([
            'name' => 'Capture Community',
            'account_tenant_id' => $tenant->id,
        ]);

        $unit = Unit::factory()->create([
            'name' => 'Capture Unit',
            'rf_community_id' => $community->id,
            'account_tenant_id' => $tenant->id,
        ]);

        MarketplaceUnit::create([
            'unit_id' => $unit->id,
            'listing_type' => 'sale',
            'price' => 900000,
            'is_active' => true,
        ]);

        $marketplaceStatuses[] = $this->withSession(['tenant_id' => $tenant->id])->get(route('marketplace.overview'))->status();
        $marketplaceStatuses[] = $this->withSession(['tenant_id' => $tenant->id])->getJson(route('marketplace-admin.units'))->status();
        $marketplaceStatuses[] = $this->withSession(['tenant_id' => $tenant->id])->getJson(route('marketplace-admin.visits'))->status();
        $marketplaceStatuses[] = $this->withSession(['tenant_id' => $tenant->id])->postJson(route('marketplace-admin.communities.list', $community), [
            'allow_cash_sale' => true,
            'allow_bank_financing' => true,
        ])->status();

        $offerStore = $this->withSession(['tenant_id' => $tenant->id])->postJson(route('marketplace-admin.offers.store'), [
            'unit_id' => $unit->id,
            'title' => 'Capture Offer',
            'description' => 'Capture discount',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addWeek()->toDateString(),
        ]);

        $marketplaceStatuses[] = $offerStore->status();

        $offerId = (int) data_get($offerStore->json(), 'data.id', 0);

        if ($offerId > 0) {
            $marketplaceStatuses[] = $this->withSession(['tenant_id' => $tenant->id])->putJson(route('marketplace-admin.offers.update', $offerId), [
                'title' => 'Capture Offer Updated',
                'discount_value' => 12,
            ])->status();

            $marketplaceStatuses[] = $this->withSession(['tenant_id' => $tenant->id])->deleteJson(route('marketplace-admin.offers.destroy', $offerId))->status();
        }

        Storage::fake('public');

        $documentsStatuses = [];

        $uploadFile = $this->withSession(['tenant_id' => $tenant->id])->postJson(route('rf.files.store'), [
            'image' => UploadedFile::fake()->image('capture-proof.png'),
            'collection' => 'documents',
            'notes' => 'Capture document',
        ]);

        $documentsStatuses[] = $uploadFile->status();
        $mediaId = (int) data_get($uploadFile->json(), 'data.id', 0);

        $documentsStatuses[] = $this->withSession(['tenant_id' => $tenant->id])->postJson(route('rf.excel-sheets.store'), [
            'file' => UploadedFile::fake()->create('capture-units.xlsx', 10),
            'rf_community_id' => $community->id,
        ])->status();

        $documentsStatuses[] = $this->withSession(['tenant_id' => $tenant->id])->postJson(route('rf.excel-sheets.land'), [
            'rf_community_id' => $community->id,
            'file' => UploadedFile::fake()->create('capture-land.xlsx', 10),
        ])->status();

        $documentsStatuses[] = $this->withSession(['tenant_id' => $tenant->id])->postJson(route('rf.excel-sheets.leads'), [
            'file' => UploadedFile::fake()->create('capture-leads.xlsx', 10),
        ])->status();

        if ($mediaId > 0) {
            $documentsStatuses[] = $this->withSession(['tenant_id' => $tenant->id])->deleteJson(route('rf.files.destroy', $mediaId))->status();
        }

        $requestStatuses = [];

        Status::factory()->create(['type' => 'request']);
        $category = RequestCategory::factory()->create();
        $subcategory = RequestSubcategory::factory()->create(['category_id' => $category->id]);

        $requestStatuses[] = $this->withSession(['tenant_id' => $tenant->id])->get(route('requests.index'))->status();
        $requestStatuses[] = $this->withSession(['tenant_id' => $tenant->id])->get(route('requests.create'))->status();

        $requestStore = $this->withSession(['tenant_id' => $tenant->id])->post(route('requests.store'), [
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'unit_id' => $unit->id,
            'community_id' => $community->id,
            'title' => 'Capture request',
            'description' => 'Capture request description',
            'priority' => 'high',
        ]);

        $requestStatuses[] = $requestStore->status();

        $storedRequest = ServiceRequest::query()->latest()->first();

        if ($storedRequest instanceof ServiceRequest) {
            $requestStatuses[] = $this->withSession(['tenant_id' => $tenant->id])->get(route('requests.show', $storedRequest))->status();
            $requestStatuses[] = $this->withSession(['tenant_id' => $tenant->id])->put(route('requests.update', $storedRequest), [
                'priority' => 'medium',
                'description' => 'Capture request updated',
            ])->status();
        }

        $transactionStatuses = [];

        $invoiceStatus = Status::factory()->create(['type' => 'invoice']);

        $transactionStatuses[] = $this->withSession(['tenant_id' => $tenant->id])->get(route('transactions.index'))->status();
        $transactionStatuses[] = $this->withSession(['tenant_id' => $tenant->id])->get(route('transactions.create'))->status();

        $transactionStore = $this->withSession(['tenant_id' => $tenant->id])->post(route('transactions.store'), [
            'lease_id' => null,
            'unit_id' => $unit->id,
            'category_id' => 1,
            'type_id' => 1,
            'status_id' => $invoiceStatus->id,
            'assignee_id' => 1,
            'amount' => 3500,
            'due_date' => now()->addWeek()->toDateString(),
            'notes' => 'Capture transaction note',
        ]);

        $transactionStatuses[] = $transactionStore->status();

        $storedTransaction = Transaction::query()->latest()->first();

        if ($storedTransaction instanceof Transaction) {
            $transactionStatuses[] = $this->withSession(['tenant_id' => $tenant->id])->get(route('transactions.show', $storedTransaction))->status();
            $transactionStatuses[] = $this->withSession(['tenant_id' => $tenant->id])->put(route('transactions.update', $storedTransaction), [
                'amount' => 4000,
                'due_date' => now()->addDays(10)->toDateString(),
                'notes' => 'Capture transaction note updated',
            ])->status();
        }

        $this->assertModuleCaptureRate('settings', $settingsStatuses);
        $this->assertModuleCaptureRate('marketplace', $marketplaceStatuses);
        $this->assertModuleCaptureRate('documents', $documentsStatuses);
        $this->assertModuleCaptureRate('requests', $requestStatuses);
        $this->assertModuleCaptureRate('transactions', $transactionStatuses);
    }
}
