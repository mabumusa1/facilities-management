<?php

namespace Tests\Feature\Feature\Marketplace;

use App\Models\AccountMembership;
use App\Models\Community;
use App\Models\MarketplaceUnit;
use App\Models\MarketplaceVisit;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class MarketplaceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function authenticateUser(): Tenant
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Marketplace Account']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($user);

        return $tenant;
    }

    /**
     * @return array{0: Community, 1: Unit, 2: MarketplaceUnit}
     */
    private function listingDependencies(int $tenantId): array
    {
        $community = Community::factory()->create([
            'name' => 'Central Community',
            'account_tenant_id' => $tenantId,
        ]);

        $unit = Unit::factory()->create([
            'name' => 'Unit 201',
            'rf_community_id' => $community->id,
            'account_tenant_id' => $tenantId,
        ]);

        $listing = MarketplaceUnit::create([
            'unit_id' => $unit->id,
            'listing_type' => 'sale',
            'price' => 980000,
            'is_active' => true,
        ]);

        return [$community, $unit, $listing];
    }

    /**
     * Ensure the main marketplace dashboard and admin units API payloads render.
     */
    public function test_overview_page_and_admin_units_api_return_expected_payloads(): void
    {
        $tenant = $this->authenticateUser();
        [, , $listing] = $this->listingDependencies($tenant->id);

        $page = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('marketplace.overview'));

        $page
            ->assertOk()
            ->assertInertia(fn (Assert $inertia) => $inertia
                ->component('marketplace/Overview')
                ->where('stats.activeListings', 1)
                ->has('recentListings', 1)
                ->where('recentListings.0.id', $listing->id)
            );

        $api = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('marketplace-admin.units'));

        $api
            ->assertOk()
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }

    public function test_sales_lead_creation_sets_expected_interest_type(): void
    {
        $tenant = $this->authenticateUser();

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('marketplace.customers.sales-lead'), [
                'name' => 'Rana Lead',
                'phone_number' => '0500000011',
                'email' => 'rana@example.com',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('rf_leads', [
            'name' => 'Rana Lead',
            'phone_number' => '0500000011',
            'interested' => 'sale',
            'account_tenant_id' => $tenant->id,
        ]);
    }

    public function test_schedule_then_cancel_visit_updates_status_flow(): void
    {
        $tenant = $this->authenticateUser();
        [, , $listing] = $this->listingDependencies($tenant->id);

        $pendingStatus = Status::factory()->create([
            'type' => 'property_visit',
            'name_en' => 'Pending',
        ]);

        $canceledStatus = Status::factory()->create([
            'type' => 'property_visit',
            'name_en' => 'Canceled',
        ]);

        $schedule = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('marketplace.visits.schedule'), [
                'marketplace_unit_id' => $listing->id,
                'visitor_name' => 'Mona Buyer',
                'visitor_phone' => '0555000000',
                'scheduled_at' => now()->addDay()->toDateTimeString(),
            ]);

        $schedule->assertRedirect();

        $visit = MarketplaceVisit::query()->latest()->firstOrFail();
        $this->assertSame($pendingStatus->id, $visit->status_id);

        $cancel = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('marketplace.visits.cancel', $visit));

        $cancel->assertRedirect();

        $visit = $visit->fresh();
        $this->assertSame($canceledStatus->id, $visit?->status_id);
        $this->assertStringContainsString('Visit canceled on', (string) $visit?->notes);
    }

    public function test_admin_can_list_and_unlist_community_in_marketplace(): void
    {
        $tenant = $this->authenticateUser();
        [$community] = $this->listingDependencies($tenant->id);

        $list = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson(route('marketplace-admin.communities.list', $community), [
                'allow_cash_sale' => true,
                'allow_bank_financing' => true,
            ]);

        $list
            ->assertOk()
            ->assertJsonPath('data.id', $community->id)
            ->assertJsonPath('data.is_market_place', true)
            ->assertJsonPath('data.allow_cash_sale', true);

        $unlist = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson(route('marketplace-admin.communities.unlist', $community));

        $unlist
            ->assertOk()
            ->assertJsonPath('data.id', $community->id)
            ->assertJsonPath('data.is_market_place', false);

        $this->assertDatabaseHas('rf_communities', [
            'id' => $community->id,
            'is_market_place' => false,
        ]);
    }

    public function test_admin_can_create_update_and_delete_marketplace_offers(): void
    {
        $tenant = $this->authenticateUser();
        [, $unit] = $this->listingDependencies($tenant->id);

        $store = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson(route('marketplace-admin.offers.store'), [
                'unit_id' => $unit->id,
                'title' => 'Summer Offer',
                'description' => '10% discount for selected listings.',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonth()->toDateString(),
            ]);

        $store
            ->assertOk()
            ->assertJsonPath('data.unit_id', $unit->id)
            ->assertJsonPath('data.title', 'Summer Offer');

        $offerId = (int) $store->json('data.id');

        $this->assertDatabaseHas('rf_marketplace_offers', [
            'id' => $offerId,
            'unit_id' => $unit->id,
            'title' => 'Summer Offer',
            'account_tenant_id' => $tenant->id,
        ]);

        $update = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->putJson(route('marketplace-admin.offers.update', $offerId), [
                'title' => 'Updated Summer Offer',
                'discount_value' => 15,
            ]);

        $update
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated Summer Offer')
            ->assertJsonPath('data.discount_value', '15.00');

        $delete = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->deleteJson(route('marketplace-admin.offers.destroy', $offerId));

        $delete
            ->assertOk()
            ->assertJsonPath('data.id', $offerId);

        $this->assertDatabaseMissing('rf_marketplace_offers', [
            'id' => $offerId,
        ]);
    }
}
