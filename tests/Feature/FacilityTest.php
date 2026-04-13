<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\Facility;
use App\Models\FacilityCategory;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_facility(): void
    {
        $tenant = Tenant::factory()->create();
        $community = Community::factory()->create(['tenant_id' => $tenant->id]);
        $category = FacilityCategory::factory()->create();

        $facility = Facility::factory()->create([
            'tenant_id' => $tenant->id,
            'community_id' => $community->id,
            'category_id' => $category->id,
        ]);

        $this->assertDatabaseHas('facilities', [
            'id' => $facility->id,
            'name_en' => $facility->name_en,
        ]);
    }

    public function test_belongs_to_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $facility = Facility::factory()->create(['tenant_id' => $tenant->id]);

        $this->assertInstanceOf(Tenant::class, $facility->tenant);
        $this->assertEquals($tenant->id, $facility->tenant->id);
    }

    public function test_belongs_to_community(): void
    {
        $community = Community::factory()->create();
        $facility = Facility::factory()->create(['community_id' => $community->id]);

        $this->assertInstanceOf(Community::class, $facility->community);
        $this->assertEquals($community->id, $facility->community->id);
    }

    public function test_belongs_to_category(): void
    {
        $category = FacilityCategory::factory()->create();
        $facility = Facility::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(FacilityCategory::class, $facility->category);
        $this->assertEquals($category->id, $facility->category->id);
    }

    public function test_is_active_returns_true_for_active_facility(): void
    {
        $facility = Facility::factory()->create(['is_active' => true]);

        $this->assertTrue($facility->isActive());
    }

    public function test_requires_approval_returns_true_when_set(): void
    {
        $facility = Facility::factory()->create(['requires_approval' => true]);

        $this->assertTrue($facility->requiresApproval());
    }

    public function test_operates_on_day_returns_true_for_included_day(): void
    {
        $facility = Facility::factory()->create([
            'operating_days' => ['monday', 'tuesday', 'wednesday'],
        ]);

        $this->assertTrue($facility->operatesOnDay('monday'));
        $this->assertFalse($facility->operatesOnDay('sunday'));
    }

    public function test_get_price_returns_correct_price_for_booking_type(): void
    {
        $facility = Facility::factory()->create([
            'price_per_hour' => 100.50,
            'price_per_day' => 500.00,
            'price_per_session' => 250.75,
        ]);

        $this->assertEquals(100.50, $facility->getPrice('hourly'));
        $this->assertEquals(500.00, $facility->getPrice('daily'));
        $this->assertEquals(250.75, $facility->getPrice('session'));
        $this->assertNull($facility->getPrice('invalid'));
    }

    public function test_can_activate_facility(): void
    {
        $facility = Facility::factory()->create(['is_active' => false]);

        $facility->activate();

        $this->assertTrue($facility->fresh()->is_active);
    }

    public function test_can_deactivate_facility(): void
    {
        $facility = Facility::factory()->create(['is_active' => true]);

        $facility->deactivate();

        $this->assertFalse($facility->fresh()->is_active);
    }

    public function test_soft_deletes_facility(): void
    {
        $facility = Facility::factory()->create();

        $facility->delete();

        $this->assertSoftDeleted('facilities', ['id' => $facility->id]);
    }
}
