<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\FacilityBooking;
use App\Models\MarketplaceOffer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardModuleParityTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_module_list_routes_render_expected_components(): void
    {
        $user = User::factory()->create();

        $routes = [
            'dashboard.announcements.index' => 'announcements/index',
            'dashboard.announcements.create' => 'announcements/create',
            'dashboard.issues.index' => 'service-requests/index',
            'dashboard.issues.create' => 'service-requests/create',
            'dashboard.bookings.index' => 'dashboard/list',
            'dashboard.booking-contracts.index' => 'dashboard/list',
            'dashboard.visits.index' => 'dashboard/list',
            'dashboard.complaints.index' => 'dashboard/list',
            'dashboard.suggestions.index' => 'dashboard/list',
            'dashboard.reports.index' => 'dashboard/list',
            'dashboard.payment.index' => 'dashboard/list',
            'dashboard.offers.index' => 'dashboard/list',
            'dashboard.offers.create' => 'dashboard/form',
            'dashboard.directory.index' => 'dashboard/list',
            'dashboard.directory.create' => 'dashboard/form',
            'dashboard.directory.update' => 'dashboard/form',
            'dashboard.move-out-tenants.index' => 'dashboard/list',
            'dashboard.system-reports.index' => 'dashboard/list',
            'dashboard.system-reports.lease' => 'dashboard/list',
            'dashboard.system-reports.maintenance' => 'dashboard/list',
            'dashboard.power-bi-reports.index' => 'dashboard/list',
        ];

        foreach ($routes as $name => $component) {
            $response = $this->actingAs($user)->get(route($name));

            $response->assertOk();
            $response->assertInertia(fn ($page) => $page->component($component));
        }
    }

    public function test_dashboard_module_detail_routes_render_expected_components(): void
    {
        $user = User::factory()->create();

        $announcement = Announcement::factory()->create(['created_by' => $user->id]);
        $booking = FacilityBooking::factory()->create();
        $offer = MarketplaceOffer::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard.announcements.show', $announcement))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('announcements/show'));

        $this->actingAs($user)
            ->get(route('dashboard.announcements.edit', $announcement))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('announcements/edit'));

        $this->actingAs($user)
            ->get(route('dashboard.bookings.show', $booking))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('dashboard/detail'));

        $this->actingAs($user)
            ->get(route('dashboard.offers.view', $offer))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('dashboard/detail'));
    }

    public function test_missing_dashboard_detail_models_return_not_found(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('dashboard.issues.view', 999999))->assertNotFound();
        $this->actingAs($user)->get(route('dashboard.issues.assign', 999999))->assertNotFound();
        $this->actingAs($user)->get(route('dashboard.complaints.show', 999999))->assertNotFound();
        $this->actingAs($user)->get(route('dashboard.suggestions.show', 999999))->assertNotFound();
        $this->actingAs($user)->get(route('dashboard.directory.show', 999999))->assertNotFound();
        $this->actingAs($user)->get(route('dashboard.move-out-tenants.show', 999999))->assertNotFound();
    }
}
