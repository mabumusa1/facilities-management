<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\MarketplaceUnit;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketplaceUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_marketplace_unit(): void
    {
        $tenant = Tenant::factory()->create();
        $unit = Unit::factory()->create(['tenant_id' => $tenant->id]);
        $status = Status::factory()->create([
            'domain' => 'marketplace',
            'slug' => 'marketplace_available',
        ]);

        $listing = MarketplaceUnit::factory()->create([
            'tenant_id' => $tenant->id,
            'unit_id' => $unit->id,
            'status_id' => $status->id,
        ]);

        $this->assertDatabaseHas('marketplace_units', [
            'id' => $listing->id,
            'unit_id' => $unit->id,
        ]);
    }

    public function test_belongs_to_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $listing = MarketplaceUnit::factory()->create(['tenant_id' => $tenant->id]);

        $this->assertInstanceOf(Tenant::class, $listing->tenant);
        $this->assertEquals($tenant->id, $listing->tenant->id);
    }

    public function test_belongs_to_unit(): void
    {
        $unit = Unit::factory()->create();
        $listing = MarketplaceUnit::factory()->create(['unit_id' => $unit->id]);

        $this->assertInstanceOf(Unit::class, $listing->unit);
        $this->assertEquals($unit->id, $listing->unit->id);
    }

    public function test_belongs_to_status(): void
    {
        $status = Status::factory()->create();
        $listing = MarketplaceUnit::factory()->create(['status_id' => $status->id]);

        $this->assertInstanceOf(Status::class, $listing->status);
        $this->assertEquals($status->id, $listing->status->id);
    }

    public function test_belongs_to_lister(): void
    {
        $lister = Contact::factory()->create();
        $listing = MarketplaceUnit::factory()->create(['listed_by' => $lister->id]);

        $this->assertInstanceOf(Contact::class, $listing->lister);
        $this->assertEquals($lister->id, $listing->lister->id);
    }

    public function test_belongs_to_agent(): void
    {
        $agent = Contact::factory()->create();
        $listing = MarketplaceUnit::factory()->create(['assigned_agent' => $agent->id]);

        $this->assertInstanceOf(Contact::class, $listing->agent);
        $this->assertEquals($agent->id, $listing->agent->id);
    }

    public function test_belongs_to_buyer(): void
    {
        $buyer = Contact::factory()->create();
        $listing = MarketplaceUnit::factory()->sold()->create(['buyer_id' => $buyer->id]);

        $this->assertInstanceOf(Contact::class, $listing->buyer);
        $this->assertEquals($buyer->id, $listing->buyer->id);
    }

    public function test_can_publish_listing(): void
    {
        $listing = MarketplaceUnit::factory()->unpublished()->create();

        $listing->publish();

        $this->assertTrue($listing->fresh()->isPublished());
        $this->assertNotNull($listing->fresh()->published_at);
    }

    public function test_can_unpublish_listing(): void
    {
        $listing = MarketplaceUnit::factory()->published()->create();

        $listing->unpublish();

        $this->assertFalse($listing->fresh()->isPublished());
    }

    public function test_can_feature_listing(): void
    {
        $listing = MarketplaceUnit::factory()->create(['is_featured' => false]);

        $listing->feature();

        $this->assertTrue($listing->fresh()->isFeatured());
    }

    public function test_can_unfeature_listing(): void
    {
        $listing = MarketplaceUnit::factory()->featured()->create();

        $listing->unfeature();

        $this->assertFalse($listing->fresh()->isFeatured());
    }

    public function test_can_mark_as_sold(): void
    {
        $buyer = Contact::factory()->create();
        $listing = MarketplaceUnit::factory()->published()->create();

        $listing->markAsSold($buyer->id, 1500000.00);

        $fresh = $listing->fresh();
        $this->assertTrue($fresh->isSold());
        $this->assertEquals($buyer->id, $fresh->buyer_id);
        $this->assertEquals(1500000.00, $fresh->sold_price);
        $this->assertFalse($fresh->isPublished());
    }

    public function test_can_increment_views(): void
    {
        $listing = MarketplaceUnit::factory()->create(['views_count' => 10]);

        $listing->incrementViews();

        $this->assertEquals(11, $listing->fresh()->views_count);
    }

    public function test_can_increment_inquiries(): void
    {
        $listing = MarketplaceUnit::factory()->create(['inquiries_count' => 5]);

        $listing->incrementInquiries();

        $this->assertEquals(6, $listing->fresh()->inquiries_count);
    }

    public function test_is_published_returns_true_for_published_listing(): void
    {
        $listing = MarketplaceUnit::factory()->published()->create();

        $this->assertTrue($listing->isPublished());
    }

    public function test_is_featured_returns_true_for_featured_listing(): void
    {
        $listing = MarketplaceUnit::factory()->featured()->create();

        $this->assertTrue($listing->isFeatured());
    }

    public function test_is_sold_returns_true_for_sold_listing(): void
    {
        $listing = MarketplaceUnit::factory()->sold()->create();

        $this->assertTrue($listing->isSold());
    }

    public function test_is_expired_returns_true_for_expired_listing(): void
    {
        $listing = MarketplaceUnit::factory()->expired()->create();

        $this->assertTrue($listing->isExpired());
    }

    public function test_is_price_negotiable(): void
    {
        $listing = MarketplaceUnit::factory()->create(['price_negotiable' => true]);

        $this->assertTrue($listing->isPriceNegotiable());
    }

    public function test_get_discount_percentage(): void
    {
        $listing = MarketplaceUnit::factory()->create([
            'listing_price' => 800000,
            'original_price' => 1000000,
        ]);

        $this->assertEquals(20.00, $listing->getDiscountPercentage());
    }

    public function test_get_discount_percentage_returns_null_without_original_price(): void
    {
        $listing = MarketplaceUnit::factory()->create([
            'listing_price' => 800000,
            'original_price' => null,
        ]);

        $this->assertNull($listing->getDiscountPercentage());
    }

    public function test_soft_deletes_marketplace_unit(): void
    {
        $listing = MarketplaceUnit::factory()->create();

        $listing->delete();

        $this->assertSoftDeleted('marketplace_units', ['id' => $listing->id]);
    }
}
