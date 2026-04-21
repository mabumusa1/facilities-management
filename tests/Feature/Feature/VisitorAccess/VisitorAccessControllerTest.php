<?php

namespace Tests\Feature\Feature\VisitorAccess;

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

class VisitorAccessControllerTest extends TestCase
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
        $tenant = Tenant::create(['name' => 'Visitor Access Account']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($user);

        return $tenant;
    }

    private function createVisit(int $tenantId, int $statusId): MarketplaceVisit
    {
        $community = Community::factory()->create([
            'account_tenant_id' => $tenantId,
        ]);

        $unit = Unit::factory()->create([
            'rf_community_id' => $community->id,
            'account_tenant_id' => $tenantId,
        ]);

        $listing = MarketplaceUnit::create([
            'unit_id' => $unit->id,
            'listing_type' => 'sale',
            'price' => 750000,
            'is_active' => true,
        ]);

        return MarketplaceVisit::create([
            'marketplace_unit_id' => $listing->id,
            'status_id' => $statusId,
            'visitor_name' => 'Visitor One',
            'visitor_phone' => '0555555555',
            'scheduled_at' => now()->addDay(),
        ]);
    }

    /**
     * Verify visit history and details pages render with expected payloads.
     */
    public function test_history_and_details_pages_render(): void
    {
        $tenant = $this->authenticateUser();
        $status = Status::factory()->create([
            'type' => 'property_visit',
            'name_en' => 'Pending',
        ]);
        $visit = $this->createVisit($tenant->id, $status->id);

        $history = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('visitor-access.history'));

        $history
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('visitor-access/History')
                ->has('visits.data', 1)
                ->where('visits.data.0.id', $visit->id)
            );

        $details = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('visitor-access.details', $visit));

        $details
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('visitor-access/Details')
                ->where('visit.id', $visit->id)
                ->where('visit.visitor_name', 'Visitor One')
            );
    }

    public function test_approve_updates_visit_to_approved_status(): void
    {
        $tenant = $this->authenticateUser();

        $pending = Status::factory()->create([
            'type' => 'property_visit',
            'name_en' => 'Pending',
        ]);

        $approved = Status::factory()->create([
            'type' => 'property_visit',
            'name_en' => 'Approved',
        ]);

        $visit = $this->createVisit($tenant->id, $pending->id);

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('visitor-access.approve', $visit));

        $response->assertRedirect();
        $this->assertDatabaseHas('rf_marketplace_visits', [
            'id' => $visit->id,
            'status_id' => $approved->id,
        ]);
    }

    public function test_reject_updates_visit_status_and_notes(): void
    {
        $tenant = $this->authenticateUser();

        $pending = Status::factory()->create([
            'type' => 'property_visit',
            'name_en' => 'Pending',
        ]);

        $rejected = Status::factory()->create([
            'type' => 'property_visit',
            'name_en' => 'Rejected',
        ]);

        $visit = $this->createVisit($tenant->id, $pending->id);

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('visitor-access.reject', $visit), [
                'notes' => 'Rejected due to missing docs',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('rf_marketplace_visits', [
            'id' => $visit->id,
            'status_id' => $rejected->id,
            'notes' => 'Rejected due to missing docs',
        ]);
    }
}
