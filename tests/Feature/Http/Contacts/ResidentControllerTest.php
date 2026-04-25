<?php

namespace Tests\Feature\Http\Contacts;

use App\Enums\IdType;
use App\Enums\RolesEnum;
use App\Models\AccountMembership;
use App\Models\Resident;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class ResidentControllerTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(RbacSeeder::class);

        $this->tenant = Tenant::create(['name' => 'Resident HTTP Test']);
        $this->tenant->makeCurrent();
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    private function adminActingAs(?Tenant $tenant = null): User
    {
        $tenant ??= $this->tenant;

        $user = User::factory()->create();
        $user->assignRole(RolesEnum::ACCOUNT_ADMINS->value);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::ACCOUNT_ADMINS->value,
        ]);

        $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id]);

        return $user;
    }

    private function tenantPost(string $url, array $payload, User $user, ?Tenant $tenant = null): TestResponse
    {
        $tenant ??= $this->tenant;

        return $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post($url, $payload);
    }

    private function tenantGet(string $url, User $user, ?Tenant $tenant = null): TestResponse
    {
        $tenant ??= $this->tenant;

        return $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->get($url);
    }

    // ── Happy path ───────────────────────────────────────────────────────────

    public function test_admin_can_create_resident_with_bilingual_fields(): void
    {
        $user = $this->adminActingAs();

        $payload = [
            'first_name' => 'Ahmed',
            'last_name' => 'Al-Rashid',
            'first_name_ar' => 'أحمد',
            'last_name_ar' => 'الراشد',
            'email' => 'ahmed@example.com',
            'phone_country_code' => 'SA',
            'phone_number' => '512345678',
            'national_id' => '1234567890',
            'id_type' => IdType::NationalId->value,
        ];

        $response = $this->tenantPost('/residents', $payload, $user);

        $response->assertSessionHasNoErrors();

        $resident = Resident::query()->where('email', 'ahmed@example.com')->first();
        $this->assertNotNull($resident);
        $response->assertRedirect("/residents/{$resident->id}");

        $this->assertSame('Ahmed', $resident->first_name);
        $this->assertSame('أحمد', $resident->first_name_ar);
        $this->assertSame(IdType::NationalId, $resident->id_type);
        $this->assertSame($this->tenant->id, $resident->account_tenant_id);
        $this->assertSame('966512345678', $resident->national_phone_number);
    }

    // ── Duplicate detected, blocked without force_create ────────────────────

    public function test_create_is_blocked_when_phone_duplicates_existing_resident(): void
    {
        $user = $this->adminActingAs();

        Resident::factory()->withPhone('966512345678')->create([
            'first_name' => 'Existing',
            'last_name' => 'Resident',
            'phone_country_code' => 'SA',
            'phone_number' => '512345678',
        ]);

        $payload = [
            'first_name' => 'Different',
            'last_name' => 'Person',
            'phone_country_code' => 'SA',
            'phone_number' => '512345678',
        ];

        $response = $this->tenantPost('/residents', $payload, $user);

        $response->assertSessionHasErrors('phone_number');
        $this->assertSame(1, Resident::query()->count());
    }

    // ── Duplicate overridden via force_create ───────────────────────────────

    public function test_create_succeeds_with_force_create_when_duplicate_exists(): void
    {
        $user = $this->adminActingAs();

        Resident::factory()->withPhone('966512345678')->create([
            'first_name' => 'Existing',
            'last_name' => 'Resident',
            'phone_country_code' => 'SA',
            'phone_number' => '512345678',
        ]);

        $payload = [
            'first_name' => 'Different',
            'last_name' => 'Person',
            'phone_country_code' => 'SA',
            'phone_number' => '512345678',
            'force_create' => true,
        ];

        $response = $this->tenantPost('/residents', $payload, $user);

        $response->assertSessionHasNoErrors();
        $this->assertSame(2, Resident::query()->count());
    }

    // ── Validation errors ──────────────────────────────────────────────────

    public function test_create_requires_a_phone_number(): void
    {
        $user = $this->adminActingAs();

        $response = $this->tenantPost('/residents', [
            'first_name' => 'Ahmed',
            'last_name' => 'Al-Rashid',
            'phone_country_code' => 'SA',
        ], $user);

        $response->assertSessionHasErrors('phone_number');
    }

    public function test_create_rejects_invalid_phone_format(): void
    {
        $user = $this->adminActingAs();

        $response = $this->tenantPost('/residents', [
            'first_name' => 'Ahmed',
            'last_name' => 'Al-Rashid',
            'phone_country_code' => 'SA',
            'phone_number' => 'abc123!@#',
        ], $user);

        $response->assertSessionHasErrors('phone_number');
    }

    public function test_create_accepts_arabic_only_name(): void
    {
        $user = $this->adminActingAs();

        $payload = [
            'first_name_ar' => 'محمد',
            'last_name_ar' => 'العمري',
            'phone_country_code' => 'SA',
            'phone_number' => '566778899',
        ];

        $response = $this->tenantPost('/residents', $payload, $user);

        $response->assertSessionHasNoErrors();

        $resident = Resident::query()->where('first_name_ar', 'محمد')->first();
        $this->assertNotNull($resident);
        $this->assertNull($resident->first_name);
        $this->assertSame('العمري', $resident->last_name_ar);
    }

    // ── Search ─────────────────────────────────────────────────────────────

    public function test_index_search_matches_english_name_fragment(): void
    {
        $user = $this->adminActingAs();

        Resident::factory()->create(['first_name' => 'Ahmed', 'last_name' => 'Al-Rashid']);
        Resident::factory()->create(['first_name' => 'Sara', 'last_name' => 'Maher']);

        $response = $this->tenantGet('/residents?search=ahmed', $user);

        $response->assertOk();
        $payload = $response->viewData('page')['props']['residents']['data'];
        $this->assertCount(1, $payload);
        $this->assertSame('Ahmed', $payload[0]['first_name']);
    }

    public function test_index_search_matches_arabic_name_fragment(): void
    {
        $user = $this->adminActingAs();

        Resident::factory()->create([
            'first_name' => 'Ahmed',
            'last_name' => 'Al-Rashid',
            'first_name_ar' => 'أحمد',
            'last_name_ar' => 'الراشد',
        ]);
        Resident::factory()->create([
            'first_name' => 'Sara',
            'last_name' => 'Maher',
            'first_name_ar' => 'سارة',
            'last_name_ar' => 'ماهر',
        ]);

        $response = $this->tenantGet('/residents?search=أحمد', $user);

        $response->assertOk();
        $payload = $response->viewData('page')['props']['residents']['data'];
        $this->assertCount(1, $payload);
        $this->assertSame('أحمد', $payload[0]['first_name_ar']);
    }

    // ── Duplicate-check endpoint ───────────────────────────────────────────

    public function test_duplicate_check_returns_match_for_existing_phone(): void
    {
        $user = $this->adminActingAs();

        $existing = Resident::factory()->withPhone('966512345678')->create([
            'first_name' => 'Existing',
            'last_name' => 'Resident',
        ]);

        $response = $this->tenantGet(
            '/residents/duplicate-check?phone_country_code=SA&phone_number=512345678',
            $user,
        );

        $response->assertOk();
        $response->assertJsonPath('duplicate', true);
        $response->assertJsonPath('match.id', $existing->id);
        $response->assertJsonPath('match.name', 'Existing Resident');
    }

    public function test_duplicate_check_returns_false_for_unknown_phone(): void
    {
        $user = $this->adminActingAs();

        Resident::factory()->withPhone('966512345678')->create();

        $response = $this->tenantGet(
            '/residents/duplicate-check?phone_country_code=SA&phone_number=599999999',
            $user,
        );

        $response->assertOk();
        $response->assertExactJson(['duplicate' => false]);
    }

    public function test_duplicate_check_normalizes_leading_zero(): void
    {
        $user = $this->adminActingAs();

        $existing = Resident::factory()->withPhone('966512345678')->create();

        $response = $this->tenantGet(
            '/residents/duplicate-check?phone_country_code=SA&phone_number=0512345678',
            $user,
        );

        $response->assertOk();
        $response->assertJsonPath('duplicate', true);
        $response->assertJsonPath('match.id', $existing->id);
    }

    // ── Authorization ──────────────────────────────────────────────────────

    public function test_user_without_view_permission_cannot_access_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::PROFESSIONALS->value);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => RolesEnum::PROFESSIONALS->value,
        ]);

        $this->tenantGet('/residents', $user)->assertForbidden();
    }

    public function test_user_without_create_permission_cannot_post(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::PROFESSIONALS->value);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => RolesEnum::PROFESSIONALS->value,
        ]);

        $payload = [
            'first_name' => 'Blocked',
            'last_name' => 'User',
            'phone_country_code' => 'SA',
            'phone_number' => '512345670',
        ];

        $response = $this->tenantPost('/residents', $payload, $user);
        $response->assertForbidden();
        $this->assertSame(0, Resident::query()->count());
    }

    // ── Tenant isolation ────────────────────────────────────────────────────

    public function test_duplicate_check_does_not_match_across_tenants(): void
    {
        Resident::factory()->withPhone('966512345678')->create();

        $tenantB = Tenant::create(['name' => 'Tenant B']);
        $tenantB->makeCurrent();

        $userB = $this->adminActingAs($tenantB);

        $response = $this->tenantGet(
            '/residents/duplicate-check?phone_country_code=SA&phone_number=512345678',
            $userB,
            $tenantB,
        );

        $response->assertOk();
        $response->assertExactJson(['duplicate' => false]);
    }

    public function test_index_does_not_show_residents_from_other_tenants(): void
    {
        Resident::factory()->create(['first_name' => 'TenantA Only']);

        $tenantB = Tenant::create(['name' => 'Tenant B']);
        $tenantB->makeCurrent();

        Resident::factory()->create(['first_name' => 'TenantB Only']);

        $userB = $this->adminActingAs($tenantB);

        $response = $this->tenantGet('/residents', $userB, $tenantB);
        $response->assertOk();

        $payload = $response->viewData('page')['props']['residents']['data'];
        $names = array_column($payload, 'first_name');
        $this->assertContains('TenantB Only', $names);
        $this->assertNotContains('TenantA Only', $names);
    }
}
