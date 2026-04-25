<?php

namespace Tests\Feature\Http\Contacts;

use App\Enums\IdType;
use App\Enums\RolesEnum;
use App\Models\AccountMembership;
use App\Models\Community;
use App\Models\Resident;
use App\Models\Tenant;
use App\Models\Unit;
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

    // ── QA: AC1 — show page loads all saved fields ──────────────────────────

    public function test_show_page_returns_saved_resident_data(): void
    {
        $user = $this->adminActingAs();

        $resident = Resident::factory()->create([
            'first_name' => 'Fatima',
            'last_name' => 'Al-Zahrani',
            'first_name_ar' => 'فاطمة',
            'last_name_ar' => 'الزهراني',
            'email' => 'fatima@example.com',
            'national_id' => 'ID98765',
        ]);

        $response = $this->tenantGet("/residents/{$resident->id}", $user);

        $response->assertOk();
        $props = $response->viewData('page')['props']['resident'];

        $this->assertSame('Fatima', $props['first_name']);
        $this->assertSame('Al-Zahrani', $props['last_name']);
        $this->assertSame('فاطمة', $props['first_name_ar']);
        $this->assertSame('الزهراني', $props['last_name_ar']);
        $this->assertSame('fatima@example.com', $props['email']);
        $this->assertSame('ID98765', $props['national_id']);
    }

    // ── QA: AC2 — duplicate check includes unit / building / community context ─

    public function test_duplicate_check_includes_unit_and_community_context_when_resident_has_unit(): void
    {
        $user = $this->adminActingAs();

        $community = Community::factory()->create(['name' => 'Palm Towers']);
        $unit = Unit::factory()->create([
            'name' => 'Unit 101',
            'rf_community_id' => $community->id,
            'rf_building_id' => null,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $existing = Resident::factory()->withPhone('966512345678')->create([
            'first_name' => 'Unit',
            'last_name' => 'Holder',
            'account_tenant_id' => $this->tenant->id,
        ]);
        // Associate the unit with the resident (Unit has a tenant_id FK → Resident)
        $unit->update(['tenant_id' => $existing->id]);

        $response = $this->tenantGet(
            '/residents/duplicate-check?phone_country_code=SA&phone_number=512345678',
            $user,
        );

        $response->assertOk();
        $response->assertJsonPath('duplicate', true);
        $response->assertJsonPath('match.unit', 'Unit 101');
        $response->assertJsonPath('match.community', 'Palm Towers');
        $response->assertJsonPath('match.building', null);
    }

    public function test_duplicate_check_returns_null_unit_context_when_resident_has_no_unit(): void
    {
        $user = $this->adminActingAs();

        Resident::factory()->withPhone('966512345678')->create([
            'first_name' => 'No',
            'last_name' => 'Unit',
        ]);

        $response = $this->tenantGet(
            '/residents/duplicate-check?phone_country_code=SA&phone_number=512345678',
            $user,
        );

        $response->assertOk();
        $response->assertJsonPath('duplicate', true);
        $response->assertJsonPath('match.unit', null);
        $response->assertJsonPath('match.building', null);
        $response->assertJsonPath('match.community', null);
    }

    // ── QA: AC2 — duplicate check requires both query params ─────────────────

    public function test_duplicate_check_returns_422_when_phone_country_code_missing(): void
    {
        $user = $this->adminActingAs();

        // The duplicateCheck endpoint always returns JsonResponse, so we send
        // an explicit Accept: application/json header to get 422 (not a redirect).
        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->withHeaders(['Accept' => 'application/json'])
            ->get('/residents/duplicate-check?phone_number=512345678');

        $response->assertUnprocessable();
    }

    public function test_duplicate_check_returns_422_when_phone_number_missing(): void
    {
        $user = $this->adminActingAs();

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->withHeaders(['Accept' => 'application/json'])
            ->get('/residents/duplicate-check?phone_country_code=SA');

        $response->assertUnprocessable();
    }

    // ── QA: AC2/AC3 — phone normalization edge cases ──────────────────────────

    public function test_duplicate_check_handles_unknown_country_code_without_crash(): void
    {
        $user = $this->adminActingAs();

        // An unknown country code means no dial prefix is prepended; the raw
        // digits are stored directly. The endpoint must return a safe response.
        $response = $this->tenantGet(
            '/residents/duplicate-check?phone_country_code=XX&phone_number=123456789',
            $user,
        );

        $response->assertOk();
        $response->assertJsonPath('duplicate', false);
    }

    public function test_create_stores_national_phone_number_for_unknown_country_code(): void
    {
        $user = $this->adminActingAs();

        $payload = [
            'first_name' => 'Global',
            'last_name' => 'User',
            'phone_country_code' => 'XX',
            'phone_number' => '123456789',
        ];

        $response = $this->tenantPost('/residents', $payload, $user);

        $response->assertSessionHasNoErrors();
        $resident = Resident::query()->where('first_name', 'Global')->first();
        $this->assertNotNull($resident);
        // Without a known dial code, the digits are stored as-is (no prefix)
        $this->assertSame('123456789', $resident->national_phone_number);
    }

    // ── QA: AC3 — validation failure paths ───────────────────────────────────

    public function test_create_rejects_phone_number_shorter_than_min_length(): void
    {
        $user = $this->adminActingAs();

        $response = $this->tenantPost('/residents', [
            'first_name' => 'Min',
            'last_name' => 'Test',
            'phone_country_code' => 'SA',
            'phone_number' => '123', // 3 digits — below min:5
        ], $user);

        $response->assertSessionHasErrors('phone_number');
        $this->assertSame(0, Resident::query()->count());
    }

    public function test_create_rejects_missing_phone_country_code(): void
    {
        $user = $this->adminActingAs();

        $response = $this->tenantPost('/residents', [
            'first_name' => 'No',
            'last_name' => 'Country',
            'phone_number' => '512345678',
        ], $user);

        $response->assertSessionHasErrors('phone_country_code');
        $this->assertSame(0, Resident::query()->count());
    }

    public function test_create_rejects_invalid_id_type_enum_value(): void
    {
        $user = $this->adminActingAs();

        $response = $this->tenantPost('/residents', [
            'first_name' => 'Bad',
            'last_name' => 'Enum',
            'phone_country_code' => 'SA',
            'phone_number' => '512345678',
            'id_type' => 'not_a_valid_enum',
        ], $user);

        $response->assertSessionHasErrors('id_type');
        $this->assertSame(0, Resident::query()->count());
    }

    public function test_create_rejects_invalid_email_format(): void
    {
        $user = $this->adminActingAs();

        $response = $this->tenantPost('/residents', [
            'first_name' => 'Bad',
            'last_name' => 'Email',
            'phone_country_code' => 'SA',
            'phone_number' => '512345678',
            'email' => 'not-an-email',
        ], $user);

        $response->assertSessionHasErrors('email');
        $this->assertSame(0, Resident::query()->count());
    }

    public function test_create_requires_first_name_when_first_name_ar_absent(): void
    {
        $user = $this->adminActingAs();

        $response = $this->tenantPost('/residents', [
            // neither first_name nor first_name_ar provided
            'last_name' => 'Missing',
            'phone_country_code' => 'SA',
            'phone_number' => '512345678',
        ], $user);

        $response->assertSessionHasErrors('first_name');
        $this->assertSame(0, Resident::query()->count());
    }

    public function test_create_requires_last_name_when_last_name_ar_absent(): void
    {
        $user = $this->adminActingAs();

        $response = $this->tenantPost('/residents', [
            'first_name' => 'Present',
            // neither last_name nor last_name_ar provided
            'phone_country_code' => 'SA',
            'phone_number' => '512345678',
        ], $user);

        $response->assertSessionHasErrors('last_name');
        $this->assertSame(0, Resident::query()->count());
    }

    public function test_force_create_false_still_blocks_duplicate(): void
    {
        $user = $this->adminActingAs();

        Resident::factory()->withPhone('966512345678')->create([
            'first_name' => 'Original',
            'last_name' => 'Person',
            'phone_country_code' => 'SA',
            'phone_number' => '512345678',
        ]);

        $response = $this->tenantPost('/residents', [
            'first_name' => 'Another',
            'last_name' => 'Person',
            'phone_country_code' => 'SA',
            'phone_number' => '512345678',
            'force_create' => false,
        ], $user);

        $response->assertSessionHasErrors('phone_number');
        $this->assertSame(1, Resident::query()->count());
    }

    // ── QA: AC3 / edge — name max-length boundary ────────────────────────────

    public function test_create_accepts_name_at_max_length_boundary(): void
    {
        $user = $this->adminActingAs();

        $maxName = str_repeat('A', 255);

        $response = $this->tenantPost('/residents', [
            'first_name' => $maxName,
            'last_name' => $maxName,
            'phone_country_code' => 'SA',
            'phone_number' => '555000001',
        ], $user);

        $response->assertSessionHasNoErrors();
        $this->assertSame(1, Resident::query()->count());
    }

    public function test_create_rejects_first_name_exceeding_max_length(): void
    {
        $user = $this->adminActingAs();

        $tooLong = str_repeat('B', 256);

        $response = $this->tenantPost('/residents', [
            'first_name' => $tooLong,
            'last_name' => 'OK',
            'phone_country_code' => 'SA',
            'phone_number' => '555000002',
        ], $user);

        $response->assertSessionHasErrors('first_name');
        $this->assertSame(0, Resident::query()->count());
    }

    // ── QA: edge — XSS / HTML in name fields stored verbatim ─────────────────

    public function test_create_stores_html_in_name_fields_verbatim_without_stripping(): void
    {
        $user = $this->adminActingAs();

        $htmlName = '<script>alert(1)</script>';

        $response = $this->tenantPost('/residents', [
            'first_name' => $htmlName,
            'last_name' => 'Safe',
            'phone_country_code' => 'SA',
            'phone_number' => '512000001',
        ], $user);

        $response->assertSessionHasNoErrors();
        $resident = Resident::query()->where('first_name', $htmlName)->first();
        // The model stores the value as-is; XSS prevention is a frontend concern
        $this->assertNotNull($resident);
        $this->assertSame($htmlName, $resident->first_name);
    }

    // ── QA: AC4 / edge — search edge cases ───────────────────────────────────

    public function test_index_search_returns_empty_result_for_no_match(): void
    {
        $user = $this->adminActingAs();

        Resident::factory()->create(['first_name' => 'Ahmed', 'last_name' => 'Test']);

        $response = $this->tenantGet('/residents?search=NoMatchWhatsoever12345', $user);

        $response->assertOk();
        $payload = $response->viewData('page')['props']['residents']['data'];
        $this->assertCount(0, $payload);
    }

    public function test_index_search_matches_phone_number_fragment(): void
    {
        $user = $this->adminActingAs();

        $target = Resident::factory()->withPhone('966512345678')->create([
            'first_name' => 'Target',
            'phone_number' => '512345678',
        ]);
        Resident::factory()->withPhone('966599990000')->create([
            'first_name' => 'Other',
            'phone_number' => '599990000',
        ]);

        $response = $this->tenantGet('/residents?search=512345678', $user);

        $response->assertOk();
        $payload = $response->viewData('page')['props']['residents']['data'];
        $ids = array_column($payload, 'id');
        $this->assertContains($target->id, $ids);
    }

    public function test_index_search_per_page_is_clamped_to_maximum_of_50(): void
    {
        $user = $this->adminActingAs();

        // Create 60 residents; even if per_page=200 is requested, cap at 50
        Resident::factory()->count(60)->create();

        $response = $this->tenantGet('/residents?per_page=200', $user);

        $response->assertOk();
        $props = $response->viewData('page')['props'];
        $this->assertLessThanOrEqual(50, $props['residents']['per_page']);
    }

    public function test_index_search_per_page_is_clamped_to_minimum_of_1(): void
    {
        $user = $this->adminActingAs();

        Resident::factory()->count(3)->create();

        $response = $this->tenantGet('/residents?per_page=0', $user);

        $response->assertOk();
        $props = $response->viewData('page')['props'];
        $this->assertGreaterThanOrEqual(1, $props['residents']['per_page']);
    }

    // ── QA: authentication failure paths ─────────────────────────────────────

    public function test_unauthenticated_user_cannot_access_index(): void
    {
        $response = $this->get('/residents');

        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_user_cannot_post_store(): void
    {
        $response = $this->post('/residents', [
            'first_name' => 'Ghost',
            'last_name' => 'User',
            'phone_country_code' => 'SA',
            'phone_number' => '512345678',
        ]);

        $response->assertRedirect('/login');
        $this->assertSame(0, Resident::query()->count());
    }

    public function test_unauthenticated_user_cannot_access_duplicate_check(): void
    {
        $response = $this->get(
            '/residents/duplicate-check?phone_country_code=SA&phone_number=512345678'
        );

        $response->assertRedirect('/login');
    }

    // ── QA: tenant boundary — store is scoped to current tenant ──────────────

    public function test_store_assigns_created_resident_to_current_tenant(): void
    {
        $user = $this->adminActingAs();

        $payload = [
            'first_name' => 'Tenant',
            'last_name' => 'Scoped',
            'phone_country_code' => 'SA',
            'phone_number' => '500111222',
        ];

        $this->tenantPost('/residents', $payload, $user);

        $resident = Resident::query()->where('first_name', 'Tenant')->first();
        $this->assertNotNull($resident);
        $this->assertSame($this->tenant->id, $resident->account_tenant_id);
    }
}
