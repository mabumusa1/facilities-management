<?php

namespace Tests\Feature\AppSettings;

use App\Models\AccountMembership;
use App\Models\InvoiceSetting;
use App\Models\RequestCategory;
use App\Models\RequestSubcategory;
use App\Models\ServiceSetting;
use App\Models\SystemSetting;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SettingsShellControllerTest extends TestCase
{
    use RefreshDatabase;

    private int $tenantId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function authenticateUser(): User
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Test Account']);
        $this->tenantId = $tenant->id;
        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);
        $this->actingAs($user);

        return $user;
    }

    /**
     * @return array<string, string>
     */
    private function tabRoutes(): array
    {
        return [
            'invoice' => 'settings.invoice',
            'service-request' => 'settings.service-request',
            'visitor-request' => 'settings.visitor-request',
            'bank-details' => 'settings.bank-details',
            'visits-details' => 'settings.visits-details',
            'sales-details' => 'settings.sales-details',
        ];
    }

    public function test_guests_cannot_access_settings_shell_tabs(): void
    {
        foreach ($this->tabRoutes() as $routeName) {
            $response = $this->get(route($routeName));

            $response->assertRedirect(route('login'));
        }
    }

    public function test_authenticated_user_can_access_each_settings_shell_tab(): void
    {
        $this->authenticateUser();

        foreach ($this->tabRoutes() as $tabKey => $routeName) {
            $response = $this->get(route($routeName));

            $response
                ->assertOk()
                ->assertInertia(fn (Assert $page) => $page
                    ->component('app-settings/settings/Index')
                    ->where('activeTab', $tabKey)
                    ->has('tabs', 6)
                );
        }
    }

    public function test_invoice_tab_includes_expected_read_payload_shape(): void
    {
        $this->authenticateUser();

        InvoiceSetting::withoutGlobalScopes()
            ->where('account_tenant_id', $this->tenantId)
            ->first()
            ->update([
                'company_name' => 'Acme Corp',
                'address' => '123 Main St',
                'vat' => 15,
                'vat_number' => 'VAT123456',
                'cr_number' => 'CR789',
                'instructions' => 'Pay within 30 days',
                'notes' => 'Thank you for your business',
            ]);

        $response = $this->get(route('settings.invoice'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('app-settings/settings/Index')
                ->where('activeTab', 'invoice')
                ->has('invoiceSetting')
                ->where('invoiceSetting.company_name', 'Acme Corp')
                ->where('invoiceSetting.address', '123 Main St')
                ->where('invoiceSetting.vat', '15.00')
                ->where('invoiceSetting.vat_number', 'VAT123456')
                ->where('invoiceSetting.cr_number', 'CR789')
                ->where('invoiceSetting.instructions', 'Pay within 30 days')
                ->where('invoiceSetting.notes', 'Thank you for your business')
                ->missing('invoiceSetting.account_tenant_id')
            );
    }

    public function test_settings_shell_exposes_expected_tab_navigation_contract(): void
    {
        $this->authenticateUser();

        $response = $this->get(route('settings.invoice'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('app-settings/settings/Index')
                ->where('tabs.0.key', 'invoice')
                ->where('tabs.0.href', route('settings.invoice'))
                ->where('tabs.1.key', 'service-request')
                ->where('tabs.1.href', route('settings.service-request'))
                ->where('tabs.2.key', 'visitor-request')
                ->where('tabs.2.href', route('settings.visitor-request'))
                ->where('tabs.3.key', 'bank-details')
                ->where('tabs.3.href', route('settings.bank-details'))
                ->where('tabs.4.key', 'visits-details')
                ->where('tabs.4.href', route('settings.visits-details'))
                ->where('tabs.5.key', 'sales-details')
                ->where('tabs.5.href', route('settings.sales-details'))
            );
    }

    public function test_authenticated_user_can_store_invoice_settings_from_settings_shell(): void
    {
        $this->authenticateUser();

        $response = $this->post(route('settings.invoice.store'), [
            'company_name' => 'Acme Corp',
            'address' => '123 Main St',
            'vat' => 15,
            'vat_number' => 'VAT123456',
            'cr_number' => 'CR789',
            'instructions' => 'Pay within 30 days',
            'notes' => 'Thank you for your business',
        ]);

        $response->assertRedirect(route('settings.invoice'));
        $this->assertDatabaseHas('rf_invoice_settings', [
            'company_name' => 'Acme Corp',
            'address' => '123 Main St',
            'vat_number' => 'VAT123456',
            'account_tenant_id' => $this->tenantId,
        ]);
    }

    public function test_settings_shell_invoice_store_requires_company_name_address_and_vat(): void
    {
        $this->authenticateUser();

        $response = $this->post(route('settings.invoice.store'), []);

        $response->assertSessionHasErrors([
            'company_name',
            'address',
            'vat',
        ]);
    }

    public function test_service_request_tab_exposes_types_categories_and_subcategories_payload(): void
    {
        $this->authenticateUser();

        $category = RequestCategory::factory()->create([
            'name_en' => 'Maintenance',
            'name_ar' => 'صيانة',
            'status' => true,
            'has_sub_categories' => true,
        ]);

        RequestSubcategory::factory()->create([
            'category_id' => $category->id,
            'name_en' => 'Plumbing',
            'name_ar' => 'سباكة',
            'status' => true,
        ]);

        $response = $this->get(route('settings.service-request'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('app-settings/settings/Index')
                ->where('activeTab', 'service-request')
                ->has('serviceRequestSettings.types', 2)
                ->where('serviceRequestSettings.types.0.key', 'home-service')
                ->where('serviceRequestSettings.types.1.key', 'neighbourhood-service')
                ->has('serviceRequestSettings.categories', 1)
                ->where('serviceRequestSettings.categories.0.id', $category->id)
                ->where('serviceRequestSettings.categories.0.name_en', 'Maintenance')
                ->has('serviceRequestSettings.categories.0.subcategories', 1)
                ->where('serviceRequestSettings.categories.0.subcategories.0.name_en', 'Plumbing')
            );
    }

    public function test_service_request_details_route_binds_params_and_returns_details_payload(): void
    {
        $this->authenticateUser();

        $category = RequestCategory::factory()->create([
            'name_en' => 'Maintenance',
            'name_ar' => 'صيانة',
            'status' => true,
            'has_sub_categories' => true,
        ]);

        RequestSubcategory::factory()->create([
            'category_id' => $category->id,
            'name_en' => 'Electrical',
            'name_ar' => 'كهرباء',
            'status' => true,
        ]);

        ServiceSetting::factory()->create([
            'category_id' => $category->id,
            'account_tenant_id' => $this->tenantId,
            'permissions' => [
                'attachments_required' => true,
            ],
        ]);

        $response = $this->get(route('settings.service-request.details', [
            'type' => 'home-service',
            'catCode' => 'maintenance',
            'catId' => $category->id,
        ]));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('app-settings/settings/ServiceRequestDetails')
                ->where('requestType', 'home-service')
                ->where('categoryCode', 'maintenance')
                ->where('category.id', $category->id)
                ->where('category.name_en', 'Maintenance')
                ->has('category.subcategories', 1)
                ->has('serviceSetting')
                ->where('serviceSetting.permissions.attachments_required', true)
            );
    }

    public function test_service_request_details_route_rejects_unsupported_type(): void
    {
        $this->authenticateUser();

        $category = RequestCategory::factory()->create();

        $response = $this->get(route('settings.service-request.details', [
            'type' => 'unsupported-type',
            'catCode' => 'any',
            'catId' => $category->id,
        ]));

        $response->assertNotFound();
    }

    public function test_authenticated_user_can_store_visitor_request_settings_from_settings_shell(): void
    {
        $this->authenticateUser();

        $response = $this->post(route('settings.visitor-request.store'), [
            'enabled' => true,
            'require_pre_approval' => true,
            'max_visitors_per_request' => 4,
            'allowed_visit_duration_minutes' => 90,
            'notes' => 'Visitors must show QR code.',
        ]);

        $response->assertRedirect(route('settings.visitor-request'));

        $setting = SystemSetting::query()->where('key', 'visitor-request')->first();

        $this->assertNotNull($setting);
        $this->assertSame($this->tenantId, $setting?->account_tenant_id);
        $this->assertSame(true, data_get($setting?->payload, 'enabled'));
        $this->assertSame(4, data_get($setting?->payload, 'max_visitors_per_request'));
    }

    public function test_bank_details_store_supports_json_contract_and_marketplace_read_endpoint(): void
    {
        $this->authenticateUser();

        $save = $this->postJson(route('settings.bank-details.store'), [
            'beneficiary_name' => 'Acme Property LLC',
            'bank_name' => 'National Bank',
            'account_number' => '12345678901234',
            'iban' => 'SA0380000000608010167519',
        ]);

        $save
            ->assertOk()
            ->assertJsonPath('data.bank_name', 'National Bank')
            ->assertJsonPath('message', 'Bank settings saved successfully.');

        $read = $this->getJson(route('marketplace-admin.settings.banks'));

        $read
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.beneficiary_name', 'Acme Property LLC')
            ->assertJsonPath('data.account_number', '12345678901234');
    }

    public function test_bank_details_store_validates_numeric_account_number_and_length(): void
    {
        $this->authenticateUser();

        $response = $this->post(route('settings.bank-details.store'), [
            'beneficiary_name' => 'Acme Property LLC',
            'bank_name' => 'National Bank',
            'account_number' => 'abc123',
            'iban' => 'SA0380000000608010167519',
        ]);

        $response->assertSessionHasErrors([
            'account_number',
        ]);
    }

    public function test_visits_details_store_and_read_contract_are_available(): void
    {
        $this->authenticateUser();

        $save = $this->postJson(route('settings.visits-details.store'), [
            'is_all_day' => false,
            'days' => ['sun', 'mon', 'tue'],
            'start_time' => '09:00',
            'end_time' => '18:00',
            'max_daily_visits' => 15,
        ]);

        $save
            ->assertOk()
            ->assertJsonPath('data.days.0', 'sun')
            ->assertJsonPath('message', 'Visits settings saved successfully.');

        $read = $this->getJson(route('marketplace-admin.settings.visits'));

        $read
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.max_daily_visits', 15)
            ->assertJsonPath('data.start_time', '09:00');
    }

    public function test_sales_details_store_and_marketplace_sales_read_contract_work(): void
    {
        $this->authenticateUser();

        $save = $this->postJson(route('settings.sales-details.store'), [
            'deposit_time_limit_days' => 12,
            'cash_contract_signing_days' => 20,
            'bank_contract_signing_days' => 35,
        ]);

        $save
            ->assertOk()
            ->assertJsonPath('data.deposit_time_limit_days', 12)
            ->assertJsonPath('message', 'Sales settings saved successfully.');

        $read = $this->getJson(route('marketplace-admin.settings.sales'));

        $read
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.cash_contract_signing_days', 20)
            ->assertJsonPath('data.bank_contract_signing_days', 35);
    }
}
