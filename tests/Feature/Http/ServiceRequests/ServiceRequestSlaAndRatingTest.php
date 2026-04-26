<?php

namespace Tests\Feature\Http\ServiceRequests;

use App\Models\AccountMembership;
use App\Models\Request as ServiceRequest;
use App\Models\Tenant;
use App\Models\User;
use DB;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ServiceRequestSlaAndRatingTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenant;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->tenant = Tenant::create(['name' => 'SR QA Test']);
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

    public function test_rate_endpoint_stores_rating_and_feedback(): void
    {
        $sr = ServiceRequest::factory()->create([
            'account_tenant_id' => $this->tenant->id,
        ]);

        $response = $this->postJson("/rf/requests/{$sr->id}/rate", [
            'rating' => 4,
            'feedback' => 'Good service, quick response.',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.rating', 4);

        $this->assertDatabaseHas('rf_requests', [
            'id' => $sr->id,
            'rating' => 4,
            'feedback' => 'Good service, quick response.',
        ]);
    }

    public function test_rate_validates_rating_range(): void
    {
        $sr = ServiceRequest::factory()->create(['account_tenant_id' => $this->tenant->id]);

        $response = $this->postJson("/rf/requests/{$sr->id}/rate", ['rating' => 6]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['rating']);
    }

    public function test_rate_minimum_rating(): void
    {
        $sr = ServiceRequest::factory()->create(['account_tenant_id' => $this->tenant->id]);

        $response = $this->postJson("/rf/requests/{$sr->id}/rate", ['rating' => 1]);
        $response->assertStatus(200);
    }

    public function test_maximum_feedback_length(): void
    {
        $sr = ServiceRequest::factory()->create(['account_tenant_id' => $this->tenant->id]);

        $response = $this->postJson("/rf/requests/{$sr->id}/rate", [
            'rating' => 3,
            'feedback' => str_repeat('x', 1001),
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['feedback']);
    }

    public function test_check_sla_for_request(): void
    {
        $sr = ServiceRequest::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'sla_response_due_at' => now()->subHour(),
            'sla_breach_response' => false,
        ]);

        $response = $this->getJson("/rf/requests/{$sr->id}/check-sla");

        $response->assertStatus(200);
        $this->assertDatabaseHas('rf_requests', [
            'id' => $sr->id,
            'sla_breach_response' => true,
        ]);
    }

    public function test_sla_not_breached_if_within_window(): void
    {
        $sr = ServiceRequest::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'sla_response_due_at' => now()->addHour(),
            'sla_breach_response' => false,
        ]);

        $response = $this->getJson("/rf/requests/{$sr->id}/check-sla");
        $response->assertStatus(200);

        $this->assertDatabaseHas('rf_requests', [
            'id' => $sr->id,
            'sla_breach_response' => false,
        ]);
    }
}
