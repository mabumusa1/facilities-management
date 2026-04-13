<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Community;
use App\Models\Contact;
use App\Models\Lease;
use App\Models\Status;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaseEntityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure statuses exist
        Status::factory()->create([
            'id' => 1,
            'name' => 'Draft',
            'domain' => 'lease',
            'slug' => 'lease_draft',
        ]);
        Status::factory()->create([
            'id' => 32,
            'name' => 'Active',
            'domain' => 'lease',
            'slug' => 'lease_active',
        ]);
        Status::factory()->create([
            'id' => 33,
            'name' => 'Expired',
            'domain' => 'lease',
            'slug' => 'lease_expired',
        ]);
    }

    public function test_can_create_lease(): void
    {
        $community = Community::factory()->create();
        $building = Building::factory()->create(['community_id' => $community->id]);
        $tenant = Contact::factory()->tenant()->create();

        $lease = Lease::factory()->create([
            'community_id' => $community->id,
            'building_id' => $building->id,
            'tenant_id' => $tenant->id,
        ]);

        $this->assertDatabaseHas('leases', [
            'id' => $lease->id,
            'tenant_id' => $tenant->id,
            'community_id' => $community->id,
            'building_id' => $building->id,
        ]);
    }

    public function test_lease_belongs_to_tenant(): void
    {
        $lease = Lease::factory()->create();

        $this->assertInstanceOf(Contact::class, $lease->tenant);
    }

    public function test_lease_belongs_to_status(): void
    {
        $lease = Lease::factory()->create();

        $this->assertInstanceOf(Status::class, $lease->status);
    }

    public function test_lease_belongs_to_community(): void
    {
        $lease = Lease::factory()->create();

        $this->assertInstanceOf(Community::class, $lease->community);
    }

    public function test_lease_belongs_to_building(): void
    {
        $lease = Lease::factory()->create();

        $this->assertInstanceOf(Building::class, $lease->building);
    }

    public function test_lease_can_have_multiple_units(): void
    {
        $community = Community::factory()->create();
        $building = Building::factory()->create(['community_id' => $community->id]);
        $lease = Lease::factory()->create([
            'community_id' => $community->id,
            'building_id' => $building->id,
        ]);

        $unit1 = Unit::factory()->create([
            'community_id' => $community->id,
            'building_id' => $building->id,
        ]);
        $unit2 = Unit::factory()->create([
            'community_id' => $community->id,
            'building_id' => $building->id,
        ]);

        $lease->units()->attach($unit1->id, [
            'rental_annual_type' => 'total',
            'annual_rental_amount' => 50000,
        ]);
        $lease->units()->attach($unit2->id, [
            'rental_annual_type' => 'total',
            'annual_rental_amount' => 45000,
        ]);

        $this->assertCount(2, $lease->units);
        $this->assertEquals(50000, $lease->units[0]->pivot->annual_rental_amount);
    }

    public function test_lease_has_transactions(): void
    {
        $lease = Lease::factory()->create();

        $this->assertInstanceOf(Collection::class, $lease->transactions);
    }

    public function test_active_scope_returns_only_active_leases(): void
    {
        Lease::factory()->active()->count(3)->create();
        Lease::factory()->expired()->count(2)->create();
        Lease::factory()->upcoming()->count(1)->create();

        $activeLeases = Lease::active()->get();

        $this->assertCount(3, $activeLeases);
        $this->assertTrue($activeLeases->every(fn ($lease) => $lease->isActive()));
    }

    public function test_expired_scope_returns_only_expired_leases(): void
    {
        Lease::factory()->active()->count(2)->create();
        Lease::factory()->expired()->count(3)->create();

        $expiredLeases = Lease::expired()->get();

        $this->assertCount(3, $expiredLeases);
        $this->assertTrue($expiredLeases->every(fn ($lease) => $lease->isExpired()));
    }

    public function test_upcoming_scope_returns_only_upcoming_leases(): void
    {
        Lease::factory()->active()->count(2)->create();
        Lease::factory()->upcoming()->count(3)->create();

        $upcomingLeases = Lease::upcoming()->get();

        $this->assertCount(3, $upcomingLeases);
        $this->assertTrue($upcomingLeases->every(fn ($lease) => $lease->start_date > now()));
    }

    public function test_expiring_within_days_scope(): void
    {
        Lease::factory()->expiringSoon(10)->count(2)->create();
        Lease::factory()->expiringSoon(60)->count(3)->create();

        $leasesExpiringSoon = Lease::expiringWithinDays(30)->get();

        $this->assertCount(2, $leasesExpiringSoon);
    }

    public function test_lease_can_be_marked_as_terminated(): void
    {
        $lease = Lease::factory()->active()->create();

        $lease->markAsTerminated();

        $this->assertTrue($lease->fresh()->is_move_out);
        $this->assertNotNull($lease->fresh()->actual_end_at);
    }

    public function test_lease_can_be_marked_as_renewed(): void
    {
        $oldLease = Lease::factory()->active()->create();
        $newLease = Lease::factory()->active()->create();

        $oldLease->markAsRenewed($newLease);

        $this->assertTrue($oldLease->fresh()->is_renew);
        $this->assertEquals($oldLease->id, $newLease->fresh()->parent_lease_id);
    }

    public function test_get_days_remaining(): void
    {
        $lease = Lease::factory()->expiringSoon(10)->create();

        $daysRemaining = $lease->getDaysRemaining();

        $this->assertGreaterThanOrEqual(9, $daysRemaining);
        $this->assertLessThanOrEqual(11, $daysRemaining);
    }

    public function test_get_duration(): void
    {
        $lease = Lease::factory()->create([
            'number_of_years' => 2,
            'number_of_months' => 6,
            'number_of_days' => 15,
        ]);

        $duration = $lease->getDuration();

        $this->assertStringContainsString('2 years', $duration);
        $this->assertStringContainsString('6 months', $duration);
        $this->assertStringContainsString('15 days', $duration);
    }

    public function test_sublease_relationship(): void
    {
        $parentLease = Lease::factory()->create();
        $subLease = Lease::factory()->sublease($parentLease)->create();

        $this->assertTrue($subLease->is_sub_lease);
        $this->assertEquals($parentLease->id, $subLease->parent_lease_id);
        $this->assertContains($subLease->id, $parentLease->subleases->pluck('id'));
    }

    public function test_by_tenant_scope(): void
    {
        $tenant = Contact::factory()->tenant()->create();
        Lease::factory()->forTenant($tenant->id)->count(3)->create();
        Lease::factory()->count(2)->create();

        $tenantLeases = Lease::byTenant($tenant->id)->get();

        $this->assertCount(3, $tenantLeases);
        $this->assertTrue($tenantLeases->every(fn ($lease) => $lease->tenant_id === $tenant->id));
    }

    public function test_by_unit_scope(): void
    {
        $community = Community::factory()->create();
        $building = Building::factory()->create(['community_id' => $community->id]);
        $unit = Unit::factory()->create([
            'community_id' => $community->id,
            'building_id' => $building->id,
        ]);

        $lease1 = Lease::factory()->create([
            'community_id' => $community->id,
            'building_id' => $building->id,
        ]);
        $lease2 = Lease::factory()->create([
            'community_id' => $community->id,
            'building_id' => $building->id,
        ]);

        $lease1->units()->attach($unit->id, [
            'rental_annual_type' => 'total',
            'annual_rental_amount' => 50000,
        ]);

        Lease::factory()->count(2)->create();

        $unitLeases = Lease::byUnit($unit->id)->get();

        $this->assertCount(1, $unitLeases);
        $this->assertEquals($lease1->id, $unitLeases->first()->id);
    }

    public function test_residential_and_commercial_scopes(): void
    {
        Lease::factory()->residential()->count(3)->create();
        Lease::factory()->commercial()->count(2)->create();

        $residentialLeases = Lease::residential()->get();
        $commercialLeases = Lease::commercial()->get();

        $this->assertCount(3, $residentialLeases);
        $this->assertCount(2, $commercialLeases);
    }
}
