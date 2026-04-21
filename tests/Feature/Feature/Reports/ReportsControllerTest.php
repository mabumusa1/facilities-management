<?php

namespace Tests\Feature\Feature\Reports;

use App\Models\AccountMembership;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ReportsControllerTest extends TestCase
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
    private function authenticateUserWithoutMembership(): array
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'No Membership Account']);

        $this->actingAs($user);

        return [$user, $tenant];
    }

    private function authenticateUserWithMembership(): Tenant
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Reports Account']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($user);

        return $tenant;
    }

    /**
     * Reports module should deny users without tenant memberships.
     */
    public function test_reports_routes_require_account_membership(): void
    {
        [, $tenant] = $this->authenticateUserWithoutMembership();

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('dashboard.reports'));

        $response->assertForbidden();
    }

    public function test_reports_pages_render_for_membership_user(): void
    {
        $tenant = $this->authenticateUserWithMembership();

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('dashboard.reports'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('reports/Index')
                ->where('reportMode', 'reports')
                ->where('title', 'Reports')
            );
    }

    public function test_reports_json_endpoints_return_expected_payloads(): void
    {
        $tenant = $this->authenticateUserWithMembership();

        $load = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson(route('report.load'), ['reportId' => 'collections']);

        $load
            ->assertOk()
            ->assertJsonPath('data.reportId', 'collections')
            ->assertJsonPath('message', 'Report loaded.');

        $settings = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('report.settings'));

        $settings
            ->assertOk()
            ->assertJsonPath('data.allow_export', true)
            ->assertJsonPath('data.default_theme', 'light');

        $theme = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson(route('report.theme'), ['theme' => 'dark']);

        $theme
            ->assertOk()
            ->assertJsonPath('data.theme', 'dark')
            ->assertJsonPath('message', 'Theme applied.');

        $zoom = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson(route('report.zoom'), ['value' => 1.5]);

        $zoom
            ->assertOk()
            ->assertJsonPath('data.value', 1.5)
            ->assertJsonPath('message', 'Zoom updated.');
    }
}
