<?php

namespace Tests\Feature;

use App\Models\Amenity;
use App\Models\City;
use App\Models\Community;
use App\Models\Country;
use App\Models\Currency;
use App\Models\District;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityEntityTest extends TestCase
{
    use RefreshDatabase;

    // ==========================================
    // Model Attribute Tests
    // ==========================================

    public function test_community_has_correct_fillable_attributes(): void
    {
        $tenant = Tenant::factory()->create();
        $country = Country::factory()->create();
        $currency = Currency::factory()->create();
        $city = City::factory()->create();
        $district = District::factory()->create();

        $community = Community::create([
            'tenant_id' => $tenant->id,
            'name' => 'Test Community',
            'country_id' => $country->id,
            'currency_id' => $currency->id,
            'city_id' => $city->id,
            'district_id' => $district->id,
            'sales_commission_rate' => 5.00,
            'rental_commission_rate' => 2.50,
            'is_marketplace' => true,
            'is_buy' => false,
            'marketplace_type' => 'rent',
            'is_off_plan_sale' => false,
            'status' => 'active',
        ]);

        $this->assertEquals('Test Community', $community->name);
        $this->assertEquals($tenant->id, $community->tenant_id);
        $this->assertEquals($country->id, $community->country_id);
        $this->assertEquals($currency->id, $community->currency_id);
        $this->assertEquals($city->id, $community->city_id);
        $this->assertEquals($district->id, $community->district_id);
        $this->assertEquals('5.00', $community->sales_commission_rate);
        $this->assertEquals('2.50', $community->rental_commission_rate);
        $this->assertTrue($community->is_marketplace);
        $this->assertFalse($community->is_buy);
        $this->assertEquals('rent', $community->marketplace_type);
        $this->assertFalse($community->is_off_plan_sale);
        $this->assertEquals('active', $community->status);
    }

    public function test_community_casts_boolean_attributes(): void
    {
        $community = Community::factory()->create([
            'is_marketplace' => 1,
            'is_buy' => 0,
            'is_off_plan_sale' => 1,
        ]);

        $this->assertIsBool($community->is_marketplace);
        $this->assertIsBool($community->is_buy);
        $this->assertIsBool($community->is_off_plan_sale);
        $this->assertTrue($community->is_marketplace);
        $this->assertFalse($community->is_buy);
        $this->assertTrue($community->is_off_plan_sale);
    }

    public function test_community_casts_map_as_array(): void
    {
        $community = Community::factory()->create([
            'map' => ['latitude' => 24.7136, 'longitude' => 46.6753],
        ]);

        $this->assertIsArray($community->map);
        $this->assertEquals(24.7136, $community->map['latitude']);
        $this->assertEquals(46.6753, $community->map['longitude']);
    }

    public function test_community_has_status_constants(): void
    {
        $this->assertEquals('active', Community::STATUS_ACTIVE);
        $this->assertEquals('inactive', Community::STATUS_INACTIVE);
    }

    public function test_community_has_marketplace_type_constants(): void
    {
        $this->assertEquals('rent', Community::MARKETPLACE_TYPE_RENT);
        $this->assertEquals('buy', Community::MARKETPLACE_TYPE_BUY);
        $this->assertEquals('both', Community::MARKETPLACE_TYPE_BOTH);
    }

    public function test_community_statuses_method_returns_all_statuses(): void
    {
        $statuses = Community::statuses();

        $this->assertIsArray($statuses);
        $this->assertCount(2, $statuses);
        $this->assertContains('active', $statuses);
        $this->assertContains('inactive', $statuses);
    }

    public function test_community_marketplace_types_method_returns_all_types(): void
    {
        $types = Community::marketplaceTypes();

        $this->assertIsArray($types);
        $this->assertCount(3, $types);
        $this->assertContains('rent', $types);
        $this->assertContains('buy', $types);
        $this->assertContains('both', $types);
    }

    // ==========================================
    // Relationship Tests
    // ==========================================

    public function test_community_belongs_to_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $community = Community::factory()->forTenant($tenant)->create();

        $this->assertInstanceOf(Tenant::class, $community->tenant);
        $this->assertEquals($tenant->id, $community->tenant->id);
    }

    public function test_community_belongs_to_country(): void
    {
        $country = Country::factory()->create();
        $community = Community::factory()->create(['country_id' => $country->id]);

        $this->assertInstanceOf(Country::class, $community->country);
        $this->assertEquals($country->id, $community->country->id);
    }

    public function test_community_belongs_to_currency(): void
    {
        $currency = Currency::factory()->create();
        $community = Community::factory()->create(['currency_id' => $currency->id]);

        $this->assertInstanceOf(Currency::class, $community->currency);
        $this->assertEquals($currency->id, $community->currency->id);
    }

    public function test_community_belongs_to_city(): void
    {
        $city = City::factory()->create();
        $community = Community::factory()->inCity($city)->create();

        $this->assertInstanceOf(City::class, $community->city);
        $this->assertEquals($city->id, $community->city->id);
    }

    public function test_community_belongs_to_district(): void
    {
        $district = District::factory()->create();
        $community = Community::factory()->inDistrict($district)->create();

        $this->assertInstanceOf(District::class, $community->district);
        $this->assertEquals($district->id, $community->district->id);
    }

    public function test_community_can_have_null_district(): void
    {
        $community = Community::factory()->create(['district_id' => null]);

        $this->assertNull($community->district);
    }

    public function test_community_belongs_to_many_amenities(): void
    {
        $community = Community::factory()->create();
        $amenities = Amenity::factory()->count(3)->create();

        $community->amenities()->attach($amenities->pluck('id'));

        $this->assertCount(3, $community->amenities);
    }

    // ==========================================
    // Scope Tests
    // ==========================================

    public function test_community_active_scope(): void
    {
        Community::factory()->count(3)->create(['status' => 'active']);
        Community::factory()->count(2)->create(['status' => 'inactive']);

        $activeCommunities = Community::active()->get();

        $this->assertCount(3, $activeCommunities);
    }

    public function test_community_inactive_scope(): void
    {
        Community::factory()->count(3)->create(['status' => 'active']);
        Community::factory()->count(2)->create(['status' => 'inactive']);

        $inactiveCommunities = Community::inactive()->get();

        $this->assertCount(2, $inactiveCommunities);
    }

    public function test_community_for_tenant_scope(): void
    {
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();

        Community::factory()->count(3)->forTenant($tenant1)->create();
        Community::factory()->count(2)->forTenant($tenant2)->create();

        $tenant1Communities = Community::forTenant($tenant1)->get();
        $tenant2Communities = Community::forTenant($tenant2->id)->get();

        $this->assertCount(3, $tenant1Communities);
        $this->assertCount(2, $tenant2Communities);
    }

    public function test_community_for_tenant_scope_with_null_returns_unscoped_results(): void
    {
        Community::factory()->count(3)->create();

        $communities = Community::forTenant(null)->get();

        $this->assertCount(3, $communities);
    }

    public function test_community_marketplace_scope(): void
    {
        Community::factory()->count(3)->marketplace()->create();
        Community::factory()->count(2)->create(['is_marketplace' => false]);

        $marketplaceCommunities = Community::marketplace()->get();

        $this->assertCount(3, $marketplaceCommunities);
    }

    public function test_community_in_city_scope(): void
    {
        $city1 = City::factory()->create();
        $city2 = City::factory()->create();

        Community::factory()->count(3)->inCity($city1)->create();
        Community::factory()->count(2)->inCity($city2)->create();

        $city1Communities = Community::inCity($city1)->get();
        $city2Communities = Community::inCity($city2->id)->get();

        $this->assertCount(3, $city1Communities);
        $this->assertCount(2, $city2Communities);
    }

    public function test_community_in_district_scope(): void
    {
        $district1 = District::factory()->create();
        $district2 = District::factory()->create();

        Community::factory()->count(3)->inDistrict($district1)->create();
        Community::factory()->count(2)->inDistrict($district2)->create();

        $district1Communities = Community::inDistrict($district1)->get();
        $district2Communities = Community::inDistrict($district2->id)->get();

        $this->assertCount(3, $district1Communities);
        $this->assertCount(2, $district2Communities);
    }

    // ==========================================
    // Helper Method Tests
    // ==========================================

    public function test_community_is_active_method(): void
    {
        $activeCommunity = Community::factory()->active()->create();
        $inactiveCommunity = Community::factory()->inactive()->create();

        $this->assertTrue($activeCommunity->isActive());
        $this->assertFalse($inactiveCommunity->isActive());
    }

    public function test_community_is_inactive_method(): void
    {
        $activeCommunity = Community::factory()->active()->create();
        $inactiveCommunity = Community::factory()->inactive()->create();

        $this->assertFalse($activeCommunity->isInactive());
        $this->assertTrue($inactiveCommunity->isInactive());
    }

    public function test_community_is_on_marketplace_method(): void
    {
        $marketplaceCommunity = Community::factory()->marketplace()->create();
        $regularCommunity = Community::factory()->create(['is_marketplace' => false]);

        $this->assertTrue($marketplaceCommunity->isOnMarketplace());
        $this->assertFalse($regularCommunity->isOnMarketplace());
    }

    public function test_community_activate_method(): void
    {
        $community = Community::factory()->inactive()->create();

        $this->assertTrue($community->isInactive());

        $community->activate();

        $this->assertTrue($community->isActive());
    }

    public function test_community_deactivate_method(): void
    {
        $community = Community::factory()->active()->create();

        $this->assertTrue($community->isActive());

        $community->deactivate();

        $this->assertTrue($community->isInactive());
    }

    // ==========================================
    // Soft Delete Tests
    // ==========================================

    public function test_community_uses_soft_deletes(): void
    {
        $community = Community::factory()->create();

        $community->delete();

        $this->assertSoftDeleted('communities', ['id' => $community->id]);
        $this->assertNull(Community::find($community->id));
        $this->assertNotNull(Community::withTrashed()->find($community->id));
    }

    public function test_community_can_be_restored(): void
    {
        $community = Community::factory()->create();
        $community->delete();

        $this->assertSoftDeleted('communities', ['id' => $community->id]);

        $community->restore();

        $this->assertNotSoftDeleted('communities', ['id' => $community->id]);
        $this->assertNotNull(Community::find($community->id));
    }

    // ==========================================
    // Factory State Tests
    // ==========================================

    public function test_factory_creates_valid_community(): void
    {
        $community = Community::factory()->create();

        $this->assertNotNull($community->id);
        $this->assertNotEmpty($community->name);
        $this->assertNotNull($community->tenant_id);
        $this->assertNotNull($community->country_id);
        $this->assertNotNull($community->currency_id);
        $this->assertNotNull($community->city_id);
        $this->assertEquals('active', $community->status);
    }

    public function test_factory_active_state(): void
    {
        $community = Community::factory()->active()->create();

        $this->assertEquals('active', $community->status);
    }

    public function test_factory_inactive_state(): void
    {
        $community = Community::factory()->inactive()->create();

        $this->assertEquals('inactive', $community->status);
    }

    public function test_factory_for_tenant_state(): void
    {
        $tenant = Tenant::factory()->create();
        $community = Community::factory()->forTenant($tenant)->create();

        $this->assertEquals($tenant->id, $community->tenant_id);
    }

    public function test_factory_marketplace_state(): void
    {
        $community = Community::factory()->marketplace()->create();

        $this->assertTrue($community->is_marketplace);
    }

    public function test_factory_for_buy_state(): void
    {
        $community = Community::factory()->forBuy()->create();

        $this->assertTrue($community->is_marketplace);
        $this->assertTrue($community->is_buy);
        $this->assertEquals('buy', $community->marketplace_type);
    }

    public function test_factory_for_rent_state(): void
    {
        $community = Community::factory()->forRent()->create();

        $this->assertTrue($community->is_marketplace);
        $this->assertEquals('rent', $community->marketplace_type);
    }

    public function test_factory_for_rent_and_buy_state(): void
    {
        $community = Community::factory()->forRentAndBuy()->create();

        $this->assertTrue($community->is_marketplace);
        $this->assertTrue($community->is_buy);
        $this->assertEquals('both', $community->marketplace_type);
    }

    public function test_factory_off_plan_sale_state(): void
    {
        $community = Community::factory()->offPlanSale()->create();

        $this->assertTrue($community->is_marketplace);
        $this->assertTrue($community->is_off_plan_sale);
    }

    public function test_factory_in_city_state(): void
    {
        $city = City::factory()->create();
        $community = Community::factory()->inCity($city)->create();

        $this->assertEquals($city->id, $community->city_id);
    }

    public function test_factory_in_district_state(): void
    {
        $district = District::factory()->create();
        $community = Community::factory()->inDistrict($district)->create();

        $this->assertEquals($district->id, $community->district_id);
    }

    public function test_factory_with_commission_state(): void
    {
        $community = Community::factory()->withCommission(7.50, 3.25)->create();

        $this->assertEquals('7.50', $community->sales_commission_rate);
        $this->assertEquals('3.25', $community->rental_commission_rate);
    }

    public function test_factory_with_map_state(): void
    {
        $community = Community::factory()->withMap(24.7136, 46.6753)->create();

        $this->assertEquals(24.7136, $community->map['latitude']);
        $this->assertEquals(46.6753, $community->map['longitude']);
    }

    // ==========================================
    // Multi-tenant Isolation Tests
    // ==========================================

    public function test_communities_are_tenant_isolated(): void
    {
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();

        $community1 = Community::factory()->forTenant($tenant1)->create(['name' => 'Tenant 1 Community']);
        $community2 = Community::factory()->forTenant($tenant2)->create(['name' => 'Tenant 2 Community']);

        $tenant1Communities = Community::forTenant($tenant1)->get();
        $tenant2Communities = Community::forTenant($tenant2)->get();

        $this->assertCount(1, $tenant1Communities);
        $this->assertCount(1, $tenant2Communities);
        $this->assertEquals('Tenant 1 Community', $tenant1Communities->first()->name);
        $this->assertEquals('Tenant 2 Community', $tenant2Communities->first()->name);
    }

    // ==========================================
    // Cascade Delete Tests
    // ==========================================

    public function test_community_amenities_are_detached_on_delete(): void
    {
        $community = Community::factory()->create();
        $amenities = Amenity::factory()->count(3)->create();
        $community->amenities()->attach($amenities->pluck('id'));

        $this->assertCount(3, $community->amenities);

        $community->forceDelete();

        $this->assertDatabaseMissing('community_amenities', [
            'community_id' => $community->id,
        ]);
    }
}
