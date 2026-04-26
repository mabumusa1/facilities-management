<?php

namespace Tests\Feature\Feature\Admin;

use App\Models\OwnerRegistration;
use App\Models\Tenant;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminDataModelTest extends TestCase
{
    public function test_account_subscriptions_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('account_subscriptions'));
    }

    public function test_subscription_plans_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('subscription_plans'));
    }

    public function test_owner_registrations_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('rf_owner_registrations'));
        $this->assertTrue(Schema::hasColumns('rf_owner_registrations', [
            'id', 'account_tenant_id', 'first_name', 'last_name', 'email',
            'phone_number', 'status', 'submitted_data', 'reviewed_by', 'review_notes',
        ]));
    }

    public function test_lead_model_exists_with_required_columns(): void
    {
        $this->assertTrue(Schema::hasTable('rf_leads'));
        $this->assertTrue(Schema::hasColumns('rf_leads', [
            'id', 'name', 'first_name', 'last_name', 'phone_number', 'email',
            'source_id', 'status_id', 'priority_id', 'lead_owner_id',
        ]));
    }

    public function test_lead_source_model_exists(): void
    {
        $this->assertTrue(Schema::hasTable('rf_lead_sources'));
    }

    public function test_owner_registration_can_be_created(): void
    {
        $tenant = Tenant::create(['name' => 'Test Owner Reg']);
        $tenant->makeCurrent();

        $reg = OwnerRegistration::create([
            'account_tenant_id' => $tenant->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone_number' => '1234567890',
            'status' => 'pending',
        ]);

        $this->assertSame('pending', $reg->status);
        $this->assertDatabaseHas('rf_owner_registrations', ['id' => $reg->id]);
    }
}
