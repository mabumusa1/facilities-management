<?php

namespace Tests\Feature;

use App\Enums\RolesEnum;
use App\Models\AccountMembership;
use App\Models\Building;
use App\Models\Community;
use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\Lease;
use App\Models\MarketplaceOffer;
use App\Models\MarketplaceUnit;
use App\Models\MarketplaceVisit;
use App\Models\Owner;
use App\Models\Payment;
use App\Models\Request as ServiceRequest;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\DemoAccountSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DemoAccountSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_demo_account_seeder_creates_a_feature_rich_showcase_dataset(): void
    {
        $this->seed(DemoAccountSeeder::class);

        $tenant = Tenant::query()->where('name', 'Demo Account')->first();

        $this->assertNotNull($tenant);

        $accountAdmin = User::query()->where('email', 'test@example.com')->first();

        $this->assertNotNull($accountAdmin);
        $this->assertTrue($accountAdmin->hasRole(RolesEnum::ACCOUNT_ADMINS));

        $this->assertDatabaseHas('account_memberships', [
            'user_id' => $accountAdmin->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::ACCOUNT_ADMINS->value,
        ]);

        $this->assertGreaterThanOrEqual(5, AccountMembership::query()->count());

        $tenant->makeCurrent();

        $this->assertGreaterThanOrEqual(5, Community::query()->count());
        $this->assertGreaterThanOrEqual(12, Building::query()->count());
        $this->assertGreaterThanOrEqual(45, Unit::query()->count());
        $this->assertGreaterThanOrEqual(20, Lease::query()->count());
        $this->assertGreaterThanOrEqual(60, Transaction::query()->count());
        $this->assertGreaterThanOrEqual(35, ServiceRequest::query()->count());
        $this->assertGreaterThanOrEqual(10, Facility::query()->count());
        $this->assertGreaterThanOrEqual(30, FacilityBooking::query()->count());
        $this->assertGreaterThanOrEqual(12, MarketplaceUnit::query()->count());
        $this->assertGreaterThanOrEqual(10, MarketplaceOffer::query()->count());
        $this->assertGreaterThanOrEqual(15, MarketplaceVisit::query()->count());
        $this->assertGreaterThanOrEqual(20, Payment::query()->count());

        $this->assertSame(0, Community::query()->whereDoesntHave('amenities')->count());
        $this->assertGreaterThan(0, Unit::query()->whereHas('features')->count());
        $this->assertSame(0, Owner::query()->doesntHave('units')->count());

        $this->assertGreaterThan(0, ServiceRequest::query()->whereNotNull('professional_id')->count());
    }
}
