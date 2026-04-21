<?php

namespace Tests\Feature\Feature\Contracts;

use App\Models\AccountMembership;
use App\Models\SystemSetting;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ApiContractQualityGateTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateUser(): array
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Contract Account']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($user);

        return [$user, $tenant];
    }

    private function notify(User $user): void
    {
        $user->notify(new class extends Notification
        {
            public function via(object $notifiable): array
            {
                return ['database'];
            }

            public function toDatabase(object $notifiable): array
            {
                return ['text' => 'Contract gate notification'];
            }
        });
    }

    /**
     * Ensure critical route names used by frontend contracts remain registered.
     */
    public function test_critical_contract_routes_exist(): void
    {
        $expectedRoutes = [
            'settings.invoice',
            'settings.visitor-request',
            'settings.bank-details.store',
            'settings.forms.index',
            'marketplace.overview',
            'marketplace.visits.schedule',
            'visitor-access.history',
            'report.load',
            'report.settings',
            'notifications.unread-count',
            'rf.modules',
            'rf.statuses',
            'rf.common-lists',
            'rf.leads',
            'rf.countries',
            'rf.files.store',
            'rf.excel-sheets.store',
            'dashboard.requires-attention',
            'marketplace-admin.settings.banks',
            'marketplace-admin.settings.sales',
            'marketplace-admin.settings.visits',
        ];

        foreach ($expectedRoutes as $routeName) {
            $this->assertTrue(Route::has($routeName), "Route [{$routeName}] must exist for contracts.");
        }
    }

    public function test_dashboard_requires_attention_contract_shape(): void
    {
        [, $tenant] = $this->authenticateUser();

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('dashboard.requires-attention'));

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    ['key', 'title', 'count', 'href'],
                ],
            ]);

        $this->assertCount(5, $response->json('data'));
    }

    public function test_marketplace_settings_read_contract_returns_success_and_data(): void
    {
        [, $tenant] = $this->authenticateUser();

        SystemSetting::create([
            'key' => 'bank-details',
            'payload' => [
                'beneficiary_name' => 'Acme Ltd',
                'bank_name' => 'National Bank',
                'account_number' => '12345678901234',
                'iban' => 'SA0380000000608010167519',
            ],
            'account_tenant_id' => $tenant->id,
        ]);

        SystemSetting::create([
            'key' => 'sales-details',
            'payload' => [
                'deposit_time_limit_days' => 10,
                'cash_contract_signing_days' => 20,
                'bank_contract_signing_days' => 30,
            ],
            'account_tenant_id' => $tenant->id,
        ]);

        SystemSetting::create([
            'key' => 'visits-details',
            'payload' => [
                'is_all_day' => false,
                'days' => ['sun', 'mon'],
                'start_time' => '09:00',
                'end_time' => '17:00',
            ],
            'account_tenant_id' => $tenant->id,
        ]);

        $banks = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('marketplace-admin.settings.banks'));

        $banks
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.bank_name', 'National Bank');

        $sales = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('marketplace-admin.settings.sales'));

        $sales
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.cash_contract_signing_days', 20);

        $visits = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('marketplace-admin.settings.visits'));

        $visits
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.start_time', '09:00');
    }

    public function test_notifications_contract_exposes_unread_count(): void
    {
        [$user, $tenant] = $this->authenticateUser();
        $this->notify($user);

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('notifications.unread-count'));

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['count'],
            ])
            ->assertJsonPath('data.count', 1);
    }
}
