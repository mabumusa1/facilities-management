<?php

namespace Tests\Feature\Leasing;

use App\Http\Controllers\Leasing\LeasePipelineController;
use App\Models\AccountMembership;
use App\Models\AppSetting;
use App\Models\Lease;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LeasePipelineTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RbacSeeder::class);

        $this->tenant = Tenant::create(['name' => 'Pipeline Test Account']);

        $this->user = User::factory()->create();
        AccountMembership::create([
            'user_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);
        $this->user->assignRole('admins');

        $this->seedLeaseStatuses();

        $this->actingAs($this->user);
    }

    /** @return array{tenant_id: int} */
    private function withTenant(): array
    {
        return ['tenant_id' => $this->tenant->id];
    }

    private function seedLeaseStatuses(): void
    {
        $statuses = [
            ['id' => 30, 'type' => 'lease', 'name' => 'New Contract',        'name_en' => 'New Contract',        'name_ar' => 'عقد جديد',  'priority' => 1],
            ['id' => 31, 'type' => 'lease', 'name' => 'Active Contract',     'name_en' => 'Active Contract',     'name_ar' => 'عقد ساري',  'priority' => 1],
            ['id' => 32, 'type' => 'lease', 'name' => 'Expired Contract',    'name_en' => 'Expired Contract',    'name_ar' => 'عقد منتهي', 'priority' => 1],
            ['id' => 33, 'type' => 'lease', 'name' => 'Cancelled Contract',  'name_en' => 'Cancelled Contract',  'name_ar' => 'عقد ملغي',  'priority' => 1],
            ['id' => 34, 'type' => 'lease', 'name' => 'Closed Contract',     'name_en' => 'Closed Contract',     'name_ar' => 'عقد مغلق',  'priority' => 1],
            ['id' => 76, 'type' => 'lease', 'name' => 'Pending Application', 'name_en' => 'pending_application', 'name_ar' => 'طلب معلق',  'priority' => 0],
        ];

        foreach ($statuses as $status) {
            DB::table('rf_statuses')->insertOrIgnore($status);
        }
    }

    // ── Pipeline index ──────────────────────────────────────────────────────────

    public function test_pipeline_index_renders_for_authorised_user(): void
    {
        $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leasing.pipeline.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('leasing/pipeline/Index')
                ->has('groups')
                ->has('totalCount')
                ->has('filters')
            );
    }

    public function test_pipeline_index_groups_leases_by_status(): void
    {
        // Active lease with end_date far away
        Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => 31,
            'end_date' => now()->addDays(120)->toDateString(),
        ]);

        // Expiring soon lease (within 30-day default window)
        Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => 31,
            'end_date' => now()->addDays(10)->toDateString(),
        ]);

        // Expired lease
        Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => 32,
            'end_date' => now()->subDays(5)->toDateString(),
        ]);

        $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leasing.pipeline.index', ['expiry_window' => 30]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('leasing/pipeline/Index')
                ->where('groups.active', fn ($active) => count($active) >= 1)
                ->where('groups.expiring_soon', fn ($expiring) => count($expiring) >= 1)
                ->where('groups.expired', fn ($expired) => count($expired) >= 1)
            );
    }

    public function test_pipeline_index_filters_by_status_id(): void
    {
        Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => 31,
            'end_date' => now()->addDays(90)->toDateString(),
        ]);

        Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => 32,
            'end_date' => now()->subDays(5)->toDateString(),
        ]);

        // Filter to expired only
        $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leasing.pipeline.index', ['status_id' => 32]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('leasing/pipeline/Index')
                ->where('groups.active', fn ($a) => count($a) === 0)
                ->where('groups.expiring_soon', fn ($e) => count($e) === 0)
            );
    }

    public function test_pipeline_index_returns_403_for_unauthorised_user(): void
    {
        // A user with no roles/permissions cannot view the pipeline
        $unauthorisedUser = User::factory()->create();
        AccountMembership::create([
            'user_id' => $unauthorisedUser->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_members',
        ]);
        // Assign no role — user has no permissions at all

        $this->actingAs($unauthorisedUser)
            ->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leasing.pipeline.index'))
            ->assertForbidden();
    }

    public function test_pipeline_index_scopes_to_current_tenant(): void
    {
        $otherTenant = Tenant::create(['name' => 'Other Tenant']);

        // Lease belonging to another tenant should not appear
        Lease::factory()->create([
            'account_tenant_id' => $otherTenant->id,
            'status_id' => 31,
            'end_date' => now()->addDays(90)->toDateString(),
        ]);

        $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leasing.pipeline.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('leasing/pipeline/Index')
                ->where('totalCount', 0)
            );
    }

    public function test_pipeline_export_preview_returns_json(): void
    {
        Lease::factory()->count(3)->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => 31,
            'end_date' => now()->addDays(90)->toDateString(),
        ]);

        $response = $this->withSession($this->withTenant())
            ->getJson(route('leasing.pipeline.export-preview'));

        $response->assertOk()
            ->assertJsonStructure(['count', 'columns', 'expiry_window'])
            ->assertJsonPath('columns', LeasePipelineController::EXPORT_COLUMNS);
    }

    public function test_pipeline_export_returns_excel_download(): void
    {
        Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => 31,
            'end_date' => now()->addDays(90)->toDateString(),
        ]);

        $response = $this->withSession($this->withTenant())
            ->get(route('leasing.pipeline.export'));

        $response->assertOk();
        $contentType = $response->headers->get('Content-Type') ?? '';
        $this->assertMatchesRegularExpression('/spreadsheetml|application\/vnd\.ms-excel|octet-stream/', $contentType);
    }

    // ── Alert settings ──────────────────────────────────────────────────────────

    public function test_alert_settings_show_renders_with_defaults(): void
    {
        $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leasing.settings.alerts'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('leasing/settings/Alerts')
                ->has('thresholds')
                ->has('defaultThresholds')
            );
    }

    public function test_alert_settings_update_saves_thresholds(): void
    {
        $payload = [
            'thresholds' => [
                ['days' => 90, 'in_app' => true,  'email' => true],
                ['days' => 30, 'in_app' => true,  'email' => false],
                ['days' => 7,  'in_app' => false, 'email' => false],
            ],
        ];

        $this->withSession($this->withTenant())
            ->post(route('leasing.settings.alerts.update'), $payload)
            ->assertRedirect(route('leasing.settings.alerts'));

        $setting = AppSetting::first();
        $this->assertNotNull($setting);
        $this->assertCount(3, $setting->lease_alert_thresholds);
        $this->assertEquals(90, $setting->lease_alert_thresholds[0]['days']);
        $this->assertEquals(7, $setting->lease_alert_thresholds[2]['days']);
    }

    public function test_alert_settings_update_validates_thresholds(): void
    {
        $this->withSession($this->withTenant())
            ->post(route('leasing.settings.alerts.update'), [
                'thresholds' => [
                    ['days' => 0, 'in_app' => 'not-a-bool', 'email' => true],
                ],
            ])
            ->assertSessionHasErrors(['thresholds.0.days', 'thresholds.0.in_app']);
    }

    public function test_alert_settings_show_returns_saved_thresholds(): void
    {
        AppSetting::updateOrCreate(
            ['account_tenant_id' => $this->tenant->id],
            ['lease_alert_thresholds' => [['days' => 45, 'in_app' => true, 'email' => false]]]
        );

        $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leasing.settings.alerts'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('leasing/settings/Alerts')
                ->where('thresholds', fn ($thresholds) => $thresholds[0]['days'] === 45)
            );
    }

    public function test_pipeline_days_until_expiry_is_computed(): void
    {
        $expiringSoon = Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => 31,
            'end_date' => now()->addDays(6)->toDateString(),
        ]);

        $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leasing.pipeline.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('leasing/pipeline/Index')
                ->where('groups.expiring_soon', function ($leases) use ($expiringSoon) {
                    $row = collect($leases)->firstWhere('id', $expiringSoon->id);

                    return $row !== null && $row['days_until_expiry'] <= 6;
                })
            );
    }
}
