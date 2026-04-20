<?php

namespace Tests\Feature;

use App\Models\Amenity;
use App\Models\Community;
use App\Models\Dependent;
use App\Models\Feature;
use App\Models\LeadSource;
use App\Models\MarketplaceUnit;
use App\Models\MarketplaceVisit;
use App\Models\Owner;
use App\Models\Resident;
use App\Models\Unit;
use App\Models\UnitArea;
use App\Models\UnitRoom;
use App\Models\UnitSpecification;
use Database\Seeders\AmenitySeeder;
use Database\Seeders\FeatureSeeder;
use Database\Seeders\LeadSourceSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class SupplementaryModelTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_lead_source_seeder(): void
    {
        $this->seed(LeadSourceSeeder::class);

        $this->assertDatabaseCount('rf_lead_sources', 11);
    }

    public function test_feature_seeder(): void
    {
        $this->seed(FeatureSeeder::class);

        $this->assertDatabaseCount('rf_features', 30);
    }

    public function test_amenity_seeder(): void
    {
        $this->seed(AmenitySeeder::class);

        $this->assertDatabaseCount('rf_amenities', 26);
    }

    public function test_dependent_polymorphic_to_resident(): void
    {
        $resident = Resident::factory()->create();
        $dependent = Dependent::factory()->create([
            'dependable_type' => Resident::class,
            'dependable_id' => $resident->id,
        ]);

        $this->assertTrue($dependent->dependable->is($resident));
        $this->assertTrue($resident->dependents->contains($dependent));
    }

    public function test_dependent_polymorphic_to_owner(): void
    {
        $owner = Owner::factory()->create();
        $dependent = Dependent::factory()->create([
            'dependable_type' => Owner::class,
            'dependable_id' => $owner->id,
        ]);

        $this->assertTrue($dependent->dependable->is($owner));
        $this->assertTrue($owner->dependents->contains($dependent));
    }

    public function test_unit_has_specifications(): void
    {
        $unit = Unit::factory()->create();
        UnitSpecification::factory()->count(3)->create(['unit_id' => $unit->id]);

        $this->assertCount(3, $unit->specifications);
    }

    public function test_unit_has_rooms(): void
    {
        $unit = Unit::factory()->create();
        UnitRoom::factory()->count(4)->create(['unit_id' => $unit->id]);

        $this->assertCount(4, $unit->rooms);
    }

    public function test_unit_has_areas(): void
    {
        $unit = Unit::factory()->create();
        UnitArea::factory()->count(2)->create(['unit_id' => $unit->id]);

        $this->assertCount(2, $unit->areas);
    }

    public function test_unit_features_many_to_many(): void
    {
        $unit = Unit::factory()->create();
        $features = Feature::factory()->count(3)->create();

        $unit->features()->attach($features);

        $this->assertCount(3, $unit->features);
    }

    public function test_community_amenities_many_to_many(): void
    {
        $community = Community::factory()->create();
        $amenities = Amenity::factory()->count(4)->create();

        $community->amenities()->attach($amenities);

        $this->assertCount(4, $community->amenities);
    }

    public function test_marketplace_unit_factory(): void
    {
        $listing = MarketplaceUnit::factory()->create();

        $this->assertModelExists($listing);
        $this->assertNotNull($listing->unit);
        $this->assertContains($listing->listing_type, ['rent', 'sale']);
    }

    public function test_marketplace_unit_has_visits(): void
    {
        $listing = MarketplaceUnit::factory()->create();
        MarketplaceVisit::factory()->count(3)->create(['marketplace_unit_id' => $listing->id]);

        $this->assertCount(3, $listing->visits);
    }

    public function test_marketplace_visit_belongs_to_listing(): void
    {
        $visit = MarketplaceVisit::factory()->create();

        $this->assertNotNull($visit->marketplaceUnit);
        $this->assertNotNull($visit->status);
    }

    public function test_lead_source_bilingual_name(): void
    {
        $source = LeadSource::factory()->create([
            'name_ar' => 'إحالة',
            'name_en' => 'Referral',
        ]);

        app()->setLocale('ar');
        $this->assertEquals('إحالة', $source->name);

        app()->setLocale('en');
        $this->assertEquals('Referral', $source->name);
    }

    public function test_unit_area_decimal_cast(): void
    {
        $area = UnitArea::factory()->create(['size' => 150.75]);
        $area->refresh();

        $this->assertEquals('150.75', $area->size);
    }
}
