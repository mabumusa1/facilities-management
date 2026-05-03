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
use Illuminate\Support\Carbon;
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

    // ── AC1: empty state + authentication + row fields ──────────────────────────

    /**
     * AC1 — empty state: pipeline view renders with zero leases.
     */
    public function test_pipeline_index_returns_empty_groups_when_no_leases_exist(): void
    {
        $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leasing.pipeline.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('leasing/pipeline/Index')
                ->where('totalCount', 0)
                ->where('groups.expiring_soon', [])
                ->where('groups.active', [])
                ->where('groups.expired', [])
                ->where('groups.terminated', [])
                ->where('groups.pending', [])
            );
    }

    /**
     * AC1 — authentication failure: unauthenticated request must be redirected (401/302).
     */
    public function test_pipeline_index_redirects_unauthenticated_user(): void
    {
        auth()->logout();

        $this->withoutVite()
            ->get(route('leasing.pipeline.index'))
            ->assertRedirect();
    }

    /**
     * AC1 — row fields: every row contains required display fields.
     */
    public function test_pipeline_index_row_contains_required_fields(): void
    {
        Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => 31,
            'end_date' => now()->addDays(60)->toDateString(),
        ]);

        $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leasing.pipeline.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('leasing/pipeline/Index')
                ->where('groups.active', function ($leases) {
                    $row = $leases[0];

                    return array_key_exists('id', $row)
                        && array_key_exists('tenant_name', $row)
                        && array_key_exists('start_date', $row)
                        && array_key_exists('end_date', $row)
                        && array_key_exists('rental_total_amount', $row)
                        && array_key_exists('days_until_expiry', $row)
                        && array_key_exists('status', $row);
                })
            );
    }

    /**
     * AC1 — boundary: lease expiring exactly today has days_until_expiry = 0.
     */
    public function test_pipeline_lease_expiring_today_has_zero_days_until_expiry(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-03'));

        $lease = Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => 31,
            'end_date' => '2026-05-03',
        ]);

        $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leasing.pipeline.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('groups.expiring_soon', function ($leases) use ($lease) {
                    $row = collect($leases)->firstWhere('id', $lease->id);

                    return $row !== null && $row['days_until_expiry'] === 0;
                })
            );

        Carbon::setTestNow();
    }

    // ── AC2: expiry window filtering ─────────────────────────────────────────────

    /**
     * AC2 — 60-day window: leases within 60 days show as expiring_soon.
     */
    public function test_pipeline_expiry_filter_60_day_window(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-03'));

        // Within 60-day window
        $within60 = Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => 31,
            'end_date' => Carbon::parse('2026-05-03')->addDays(45)->toDateString(),
        ]);

        // Outside 60-day window (61 days away)
        $outside60 = Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => 31,
            'end_date' => Carbon::parse('2026-05-03')->addDays(61)->toDateString(),
        ]);

        $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leasing.pipeline.index', ['expiry_window' => 60]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('groups.expiring_soon', function ($leases) use ($within60, $outside60) {
                    $ids = collect($leases)->pluck('id');

                    return $ids->contains($within60->id) && ! $ids->contains($outside60->id);
                })
                ->where('groups.active', function ($leases) use ($outside60) {
                    return collect($leases)->pluck('id')->contains($outside60->id);
                })
            );

        Carbon::setTestNow();
    }

    /**
     * AC2 — 90-day window: leases within 90 days show as expiring_soon.
     */
    public function test_pipeline_expiry_filter_90_day_window(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-03'));

        $within90 = Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => 31,
            'end_date' => Carbon::parse('2026-05-03')->addDays(80)->toDateString(),
        ]);

        $outside90 = Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => 31,
            'end_date' => Carbon::parse('2026-05-03')->addDays(120)->toDateString(),
        ]);

        $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leasing.pipeline.index', ['expiry_window' => 90]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('groups.expiring_soon', function ($leases) use ($within90, $outside90) {
                    $ids = collect($leases)->pluck('id');

                    return $ids->contains($within90->id) && ! $ids->contains($outside90->id);
                })
            );

        Carbon::setTestNow();
    }

    /**
     * AC2 — invalid window: an out-of-range expiry_window value falls back to the 30-day default.
     */
    public function test_pipeline_invalid_expiry_window_falls_back_to_default(): void
    {
        $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leasing.pipeline.index', ['expiry_window' => 999]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('filters.expiry_window', 30)
            );
    }

    /**
     * AC2 — boundary: lease expiring exactly on the 30-day cutoff falls into expiring_soon.
     */
    public function test_pipeline_lease_on_exact_expiry_window_boundary_is_expiring_soon(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-03'));

        $boundary = Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => 31,
            'end_date' => Carbon::parse('2026-05-03')->addDays(30)->toDateString(),
        ]);

        $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leasing.pipeline.index', ['expiry_window' => 30]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('groups.expiring_soon', function ($leases) use ($boundary) {
                    return collect($leases)->pluck('id')->contains($boundary->id);
                })
            );

        Carbon::setTestNow();
    }

    // ── AC3: alert settings edge cases ──────────────────────────────────────────

    /**
     * AC3 — empty thresholds array: saving an empty array clears all thresholds.
     */
    public function test_alert_settings_update_accepts_empty_thresholds_array(): void
    {
        // Pre-seed some thresholds
        AppSetting::updateOrCreate(
            ['account_tenant_id' => $this->tenant->id],
            ['lease_alert_thresholds' => [['days' => 30, 'in_app' => true, 'email' => true]]]
        );

        $this->withSession($this->withTenant())
            ->post(route('leasing.settings.alerts.update'), ['thresholds' => []])
            ->assertRedirect(route('leasing.settings.alerts'));

        $setting = AppSetting::first();
        $this->assertNotNull($setting);
        $this->assertCount(0, $setting->lease_alert_thresholds);
    }

    /**
     * AC3 — missing field: omitting `thresholds` key entirely must return validation error.
     */
    public function test_alert_settings_update_requires_thresholds_field(): void
    {
        $this->withSession($this->withTenant())
            ->post(route('leasing.settings.alerts.update'), [])
            ->assertSessionHasErrors(['thresholds']);
    }

    /**
     * AC3 — validation: days must be between 1 and 365.
     */
    public function test_alert_settings_update_rejects_days_above_365(): void
    {
        $this->withSession($this->withTenant())
            ->post(route('leasing.settings.alerts.update'), [
                'thresholds' => [
                    ['days' => 366, 'in_app' => true, 'email' => false],
                ],
            ])
            ->assertSessionHasErrors(['thresholds.0.days']);
    }

    /**
     * AC3 — authorization: unauthorized user cannot update alert settings.
     */
    public function test_alert_settings_update_returns_403_for_unauthorised_user(): void
    {
        $unauthorisedUser = User::factory()->create();
        AccountMembership::create([
            'user_id' => $unauthorisedUser->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_members',
        ]);

        $this->actingAs($unauthorisedUser)
            ->withSession($this->withTenant())
            ->post(route('leasing.settings.alerts.update'), [
                'thresholds' => [['days' => 30, 'in_app' => true, 'email' => true]],
            ])
            ->assertForbidden();
    }

    /**
     * AC3 — default thresholds: show page returns the three default thresholds
     *        when no AppSetting record exists for this tenant.
     */
    public function test_alert_settings_show_returns_defaults_when_no_setting_exists(): void
    {
        // Ensure no AppSetting exists for this tenant
        AppSetting::where('account_tenant_id', $this->tenant->id)->delete();

        $this->withSession($this->withTenant())
            ->withoutVite()
            ->get(route('leasing.settings.alerts'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('thresholds', fn ($thresholds) => count($thresholds) === 3)
                ->where('defaultThresholds', fn ($defaults) => count($defaults) === 3)
            );
    }

    // ── AC4: export edge cases ────────────────────────────────────────────────────

    /**
     * AC4 — unauthenticated export must redirect.
     */
    public function test_pipeline_export_redirects_unauthenticated_user(): void
    {
        auth()->logout();

        $this->get(route('leasing.pipeline.export'))
            ->assertRedirect();
    }

    /**
     * AC4 — export respects status_id filter: only leases with matching status are included.
     */
    public function test_pipeline_export_preview_respects_status_filter(): void
    {
        Lease::factory()->count(2)->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => 31,
            'end_date' => now()->addDays(90)->toDateString(),
        ]);

        Lease::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => 32,
            'end_date' => now()->subDays(5)->toDateString(),
        ]);

        $response = $this->withSession($this->withTenant())
            ->getJson(route('leasing.pipeline.export-preview', ['status_id' => 31]));

        $response->assertOk()
            ->assertJsonPath('count', 2);
    }

    /**
     * AC4 — zero-result export: export-preview returns count=0 when no leases match filter.
     */
    public function test_pipeline_export_preview_returns_zero_when_no_leases_match(): void
    {
        // Create active leases but filter by a status with no records
        Lease::factory()->count(3)->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => 31,
            'end_date' => now()->addDays(90)->toDateString(),
        ]);

        $response = $this->withSession($this->withTenant())
            ->getJson(route('leasing.pipeline.export-preview', ['status_id' => 76]));

        $response->assertOk()
            ->assertJsonPath('count', 0);
    }

    /**
     * AC4 — export columns: the preview always returns the canonical 11 columns.
     */
    public function test_pipeline_export_preview_returns_canonical_columns(): void
    {
        $response = $this->withSession($this->withTenant())
            ->getJson(route('leasing.pipeline.export-preview'));

        $response->assertOk();
        $columns = $response->json('columns');
        $this->assertCount(11, $columns);
        $this->assertContains('lease_id', $columns);
        $this->assertContains('tenant_name', $columns);
        $this->assertContains('status', $columns);
    }

    /**
     * AC4 — export with zero matching records still produces a downloadable file.
     */
    public function test_pipeline_export_with_no_matching_leases_still_downloads(): void
    {
        // No leases exist; the export should still be a valid file download
        $response = $this->withSession($this->withTenant())
            ->get(route('leasing.pipeline.export'));

        $response->assertOk();
        $contentType = $response->headers->get('Content-Type') ?? '';
        $this->assertMatchesRegularExpression('/spreadsheetml|application\/vnd\.ms-excel|octet-stream/', $contentType);
    }

    // ── AC5: tenant isolation ─────────────────────────────────────────────────────

    /**
     * AC5 — second tenant: leases from tenant B are not visible to tenant A's session.
     */
    public function test_pipeline_tenant_b_leases_are_hidden_from_tenant_a_session(): void
    {
        $tenantA = $this->tenant;
        $tenantB = Tenant::create(['name' => 'Tenant B']);

        // Three leases for tenant A
        Lease::factory()->count(3)->create([
            'account_tenant_id' => $tenantA->id,
            'status_id' => 31,
            'end_date' => now()->addDays(90)->toDateString(),
        ]);

        // Two leases for tenant B (should be invisible)
        Lease::factory()->count(2)->create([
            'account_tenant_id' => $tenantB->id,
            'status_id' => 31,
            'end_date' => now()->addDays(90)->toDateString(),
        ]);

        $this->withSession(['tenant_id' => $tenantA->id])
            ->withoutVite()
            ->get(route('leasing.pipeline.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('totalCount', 3)
            );
    }

    /**
     * AC5 — cross-tenant export preview: only counts leases for the session's tenant.
     */
    public function test_pipeline_export_preview_scopes_count_to_current_tenant(): void
    {
        $tenantB = Tenant::create(['name' => 'Tenant B Export']);

        // 2 leases for our tenant
        Lease::factory()->count(2)->create([
            'account_tenant_id' => $this->tenant->id,
            'status_id' => 31,
            'end_date' => now()->addDays(90)->toDateString(),
        ]);

        // 5 leases for tenant B — must not inflate the count
        Lease::factory()->count(5)->create([
            'account_tenant_id' => $tenantB->id,
            'status_id' => 31,
            'end_date' => now()->addDays(90)->toDateString(),
        ]);

        $response = $this->withSession($this->withTenant())
            ->getJson(route('leasing.pipeline.export-preview'));

        $response->assertOk()
            ->assertJsonPath('count', 2);
    }
}
