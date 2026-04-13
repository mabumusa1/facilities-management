<?php

namespace Tests\Feature;

use App\Models\Status;
use Database\Seeders\StatusSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatusSystemTest extends TestCase
{
    use RefreshDatabase;

    // ==================== Basic CRUD Tests ====================

    public function test_status_can_be_created(): void
    {
        $status = Status::factory()->create([
            'name' => 'Test Status',
            'name_ar' => 'حالة تجريبية',
            'domain' => 'lease',
            'slug' => 'lease_test_status',
        ]);

        $this->assertDatabaseHas('statuses', [
            'name' => 'Test Status',
            'name_ar' => 'حالة تجريبية',
            'domain' => 'lease',
            'slug' => 'lease_test_status',
        ]);
        $this->assertTrue($status->is_active);
    }

    public function test_status_slug_must_be_unique(): void
    {
        Status::factory()->create(['slug' => 'unique_slug']);

        $this->expectException(QueryException::class);
        Status::factory()->create(['slug' => 'unique_slug']);
    }

    public function test_status_can_be_updated(): void
    {
        $status = Status::factory()->create([
            'name' => 'Original Name',
        ]);

        $status->update(['name' => 'Updated Name']);

        $this->assertDatabaseHas('statuses', [
            'id' => $status->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_status_can_be_deleted(): void
    {
        $status = Status::factory()->create();
        $statusId = $status->id;

        $status->delete();

        $this->assertDatabaseMissing('statuses', ['id' => $statusId]);
    }

    // ==================== Domain Tests ====================

    public function test_status_for_domain_scope(): void
    {
        Status::factory()->forDomain('lease')->count(3)->create();
        Status::factory()->forDomain('service_request')->count(2)->create();

        $leaseStatuses = Status::forDomain('lease')->get();
        $requestStatuses = Status::forDomain('service_request')->get();

        $this->assertCount(3, $leaseStatuses);
        $this->assertCount(2, $requestStatuses);
    }

    public function test_status_domains_constant(): void
    {
        $domains = Status::domains();

        $this->assertContains(Status::DOMAIN_SERVICE_REQUEST, $domains);
        $this->assertContains(Status::DOMAIN_VISITOR_ACCESS, $domains);
        $this->assertContains(Status::DOMAIN_FACILITY_BOOKING, $domains);
        $this->assertContains(Status::DOMAIN_MARKETPLACE_UNIT, $domains);
        $this->assertContains(Status::DOMAIN_MARKETPLACE_VISIT, $domains);
        $this->assertContains(Status::DOMAIN_LEASE, $domains);
        $this->assertContains(Status::DOMAIN_VISIT_SCHEDULING, $domains);
        $this->assertContains(Status::DOMAIN_BOOKING_CONTRACT, $domains);
        $this->assertContains(Status::DOMAIN_APPLICATION, $domains);
        $this->assertContains(Status::DOMAIN_TRANSACTION, $domains);
        $this->assertContains(Status::DOMAIN_UNIT, $domains);
    }

    // ==================== Scope Tests ====================

    public function test_status_active_scope(): void
    {
        Status::factory()->count(3)->create();
        Status::factory()->inactive()->create();

        $this->assertEquals(3, Status::active()->count());
    }

    public function test_find_by_slug(): void
    {
        $status = Status::factory()->create([
            'slug' => 'test_find_slug',
        ]);

        $found = Status::findBySlug('test_find_slug');

        $this->assertNotNull($found);
        $this->assertEquals($status->id, $found->id);
    }

    public function test_find_by_slug_returns_null_for_non_existent(): void
    {
        $found = Status::findBySlug('non_existent_slug');

        $this->assertNull($found);
    }

    // ==================== Factory State Tests ====================

    public function test_factory_creates_valid_status(): void
    {
        $status = Status::factory()->create();

        $this->assertNotEmpty($status->name);
        $this->assertNotEmpty($status->domain);
        $this->assertNotEmpty($status->slug);
        $this->assertTrue($status->is_active);
    }

    public function test_factory_inactive_state(): void
    {
        $status = Status::factory()->inactive()->create();

        $this->assertFalse($status->is_active);
    }

    public function test_factory_for_domain_state(): void
    {
        $status = Status::factory()
            ->forDomain('visitor_access')
            ->create(['name' => 'Test Visitor Status']);

        $this->assertEquals('visitor_access', $status->domain);
    }

    public function test_factory_with_slug_state(): void
    {
        $status = Status::factory()
            ->withSlug('custom_slug')
            ->create();

        $this->assertEquals('custom_slug', $status->slug);
    }

    public function test_factory_with_color_state(): void
    {
        $status = Status::factory()
            ->withColor('#FF0000')
            ->create();

        $this->assertEquals('#FF0000', $status->color);
    }

    // ==================== Attribute Tests ====================

    public function test_status_casts_priority_to_integer(): void
    {
        $status = Status::factory()->create(['priority' => '5']);

        $this->assertIsInt($status->priority);
        $this->assertEquals(5, $status->priority);
    }

    public function test_status_casts_is_active_to_boolean(): void
    {
        $status = Status::factory()->create(['is_active' => 1]);

        $this->assertIsBool($status->is_active);
        $this->assertTrue($status->is_active);
    }

    // ==================== Seeder Tests ====================

    public function test_status_seeder_creates_all_domains(): void
    {
        $this->seed(StatusSeeder::class);

        // Check each domain has statuses
        $this->assertTrue(Status::forDomain('service_request')->exists());
        $this->assertTrue(Status::forDomain('visitor_access')->exists());
        $this->assertTrue(Status::forDomain('facility_booking')->exists());
        $this->assertTrue(Status::forDomain('marketplace_unit')->exists());
        $this->assertTrue(Status::forDomain('marketplace_visit')->exists());
        $this->assertTrue(Status::forDomain('lease')->exists());
        $this->assertTrue(Status::forDomain('visit_scheduling')->exists());
        $this->assertTrue(Status::forDomain('booking_contract')->exists());
        $this->assertTrue(Status::forDomain('application')->exists());
        $this->assertTrue(Status::forDomain('transaction')->exists());
        $this->assertTrue(Status::forDomain('unit')->exists());
    }

    public function test_status_seeder_creates_service_request_statuses(): void
    {
        $this->seed(StatusSeeder::class);

        $statuses = Status::forDomain('service_request')->get();

        $this->assertTrue($statuses->where('slug', 'service_request_new')->isNotEmpty());
        $this->assertTrue($statuses->where('slug', 'service_request_assigned')->isNotEmpty());
        $this->assertTrue($statuses->where('slug', 'service_request_in_progress')->isNotEmpty());
        $this->assertTrue($statuses->where('slug', 'service_request_resolved')->isNotEmpty());
        $this->assertTrue($statuses->where('slug', 'service_request_cancelled')->isNotEmpty());
    }

    public function test_status_seeder_creates_lease_statuses(): void
    {
        $this->seed(StatusSeeder::class);

        $statuses = Status::forDomain('lease')->get();

        $this->assertTrue($statuses->where('slug', 'lease_draft')->isNotEmpty());
        $this->assertTrue($statuses->where('slug', 'lease_active')->isNotEmpty());
        $this->assertTrue($statuses->where('slug', 'lease_expired')->isNotEmpty());
        $this->assertTrue($statuses->where('slug', 'lease_terminated')->isNotEmpty());
        $this->assertTrue($statuses->where('slug', 'lease_closed')->isNotEmpty());
    }

    public function test_status_seeder_creates_visitor_access_statuses(): void
    {
        $this->seed(StatusSeeder::class);

        $statuses = Status::forDomain('visitor_access')->get();

        $this->assertTrue($statuses->where('slug', 'visitor_access_new')->isNotEmpty());
        $this->assertTrue($statuses->where('slug', 'visitor_access_pending')->isNotEmpty());
        $this->assertTrue($statuses->where('slug', 'visitor_access_approved')->isNotEmpty());
        $this->assertTrue($statuses->where('slug', 'visitor_access_rejected')->isNotEmpty());
        $this->assertTrue($statuses->where('slug', 'visitor_access_checked_in')->isNotEmpty());
        $this->assertTrue($statuses->where('slug', 'visitor_access_checked_out')->isNotEmpty());
    }

    public function test_status_seeder_creates_unit_statuses(): void
    {
        $this->seed(StatusSeeder::class);

        $statuses = Status::forDomain('unit')->get();

        $this->assertTrue($statuses->where('slug', 'unit_vacant')->isNotEmpty());
        $this->assertTrue($statuses->where('slug', 'unit_occupied')->isNotEmpty());
        $this->assertTrue($statuses->where('slug', 'unit_under_maintenance')->isNotEmpty());
        $this->assertTrue($statuses->where('slug', 'unit_reserved')->isNotEmpty());
    }

    public function test_status_seeder_creates_statuses_with_arabic_names(): void
    {
        $this->seed(StatusSeeder::class);

        $status = Status::findBySlug('lease_active');

        $this->assertNotNull($status);
        $this->assertNotNull($status->name_ar);
        $this->assertEquals('عقد ساري', $status->name_ar);
    }

    public function test_status_seeder_creates_statuses_with_colors(): void
    {
        $this->seed(StatusSeeder::class);

        $status = Status::findBySlug('lease_active');

        $this->assertNotNull($status);
        $this->assertNotNull($status->color);
        $this->assertEquals('#22C55E', $status->color);
    }

    public function test_status_seeder_creates_statuses_with_icons(): void
    {
        $this->seed(StatusSeeder::class);

        $status = Status::findBySlug('lease_active');

        $this->assertNotNull($status);
        $this->assertNotNull($status->icon);
        $this->assertEquals('check-circle', $status->icon);
    }

    public function test_status_seeder_total_count(): void
    {
        $this->seed(StatusSeeder::class);

        // Should have all 62 statuses as defined in seeder
        $this->assertGreaterThanOrEqual(60, Status::count());
    }

    public function test_status_seeder_is_idempotent(): void
    {
        $this->seed(StatusSeeder::class);
        $countAfterFirst = Status::count();

        $this->seed(StatusSeeder::class);
        $countAfterSecond = Status::count();

        $this->assertEquals($countAfterFirst, $countAfterSecond);
    }

    // ==================== Business Logic Tests ====================

    public function test_booking_contract_statuses_have_correct_priority_order(): void
    {
        $this->seed(StatusSeeder::class);

        $statuses = Status::forDomain('booking_contract')
            ->orderBy('priority')
            ->get();

        // First should be initial booking
        $this->assertEquals('booking_contract_initial_created', $statuses->first()->slug);
    }

    public function test_can_filter_multiple_domains(): void
    {
        $this->seed(StatusSeeder::class);

        $domains = ['lease', 'unit'];
        $statuses = Status::whereIn('domain', $domains)->get();

        $this->assertTrue($statuses->where('domain', 'lease')->isNotEmpty());
        $this->assertTrue($statuses->where('domain', 'unit')->isNotEmpty());
        $this->assertTrue($statuses->where('domain', 'service_request')->isEmpty());
    }
}
