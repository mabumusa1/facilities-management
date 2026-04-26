<?php

namespace Tests\Feature\Http\Facilities;

use App\Models\AccountMembership;
use App\Models\Community;
use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\FacilityWaitlist;
use App\Models\Resident;
use App\Models\Tenant;
use App\Models\User;
use DB;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class BookingManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    private Facility $facility;

    private Resident $resident;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->tenant = Tenant::create(['name' => 'Fac Test']);
        $this->tenant->makeCurrent();

        AccountMembership::create([
            'user_id' => $this->user->id, 'account_tenant_id' => $this->tenant->id, 'role' => 'account_admins',
        ]);

        $this->ensureAccountAdminsRoleExists();
        $this->user->assignRole('accountAdmins');

        $this->actingAs($this->user);
        $this->withSession(['tenant_id' => $this->tenant->id]);

        $this->resident = Resident::factory()->create(['account_tenant_id' => $this->tenant->id]);

        $community = Community::factory()->create(['account_tenant_id' => $this->tenant->id]);
        $this->facility = Facility::factory()->create([
            'community_id' => $community->id,
            'account_tenant_id' => $this->tenant->id,
        ]);
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    private function ensureAccountAdminsRoleExists(): void
    {
        if (! DB::table('roles')->where('name', 'accountAdmins')->where('guard_name', 'web')->exists()) {
            DB::table('roles')->insert([
                'name' => 'accountAdmins', 'guard_name' => 'web',
                'name_en' => 'Account Admins', 'name_ar' => 'مدراء الحسابات',
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    private function createBooking(array $overrides = []): FacilityBooking
    {
        return FacilityBooking::create(array_merge([
            'facility_id' => $this->facility->id,
            'account_tenant_id' => $this->tenant->id,
            'booker_type' => User::class,
            'booker_id' => $this->user->id,
            'booking_date' => now()->format('Y-m-d'),
            'start_time' => '10:00',
            'end_time' => '12:00',
        ], $overrides));
    }

    // -------------------------------------------------------------------------
    // Calendar
    // -------------------------------------------------------------------------

    public function test_calendar_returns_bookings_in_date_range(): void
    {
        $this->createBooking(['booking_date' => now()->format('Y-m-d')]);

        $response = $this->getJson('/rf/bookings/calendar?from='.now()->format('Y-m-d').'&to='.now()->addDay()->format('Y-m-d'));

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('data'));
    }

    public function test_calendar_validates_date_range(): void
    {
        $response = $this->getJson('/rf/bookings/calendar?from='.now()->format('Y-m-d').'&to='.now()->subDay()->format('Y-m-d'));
        $response->assertStatus(422);
    }

    // -------------------------------------------------------------------------
    // Check-in / Check-out
    // -------------------------------------------------------------------------

    public function test_check_in_records_arrival(): void
    {
        $booking = $this->createBooking();

        $response = $this->postJson("/rf/bookings/{$booking->id}/check-in");

        $response->assertStatus(200);
        $this->assertNotNull($booking->fresh()->checked_in_at);
    }

    public function test_double_check_in_is_prevented(): void
    {
        $booking = $this->createBooking(['checked_in_at' => now()]);

        $response = $this->postJson("/rf/bookings/{$booking->id}/check-in");
        $response->assertStatus(422);
    }

    public function test_check_out_records_departure(): void
    {
        $booking = $this->createBooking(['checked_in_at' => now()->subHour()]);

        $response = $this->postJson("/rf/bookings/{$booking->id}/check-out");

        $response->assertStatus(200);
        $this->assertNotNull($booking->fresh()->checked_out_at);
    }

    public function test_double_check_out_is_prevented(): void
    {
        $booking = $this->createBooking([
            'checked_in_at' => now()->subHours(2),
            'checked_out_at' => now()->subHour(),
        ]);

        $response = $this->postJson("/rf/bookings/{$booking->id}/check-out");
        $response->assertStatus(422);
    }

    // -------------------------------------------------------------------------
    // Waitlist
    // -------------------------------------------------------------------------

    public function test_join_waitlist(): void
    {
        $response = $this->postJson('/rf/waitlist/join', [
            'facility_id' => $this->facility->id,
            'date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '14:00',
            'end_time' => '16:00',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('rf_facility_waitlist', [
            'facility_id' => $this->facility->id,
        ]);
    }

    public function test_leave_waitlist(): void
    {
        $wl = FacilityWaitlist::create([
            'account_tenant_id' => $this->tenant->id,
            'facility_id' => $this->facility->id,
            'resident_id' => $this->user->id,
            'requested_start_at' => '14:00',
            'requested_end_at' => '16:00',
        ]);

        $response = $this->deleteJson('/rf/waitlist/leave', ['waitlist_id' => $wl->id]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('rf_facility_waitlist', ['id' => $wl->id]);
    }

    // -------------------------------------------------------------------------
    // Operational report
    // -------------------------------------------------------------------------

    public function test_operational_report_returns_stats(): void
    {
        $this->createBooking(['checked_in_at' => now(), 'booking_date' => now()->format('Y-m-d')]);

        $response = $this->getJson('/rf/facilities/report/operational?from='.now()->subWeek()->format('Y-m-d').'&to='.now()->format('Y-m-d'));

        $response->assertStatus(200);
        $response->assertJsonPath('data.total_bookings', 1);
        $response->assertJsonPath('data.checked_in', 1);
    }
}
