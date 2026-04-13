<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create();
    }

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_dashboard_service_can_be_instantiated(): void
    {
        $service = new DashboardService;

        $this->assertInstanceOf(DashboardService::class, $service);
    }

    public function test_get_unit_statistics_returns_correct_format(): void
    {
        $service = new DashboardService;

        $stats = $service->getUnitStatistics($this->tenant->id);

        $this->assertArrayHasKey('vacant', $stats);
        $this->assertArrayHasKey('leased', $stats);
        $this->assertArrayHasKey('sold', $stats);
        $this->assertArrayHasKey('total', $stats);
    }

    public function test_get_requires_attention_returns_correct_format(): void
    {
        $service = new DashboardService;

        $attention = $service->getRequiresAttention($this->tenant->id);

        $this->assertArrayHasKey('requests_approval', $attention);
        $this->assertArrayHasKey('pending_complaints', $attention);
        $this->assertArrayHasKey('expiring_leases', $attention);
        $this->assertArrayHasKey('overdue_receipts', $attention);
    }

    public function test_get_lease_statistics_returns_correct_format(): void
    {
        $service = new DashboardService;

        $stats = $service->getLeaseStatistics($this->tenant->id);

        $this->assertArrayHasKey('active', $stats);
        $this->assertArrayHasKey('expiring_soon', $stats);
        $this->assertArrayHasKey('expired', $stats);
        $this->assertArrayHasKey('total', $stats);
    }

    public function test_get_service_request_statistics_returns_correct_format(): void
    {
        $service = new DashboardService;

        $stats = $service->getServiceRequestStatistics($this->tenant->id);

        $this->assertArrayHasKey('open', $stats);
        $this->assertArrayHasKey('in_progress', $stats);
        $this->assertArrayHasKey('pending_approval', $stats);
        $this->assertArrayHasKey('completed', $stats);
        $this->assertArrayHasKey('closed', $stats);
        $this->assertArrayHasKey('total', $stats);
    }

    public function test_get_marketplace_statistics_returns_correct_format(): void
    {
        $service = new DashboardService;

        $stats = $service->getMarketplaceStatistics($this->tenant->id);

        $this->assertArrayHasKey('active_listings', $stats);
        $this->assertArrayHasKey('total_listings', $stats);
        $this->assertArrayHasKey('scheduled_visits', $stats);
        $this->assertArrayHasKey('pending_offers', $stats);
        $this->assertArrayHasKey('completed_sales', $stats);
    }

    public function test_get_financial_overview_returns_correct_format(): void
    {
        $service = new DashboardService;

        $stats = $service->getFinancialOverview($this->tenant->id);

        $this->assertArrayHasKey('monthly_income', $stats);
        $this->assertArrayHasKey('monthly_expenses', $stats);
        $this->assertArrayHasKey('net_income', $stats);
        $this->assertArrayHasKey('pending_payments', $stats);
        $this->assertArrayHasKey('overdue_payments', $stats);
    }

    public function test_get_facility_statistics_returns_correct_format(): void
    {
        $service = new DashboardService;

        $stats = $service->getFacilityStatistics($this->tenant->id);

        $this->assertArrayHasKey('today_bookings', $stats);
        $this->assertArrayHasKey('upcoming_bookings', $stats);
        $this->assertArrayHasKey('pending_approval', $stats);
        $this->assertArrayHasKey('total_bookings', $stats);
    }

    public function test_get_visitor_statistics_returns_correct_format(): void
    {
        $service = new DashboardService;

        $stats = $service->getVisitorStatistics($this->tenant->id);

        $this->assertArrayHasKey('expected_today', $stats);
        $this->assertArrayHasKey('checked_in_today', $stats);
        $this->assertArrayHasKey('pending_approval', $stats);
    }

    public function test_get_dashboard_data_returns_all_sections(): void
    {
        $service = new DashboardService;

        $data = $service->getDashboardData($this->tenant->id);

        $this->assertArrayHasKey('units', $data);
        $this->assertArrayHasKey('requires_attention', $data);
        $this->assertArrayHasKey('leases', $data);
        $this->assertArrayHasKey('service_requests', $data);
        $this->assertArrayHasKey('marketplace', $data);
        $this->assertArrayHasKey('financials', $data);
        $this->assertArrayHasKey('facilities', $data);
        $this->assertArrayHasKey('visitors', $data);
    }

    public function test_unit_statistics_returns_zeros_for_empty_tenant(): void
    {
        $service = new DashboardService;

        $stats = $service->getUnitStatistics($this->tenant->id);

        $this->assertEquals(0, $stats['vacant']);
        $this->assertEquals(0, $stats['leased']);
        $this->assertEquals(0, $stats['sold']);
        $this->assertEquals(0, $stats['total']);
    }

    public function test_lease_statistics_returns_zeros_for_empty_tenant(): void
    {
        $service = new DashboardService;

        $stats = $service->getLeaseStatistics($this->tenant->id);

        $this->assertEquals(0, $stats['active']);
        $this->assertEquals(0, $stats['expiring_soon']);
        $this->assertEquals(0, $stats['expired']);
        $this->assertEquals(0, $stats['total']);
    }

    public function test_requires_attention_returns_zeros_for_empty_tenant(): void
    {
        $service = new DashboardService;

        $attention = $service->getRequiresAttention($this->tenant->id);

        $this->assertEquals(0, $attention['requests_approval']);
        $this->assertEquals(0, $attention['pending_complaints']);
        $this->assertEquals(0, $attention['expiring_leases']);
        $this->assertEquals(0, $attention['overdue_receipts']);
    }

    public function test_financial_overview_returns_zeros_for_empty_tenant(): void
    {
        $service = new DashboardService;

        $stats = $service->getFinancialOverview($this->tenant->id);

        $this->assertEquals(0, $stats['monthly_income']);
        $this->assertEquals(0, $stats['monthly_expenses']);
        $this->assertEquals(0, $stats['net_income']);
        $this->assertEquals(0, $stats['pending_payments']);
        $this->assertEquals(0, $stats['overdue_payments']);
    }

    public function test_marketplace_statistics_returns_zeros_for_empty_tenant(): void
    {
        $service = new DashboardService;

        $stats = $service->getMarketplaceStatistics($this->tenant->id);

        $this->assertEquals(0, $stats['active_listings']);
        $this->assertEquals(0, $stats['total_listings']);
        $this->assertEquals(0, $stats['scheduled_visits']);
        $this->assertEquals(0, $stats['pending_offers']);
        $this->assertEquals(0, $stats['completed_sales']);
    }

    public function test_facility_statistics_returns_zeros_for_empty_tenant(): void
    {
        $service = new DashboardService;

        $stats = $service->getFacilityStatistics($this->tenant->id);

        $this->assertEquals(0, $stats['today_bookings']);
        $this->assertEquals(0, $stats['upcoming_bookings']);
        $this->assertEquals(0, $stats['pending_approval']);
        $this->assertEquals(0, $stats['total_bookings']);
    }

    public function test_visitor_statistics_returns_zeros_for_empty_tenant(): void
    {
        $service = new DashboardService;

        $stats = $service->getVisitorStatistics($this->tenant->id);

        $this->assertEquals(0, $stats['expected_today']);
        $this->assertEquals(0, $stats['checked_in_today']);
        $this->assertEquals(0, $stats['pending_approval']);
    }

    public function test_dashboard_page_renders_with_statistics(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('dashboard')
            ->has('statistics')
            ->has('statistics.units')
            ->has('statistics.requires_attention')
            ->has('statistics.leases')
            ->has('statistics.service_requests')
            ->has('statistics.marketplace')
            ->has('statistics.financials')
            ->has('statistics.facilities')
            ->has('statistics.visitors')
        );
    }
}
