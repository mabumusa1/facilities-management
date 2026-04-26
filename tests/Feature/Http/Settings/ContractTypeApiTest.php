<?php

namespace Tests\Feature\Http\Settings;

use App\Models\AccountMembership;
use App\Models\ContractType;
use App\Models\Tenant;
use App\Models\User;
use DB;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ContractTypeApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->tenant = Tenant::create(['name' => 'CT Test']);
        $this->tenant->makeCurrent();

        AccountMembership::create([
            'user_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);

        $this->ensureAccountAdminsRoleExists();
        $this->user->assignRole('accountAdmins');

        $this->actingAs($this->user);
        $this->withSession(['tenant_id' => $this->tenant->id]);
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
                'name' => 'accountAdmins',
                'guard_name' => 'web',
                'name_en' => 'Account Admins',
                'name_ar' => 'مدراء الحسابات',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function test_index_returns_contract_types(): void
    {
        ContractType::create(['name_en' => 'Yearly', 'name_ar' => 'سنوي', 'is_active' => true, 'sort_order' => 1]);
        ContractType::create(['name_en' => 'Monthly', 'name_ar' => 'شهري', 'is_active' => false, 'sort_order' => 2]);

        $response = $this->getJson('/rf/contract-types');

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('data'));
    }

    public function test_store_creates_contract_type(): void
    {
        $response = $this->postJson('/rf/contract-types', [
            'name_en' => 'Commercial Lease',
            'name_ar' => 'عقد تجاري',
            'default_payment_terms_days' => 30,
            'is_active' => true,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('rf_contract_types', [
            'name_en' => 'Commercial Lease',
            'name_ar' => 'عقد تجاري',
            'is_active' => true,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->postJson('/rf/contract-types', [
            'is_active' => true,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name_en', 'name_ar']);
    }

    public function test_update_updates_contract_type(): void
    {
        $ct = ContractType::create(['name_en' => 'Old', 'name_ar' => 'قديم', 'is_active' => true]);

        $response = $this->putJson("/rf/contract-types/{$ct->id}", [
            'name_en' => 'Updated',
            'name_ar' => 'محدث',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('rf_contract_types', ['id' => $ct->id, 'name_en' => 'Updated']);
    }

    public function test_destroy_deletes_contract_type(): void
    {
        $ct = ContractType::create(['name_en' => 'To Delete', 'name_ar' => 'للحذف', 'is_active' => true]);

        $response = $this->deleteJson("/rf/contract-types/{$ct->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('rf_contract_types', ['id' => $ct->id]);
    }
}
