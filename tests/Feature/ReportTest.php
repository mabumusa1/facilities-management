<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ReportTest extends TestCase
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

    // ===== Authentication Tests =====

    public function test_guests_are_redirected_to_login_from_reports_index(): void
    {
        $response = $this->get(route('reports.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_to_login_from_lease_reports(): void
    {
        $response = $this->get(route('reports.leases'));
        $response->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_to_login_from_maintenance_reports(): void
    {
        $response = $this->get(route('reports.maintenance'));
        $response->assertRedirect(route('login'));
    }

    // ===== Page Rendering Tests =====

    public function test_authenticated_users_can_visit_reports_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.index'));
        $response->assertOk();
    }

    public function test_authenticated_users_can_visit_lease_reports(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.leases'));
        $response->assertOk();
    }

    public function test_authenticated_users_can_visit_maintenance_reports(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.maintenance'));
        $response->assertOk();
    }

    // ===== Service Tests =====

    public function test_report_service_can_be_instantiated(): void
    {
        $service = new ReportService;

        $this->assertInstanceOf(ReportService::class, $service);
    }

    // ===== Lease Statistics Tests =====

    public function test_get_lease_statistics_returns_correct_format(): void
    {
        $service = new ReportService;

        $stats = $service->getLeaseStatistics($this->tenant->id);

        $this->assertArrayHasKey('total_leases', $stats);
        $this->assertArrayHasKey('new_leases', $stats);
        $this->assertArrayHasKey('active_leases', $stats);
        $this->assertArrayHasKey('expired_leases', $stats);
        $this->assertArrayHasKey('terminated_leases', $stats);
        $this->assertArrayHasKey('percent_new_leases', $stats);
        $this->assertArrayHasKey('percent_active_leases', $stats);
        $this->assertArrayHasKey('percent_expired_leases', $stats);
        $this->assertArrayHasKey('percent_terminated_leases', $stats);
        $this->assertArrayHasKey('active_commercial_leases', $stats);
        $this->assertArrayHasKey('active_residential_leases', $stats);
        $this->assertArrayHasKey('current_month_collection', $stats);
        $this->assertArrayHasKey('current_year_collection', $stats);
        $this->assertArrayHasKey('paid_collection_current_month', $stats);
        $this->assertArrayHasKey('paid_collection_current_year', $stats);
    }

    public function test_lease_statistics_returns_zeros_for_empty_tenant(): void
    {
        $service = new ReportService;

        $stats = $service->getLeaseStatistics($this->tenant->id);

        $this->assertEquals(0, $stats['total_leases']);
        $this->assertEquals(0, $stats['new_leases']);
        $this->assertEquals(0, $stats['active_leases']);
        $this->assertEquals(0, $stats['expired_leases']);
        $this->assertEquals(0, $stats['terminated_leases']);
        $this->assertEquals(0, $stats['percent_new_leases']);
        $this->assertEquals(0, $stats['percent_active_leases']);
        $this->assertEquals(0, $stats['percent_expired_leases']);
        $this->assertEquals(0, $stats['percent_terminated_leases']);
        $this->assertEquals(0, $stats['active_commercial_leases']);
        $this->assertEquals(0, $stats['active_residential_leases']);
        $this->assertEquals(0, $stats['current_month_collection']);
        $this->assertEquals(0, $stats['current_year_collection']);
        $this->assertEquals(0, $stats['paid_collection_current_month']);
        $this->assertEquals(0, $stats['paid_collection_current_year']);
    }

    // ===== Expiring Leases Tests =====

    public function test_get_expiring_leases_report_returns_collection(): void
    {
        $service = new ReportService;

        $leases = $service->getExpiringLeasesReport($this->tenant->id);

        $this->assertInstanceOf(Collection::class, $leases);
    }

    public function test_get_expiring_leases_report_returns_empty_for_empty_tenant(): void
    {
        $service = new ReportService;

        $leases = $service->getExpiringLeasesReport($this->tenant->id);

        $this->assertTrue($leases->isEmpty());
    }

    // ===== Leases By Status Tests =====

    public function test_get_leases_by_status_report_returns_correct_format(): void
    {
        $service = new ReportService;

        $report = $service->getLeasesByStatusReport($this->tenant->id);

        $this->assertArrayHasKey('statuses', $report);
        $this->assertArrayHasKey('total', $report);
        $this->assertIsArray($report['statuses']);
    }

    public function test_leases_by_status_returns_zeros_for_empty_tenant(): void
    {
        $service = new ReportService;

        $report = $service->getLeasesByStatusReport($this->tenant->id);

        $this->assertEquals(0, $report['total']);
        $this->assertEmpty($report['statuses']);
    }

    // ===== Maintenance Statistics Tests =====

    public function test_get_maintenance_statistics_returns_correct_format(): void
    {
        $service = new ReportService;

        $stats = $service->getMaintenanceStatistics($this->tenant->id);

        $this->assertArrayHasKey('total_requests', $stats);
        $this->assertArrayHasKey('open_requests', $stats);
        $this->assertArrayHasKey('in_progress_requests', $stats);
        $this->assertArrayHasKey('completed_requests', $stats);
        $this->assertArrayHasKey('closed_requests', $stats);
        $this->assertArrayHasKey('requests_this_month', $stats);
        $this->assertArrayHasKey('requests_this_year', $stats);
        $this->assertArrayHasKey('high_priority_count', $stats);
        $this->assertArrayHasKey('average_resolution_days', $stats);
    }

    public function test_maintenance_statistics_returns_zeros_for_empty_tenant(): void
    {
        $service = new ReportService;

        $stats = $service->getMaintenanceStatistics($this->tenant->id);

        $this->assertEquals(0, $stats['total_requests']);
        $this->assertEquals(0, $stats['open_requests']);
        $this->assertEquals(0, $stats['in_progress_requests']);
        $this->assertEquals(0, $stats['completed_requests']);
        $this->assertEquals(0, $stats['closed_requests']);
        $this->assertEquals(0, $stats['requests_this_month']);
        $this->assertEquals(0, $stats['requests_this_year']);
        $this->assertEquals(0, $stats['high_priority_count']);
        $this->assertEquals(0, $stats['average_resolution_days']);
    }

    // ===== Maintenance By Category Tests =====

    public function test_get_maintenance_by_category_report_returns_correct_format(): void
    {
        $service = new ReportService;

        $report = $service->getMaintenanceByCategoryReport($this->tenant->id);

        $this->assertArrayHasKey('categories', $report);
        $this->assertArrayHasKey('total', $report);
        $this->assertIsArray($report['categories']);
    }

    public function test_maintenance_by_category_returns_zeros_for_empty_tenant(): void
    {
        $service = new ReportService;

        $report = $service->getMaintenanceByCategoryReport($this->tenant->id);

        $this->assertEquals(0, $report['total']);
        $this->assertEmpty($report['categories']);
    }

    // ===== Maintenance By Priority Tests =====

    public function test_get_maintenance_by_priority_report_returns_correct_format(): void
    {
        $service = new ReportService;

        $report = $service->getMaintenanceByPriorityReport($this->tenant->id);

        $this->assertArrayHasKey('low', $report);
        $this->assertArrayHasKey('medium', $report);
        $this->assertArrayHasKey('high', $report);
        $this->assertArrayHasKey('urgent', $report);
    }

    public function test_maintenance_by_priority_returns_zeros_for_empty_tenant(): void
    {
        $service = new ReportService;

        $report = $service->getMaintenanceByPriorityReport($this->tenant->id);

        $this->assertEquals(0, $report['low']);
        $this->assertEquals(0, $report['medium']);
        $this->assertEquals(0, $report['high']);
        $this->assertEquals(0, $report['urgent']);
    }

    // ===== Maintenance Trend Tests =====

    public function test_get_maintenance_trend_report_returns_array(): void
    {
        $service = new ReportService;

        $report = $service->getMaintenanceTrendReport($this->tenant->id);

        $this->assertIsArray($report);
    }

    public function test_maintenance_trend_returns_empty_for_empty_tenant(): void
    {
        $service = new ReportService;

        $report = $service->getMaintenanceTrendReport($this->tenant->id);

        $this->assertEmpty($report);
    }

    // ===== Occupancy Report Tests =====

    public function test_get_occupancy_report_returns_correct_format(): void
    {
        $service = new ReportService;

        $report = $service->getOccupancyReport($this->tenant->id);

        $this->assertArrayHasKey('total_units', $report);
        $this->assertArrayHasKey('occupied_units', $report);
        $this->assertArrayHasKey('vacant_units', $report);
        $this->assertArrayHasKey('maintenance_units', $report);
        $this->assertArrayHasKey('occupancy_rate', $report);
        $this->assertArrayHasKey('vacancy_rate', $report);
    }

    public function test_occupancy_report_returns_zeros_for_empty_tenant(): void
    {
        $service = new ReportService;

        $report = $service->getOccupancyReport($this->tenant->id);

        $this->assertEquals(0, $report['total_units']);
        $this->assertEquals(0, $report['occupied_units']);
        $this->assertEquals(0, $report['vacant_units']);
        $this->assertEquals(0, $report['maintenance_units']);
        $this->assertEquals(0, $report['occupancy_rate']);
        $this->assertEquals(0, $report['vacancy_rate']);
    }

    // ===== Rent Collection Report Tests =====

    public function test_get_rent_collection_report_returns_correct_format(): void
    {
        $service = new ReportService;

        $report = $service->getRentCollectionReport($this->tenant->id);

        $this->assertArrayHasKey('total_due', $report);
        $this->assertArrayHasKey('total_collected', $report);
        $this->assertArrayHasKey('total_pending', $report);
        $this->assertArrayHasKey('total_overdue', $report);
        $this->assertArrayHasKey('collection_rate', $report);
        $this->assertArrayHasKey('period', $report);
        $this->assertArrayHasKey('start', $report['period']);
        $this->assertArrayHasKey('end', $report['period']);
    }

    public function test_rent_collection_report_returns_zeros_for_empty_tenant(): void
    {
        $service = new ReportService;

        $report = $service->getRentCollectionReport($this->tenant->id);

        $this->assertEquals(0, $report['total_due']);
        $this->assertEquals(0, $report['total_collected']);
        $this->assertEquals(0, $report['total_pending']);
        $this->assertEquals(0, $report['total_overdue']);
        $this->assertEquals(0, $report['collection_rate']);
    }

    // ===== Inertia Page Tests =====

    public function test_reports_index_page_renders_with_statistics(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('reports/index')
            ->has('leaseStatistics')
            ->has('maintenanceStatistics')
            ->has('occupancyReport')
        );
    }

    public function test_lease_reports_page_renders_with_data(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.leases'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('reports/leases')
            ->has('statistics')
            ->has('statusReport')
            ->has('expiringLeases')
            ->has('rentCollection')
        );
    }

    public function test_maintenance_reports_page_renders_with_data(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.maintenance'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('reports/maintenance')
            ->has('statistics')
            ->has('categoryReport')
            ->has('priorityReport')
            ->has('trendReport')
        );
    }

    // ===== API Tests =====

    public function test_guests_are_redirected_from_lease_statistics_api(): void
    {
        $response = $this->get('/api/reports/leases/statistics');
        $response->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_maintenance_statistics_api(): void
    {
        $response = $this->get('/api/reports/maintenance/statistics');
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_access_lease_statistics_api(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/api/reports/leases/statistics');

        $response->assertOk();
        $response->assertJsonStructure([
            'total_leases',
            'new_leases',
            'active_leases',
            'expired_leases',
            'terminated_leases',
        ]);
    }

    public function test_authenticated_users_can_access_maintenance_statistics_api(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/api/reports/maintenance/statistics');

        $response->assertOk();
        $response->assertJsonStructure([
            'total_requests',
            'open_requests',
            'in_progress_requests',
            'completed_requests',
            'closed_requests',
        ]);
    }

    public function test_authenticated_users_can_access_occupancy_api(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/api/reports/occupancy');

        $response->assertOk();
        $response->assertJsonStructure([
            'total_units',
            'occupied_units',
            'vacant_units',
            'maintenance_units',
            'occupancy_rate',
            'vacancy_rate',
        ]);
    }
}
