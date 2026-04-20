<?php

namespace Tests\Unit;

use App\Enums\AdminRole;
use App\Enums\Gender;
use App\Enums\LeaseEscalationType;
use App\Enums\MarketplaceType;
use App\Enums\RentalType;
use App\Enums\TenantType;
use PHPUnit\Framework\TestCase;

class TraitsTest extends TestCase
{
    public function test_tenant_type_enum_has_expected_cases(): void
    {
        $this->assertEquals('individual', TenantType::Individual->value);
        $this->assertEquals('company', TenantType::Company->value);
        $this->assertCount(2, TenantType::cases());
    }

    public function test_rental_type_enum_has_expected_cases(): void
    {
        $this->assertEquals('total', RentalType::Total->value);
        $this->assertEquals('detailed', RentalType::Detailed->value);
        $this->assertCount(2, RentalType::cases());
    }

    public function test_gender_enum_has_expected_cases(): void
    {
        $this->assertEquals('male', Gender::Male->value);
        $this->assertEquals('female', Gender::Female->value);
        $this->assertCount(2, Gender::cases());
    }

    public function test_marketplace_type_enum_has_expected_cases(): void
    {
        $this->assertEquals('rent', MarketplaceType::Rent->value);
        $this->assertEquals('sale', MarketplaceType::Sale->value);
        $this->assertEquals('both', MarketplaceType::Both->value);
        $this->assertCount(3, MarketplaceType::cases());
    }

    public function test_lease_escalation_type_enum_has_expected_cases(): void
    {
        $this->assertEquals('fixed', LeaseEscalationType::Fixed->value);
        $this->assertEquals('percentage', LeaseEscalationType::Percentage->value);
        $this->assertCount(2, LeaseEscalationType::cases());
    }

    public function test_admin_role_enum_has_expected_cases(): void
    {
        $this->assertEquals('Admins', AdminRole::Admins->value);
        $this->assertEquals('accountingManagers', AdminRole::AccountingManagers->value);
        $this->assertEquals('serviceManagers', AdminRole::ServiceManagers->value);
        $this->assertEquals('marketingManagers', AdminRole::MarketingManagers->value);
        $this->assertEquals('salesAndLeasingManagers', AdminRole::SalesAndLeasingManagers->value);
        $this->assertCount(5, AdminRole::cases());
    }
}
