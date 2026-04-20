<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\FacilityCategory;
use App\Models\FeaturedService;
use App\Models\Request;
use App\Models\RequestCategory;
use App\Models\RequestSubcategory;
use App\Models\Resident;
use App\Models\ServiceSetting;
use App\Models\WorkingDay;
use Database\Seeders\FacilityCategorySeeder;
use Database\Seeders\RequestCategorySeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class RequestServiceTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_request_category_seeder_creates_records(): void
    {
        $this->seed(RequestCategorySeeder::class);

        $this->assertDatabaseCount('rf_request_categories', 8);
    }

    public function test_facility_category_seeder_creates_records(): void
    {
        $this->seed(FacilityCategorySeeder::class);

        $this->assertDatabaseCount('rf_facility_categories', 6);
    }

    public function test_request_category_has_subcategories(): void
    {
        $category = RequestCategory::factory()->create(['has_sub_categories' => true]);
        RequestSubcategory::factory()->count(3)->create(['category_id' => $category->id]);

        $this->assertCount(3, $category->subcategories);
    }

    public function test_subcategory_has_working_days(): void
    {
        $subcategory = RequestSubcategory::factory()->create();
        WorkingDay::factory()->count(5)->create(['subcategory_id' => $subcategory->id]);

        $this->assertCount(5, $subcategory->workingDays);
    }

    public function test_subcategory_has_featured_services(): void
    {
        $subcategory = RequestSubcategory::factory()->create();
        FeaturedService::factory()->count(2)->create(['subcategory_id' => $subcategory->id]);

        $this->assertCount(2, $subcategory->featuredServices);
    }

    public function test_request_factory_creates_valid_model(): void
    {
        $request = Request::factory()->create();

        $this->assertModelExists($request);
        $this->assertNotNull($request->category);
        $this->assertNotNull($request->status);
        $this->assertInstanceOf(Resident::class, $request->requester);
    }

    public function test_request_polymorphic_requester(): void
    {
        $resident = Resident::factory()->create();
        $request = Request::factory()->create([
            'requester_type' => Resident::class,
            'requester_id' => $resident->id,
        ]);

        $this->assertTrue($request->requester->is($resident));
    }

    public function test_request_soft_deletes(): void
    {
        $request = Request::factory()->create();
        $request->delete();

        $this->assertSoftDeleted($request);
    }

    public function test_service_setting_belongs_to_category(): void
    {
        $setting = ServiceSetting::factory()->create();

        $this->assertNotNull($setting->category);
        $this->assertInstanceOf(RequestCategory::class, $setting->category);
    }

    public function test_service_setting_json_casts(): void
    {
        $setting = ServiceSetting::factory()->create([
            'visibilities' => ['tenant', 'owner'],
            'permissions' => ['create'],
        ]);

        $setting->refresh();

        $this->assertIsArray($setting->visibilities);
        $this->assertContains('tenant', $setting->visibilities);
    }

    public function test_facility_factory_creates_valid_model(): void
    {
        $facility = Facility::factory()->create();

        $this->assertModelExists($facility);
        $this->assertNotNull($facility->category);
        $this->assertInstanceOf(FacilityCategory::class, $facility->category);
    }

    public function test_facility_has_bookings(): void
    {
        $facility = Facility::factory()->create();
        FacilityBooking::factory()->count(3)->create(['facility_id' => $facility->id]);

        $this->assertCount(3, $facility->bookings);
    }

    public function test_facility_booking_polymorphic_booker(): void
    {
        $resident = Resident::factory()->create();
        $booking = FacilityBooking::factory()->create([
            'booker_type' => Resident::class,
            'booker_id' => $resident->id,
        ]);

        $this->assertTrue($booking->booker->is($resident));
    }

    public function test_facility_soft_deletes(): void
    {
        $facility = Facility::factory()->create();
        $facility->delete();

        $this->assertSoftDeleted($facility);
    }

    public function test_facility_booking_soft_deletes(): void
    {
        $booking = FacilityBooking::factory()->create();
        $booking->delete();

        $this->assertSoftDeleted($booking);
    }

    public function test_announcement_factory_creates_valid_model(): void
    {
        $announcement = Announcement::factory()->create();

        $this->assertModelExists($announcement);
        $this->assertNotNull($announcement->community);
    }

    public function test_announcement_soft_deletes(): void
    {
        $announcement = Announcement::factory()->create();
        $announcement->delete();

        $this->assertSoftDeleted($announcement);
    }

    public function test_announcement_boolean_casts(): void
    {
        $announcement = Announcement::factory()->create(['is_published' => true]);
        $announcement->refresh();

        $this->assertTrue($announcement->is_published);
    }

    public function test_category_bilingual_name(): void
    {
        $category = RequestCategory::factory()->create([
            'name_ar' => 'صيانة',
            'name_en' => 'Maintenance',
        ]);

        app()->setLocale('ar');
        $this->assertEquals('صيانة', $category->name);

        app()->setLocale('en');
        $this->assertEquals('Maintenance', $category->name);
    }
}
