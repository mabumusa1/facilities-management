<?php

namespace Tests\Feature;

use App\Models\AccountMembership;
use App\Models\Announcement;
use App\Models\Community;
use App\Models\Lead;
use App\Models\MarketplaceUnit;
use App\Models\MarketplaceVisit;
use App\Models\Request as ServiceRequest;
use App\Models\RequestCategory;
use App\Models\Resident;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: User, 1: Tenant}
     */
    private function authenticateUserWithTenant(): array
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Dashboard Account']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($user);

        return [$user, $tenant];
    }

    public function test_guests_are_redirected_to_the_login_page()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard()
    {
        [, $tenant] = $this->authenticateUserWithTenant();

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('dashboard'));

        $response->assertOk();
    }

    public function test_requires_attention_endpoint_returns_expected_items_and_counts()
    {
        [, $tenant] = $this->authenticateUserWithTenant();

        $community = Community::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $unit = Unit::factory()->create([
            'rf_community_id' => $community->id,
            'account_tenant_id' => $tenant->id,
        ]);

        $listing = MarketplaceUnit::create([
            'unit_id' => $unit->id,
            'listing_type' => 'sale',
            'price' => 640000,
            'is_active' => true,
        ]);

        $visitStatus = Status::factory()->create([
            'type' => 'property_visit',
            'name_en' => 'Pending',
        ]);

        MarketplaceVisit::create([
            'marketplace_unit_id' => $listing->id,
            'status_id' => $visitStatus->id,
            'visitor_name' => 'Visit User',
            'visitor_phone' => '0500000000',
        ]);

        $requestStatus = Status::factory()->create([
            'type' => 'request',
            'name_en' => 'Open',
        ]);

        $category = RequestCategory::factory()->create();
        $resident = Resident::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        ServiceRequest::create([
            'category_id' => $category->id,
            'status_id' => $requestStatus->id,
            'requester_type' => Resident::class,
            'requester_id' => $resident->id,
            'title' => 'Broken AC',
            'description' => 'Air conditioner is not working',
            'account_tenant_id' => $tenant->id,
        ]);

        Transaction::factory()->create([
            'due_on' => now()->subDay()->toDateString(),
            'is_paid' => false,
            'account_tenant_id' => $tenant->id,
        ]);

        Announcement::factory()->create([
            'status' => false,
            'account_tenant_id' => $tenant->id,
            'community_id' => $community->id,
        ]);

        Lead::create([
            'name' => 'Unassigned Lead',
            'phone_number' => '0551111111',
            'email' => 'lead@dashboard.test',
            'interested' => 'sale',
            'account_tenant_id' => $tenant->id,
        ]);

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

        $items = collect($response->json('data'))->keyBy('key');

        $this->assertSame(1, $items->get('open_requests')['count']);
        $this->assertSame(1, $items->get('pending_visits')['count']);
        $this->assertSame(1, $items->get('overdue_transactions')['count']);
        $this->assertSame(1, $items->get('draft_announcements')['count']);
        $this->assertSame(1, $items->get('unassigned_leads')['count']);
    }
}
