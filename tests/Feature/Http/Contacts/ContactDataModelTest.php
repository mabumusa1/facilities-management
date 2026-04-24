<?php

namespace Tests\Feature\Http\Contacts;

use App\Enums\IdType;
use App\Models\Dependent;
use App\Models\Owner;
use App\Models\Professional;
use App\Models\Resident;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ContactDataModelTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create(['name' => 'Contacts Test Account']);
        $this->tenant->makeCurrent();
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    // ── Resident ──

    public function test_resident_can_be_created_with_bilingual_name_and_id_type(): void
    {
        $resident = Resident::factory()->create([
            'first_name' => 'Ahmed',
            'first_name_ar' => 'أحمد',
            'last_name' => 'Al-Rashidi',
            'last_name_ar' => 'الراشدي',
            'id_type' => IdType::EmiratesId,
        ]);

        $this->assertModelExists($resident);
        $this->assertEquals('Ahmed', $resident->first_name);
        $this->assertEquals('أحمد', $resident->first_name_ar);
        $this->assertEquals('Al-Rashidi', $resident->last_name);
        $this->assertEquals('الراشدي', $resident->last_name_ar);
        $this->assertEquals(IdType::EmiratesId, $resident->id_type);
    }

    public function test_resident_name_virtual_attribute_returns_english_when_locale_is_en(): void
    {
        $resident = Resident::factory()->create([
            'first_name' => 'Ahmed',
            'last_name' => 'Al-Rashidi',
            'first_name_ar' => 'أحمد',
            'last_name_ar' => 'الراشدي',
        ]);

        app()->setLocale('en');

        $this->assertEquals('Ahmed Al-Rashidi', $resident->name);
    }

    public function test_resident_name_virtual_attribute_returns_arabic_when_locale_is_ar(): void
    {
        $resident = Resident::factory()->create([
            'first_name' => 'Ahmed',
            'last_name' => 'Al-Rashidi',
            'first_name_ar' => 'أحمد',
            'last_name_ar' => 'الراشدي',
        ]);

        app()->setLocale('ar');

        $this->assertEquals('أحمد الراشدي', $resident->name);
    }

    public function test_resident_name_falls_back_to_arabic_when_english_is_null(): void
    {
        $resident = Resident::factory()->create([
            'first_name' => null,
            'last_name' => null,
            'first_name_ar' => 'أحمد',
            'last_name_ar' => 'الراشدي',
        ]);

        app()->setLocale('en');

        $this->assertEquals('أحمد الراشدي', $resident->name);
    }

    public function test_resident_name_falls_back_to_english_when_arabic_is_null_and_locale_is_ar(): void
    {
        $resident = Resident::factory()->create([
            'first_name' => 'Ahmed',
            'last_name' => 'Al-Rashidi',
            'first_name_ar' => null,
            'last_name_ar' => null,
        ]);

        app()->setLocale('ar');

        $this->assertEquals('Ahmed Al-Rashidi', $resident->name);
    }

    public function test_resident_id_type_is_cast_to_enum(): void
    {
        $resident = Resident::factory()->create(['id_type' => IdType::Passport]);

        $fresh = Resident::withoutGlobalScopes()->find($resident->id);

        $this->assertInstanceOf(IdType::class, $fresh->id_type);
        $this->assertEquals(IdType::Passport, $fresh->id_type);
    }

    // ── Owner ──

    public function test_owner_can_be_created_with_bilingual_name_and_id_type(): void
    {
        $owner = Owner::factory()->create([
            'first_name' => 'Fatima',
            'first_name_ar' => 'فاطمة',
            'last_name' => 'Al-Zahrawi',
            'last_name_ar' => 'الزهراوي',
            'id_type' => IdType::NationalId,
        ]);

        $this->assertModelExists($owner);
        $this->assertEquals('Fatima', $owner->first_name);
        $this->assertEquals('فاطمة', $owner->first_name_ar);
        $this->assertEquals(IdType::NationalId, $owner->id_type);
    }

    public function test_owner_name_is_locale_aware(): void
    {
        $owner = Owner::factory()->create([
            'first_name' => 'Fatima',
            'last_name' => 'Al-Zahrawi',
            'first_name_ar' => 'فاطمة',
            'last_name_ar' => 'الزهراوي',
        ]);

        app()->setLocale('en');
        $this->assertEquals('Fatima Al-Zahrawi', $owner->name);

        app()->setLocale('ar');
        $this->assertEquals('فاطمة الزهراوي', $owner->name);
    }

    public function test_owner_units_relationship_is_intact(): void
    {
        $owner = Owner::factory()->create();

        // The relationship is defined — verify it resolves without error
        $this->assertInstanceOf(HasMany::class, $owner->units());
    }

    // ── Professional ──

    public function test_professional_can_be_created_with_bilingual_name_and_national_phone(): void
    {
        $professional = Professional::factory()->create([
            'first_name' => 'Khalid',
            'first_name_ar' => 'خالد',
            'last_name' => 'Al-Mutairi',
            'last_name_ar' => 'المطيري',
            'id_type' => IdType::Iqama,
            'national_phone_number' => '+966501234567',
        ]);

        $this->assertModelExists($professional);
        $this->assertEquals('Khalid', $professional->first_name);
        $this->assertEquals('خالد', $professional->first_name_ar);
        $this->assertEquals(IdType::Iqama, $professional->id_type);
        $this->assertEquals('+966501234567', $professional->national_phone_number);
    }

    public function test_professional_name_is_locale_aware(): void
    {
        $professional = Professional::factory()->create([
            'first_name' => 'Khalid',
            'last_name' => 'Al-Mutairi',
            'first_name_ar' => 'خالد',
            'last_name_ar' => 'المطيري',
        ]);

        app()->setLocale('en');
        $this->assertEquals('Khalid Al-Mutairi', $professional->name);

        app()->setLocale('ar');
        $this->assertEquals('خالد المطيري', $professional->name);
    }

    // ── Dependent ──

    public function test_dependent_can_be_created_with_bilingual_name(): void
    {
        $resident = Resident::factory()->create();

        $dependent = Dependent::factory()->create([
            'dependable_type' => Resident::class,
            'dependable_id' => $resident->id,
            'first_name' => 'Sara',
            'first_name_ar' => 'سارة',
            'last_name' => 'Al-Rashidi',
            'last_name_ar' => 'الراشدي',
        ]);

        $this->assertModelExists($dependent);
        $this->assertEquals('سارة', $dependent->first_name_ar);
        $this->assertEquals('الراشدي', $dependent->last_name_ar);
    }

    public function test_dependent_morphs_to_parent_resident(): void
    {
        $resident = Resident::factory()->create();

        $dependent = Dependent::factory()->create([
            'dependable_type' => Resident::class,
            'dependable_id' => $resident->id,
        ]);

        $this->assertInstanceOf(Resident::class, $dependent->dependable);
        $this->assertEquals($resident->id, $dependent->dependable->id);
    }

    public function test_dependent_name_is_locale_aware(): void
    {
        $resident = Resident::factory()->create();

        $dependent = Dependent::factory()->create([
            'dependable_type' => Resident::class,
            'dependable_id' => $resident->id,
            'first_name' => 'Sara',
            'last_name' => 'Al-Rashidi',
            'first_name_ar' => 'سارة',
            'last_name_ar' => 'الراشدي',
        ]);

        app()->setLocale('en');
        $this->assertEquals('Sara Al-Rashidi', $dependent->name);

        app()->setLocale('ar');
        $this->assertEquals('سارة الراشدي', $dependent->name);
    }

    // ── Tenant scoping ──

    public function test_resident_is_scoped_to_current_tenant(): void
    {
        $residentA = Resident::factory()->create(['first_name' => 'TenantA Resident']);

        // Create a second tenant and its resident
        $tenantB = Tenant::create(['name' => 'Tenant B']);
        $tenantB->makeCurrent();

        $residentB = Resident::factory()->create(['first_name' => 'TenantB Resident']);

        // Restore tenant A and assert only its resident is visible
        $this->tenant->makeCurrent();

        $residents = Resident::all();
        $this->assertTrue($residents->contains('id', $residentA->id));
        $this->assertFalse($residents->contains('id', $residentB->id));
    }

    // ── Phone duplicate search ──

    public function test_residents_can_be_searched_by_tenant_and_phone(): void
    {
        $phone = '+966501111111';

        $resident = Resident::factory()->withPhone($phone)->create();
        Resident::factory()->withPhone('+966502222222')->create();
        Resident::factory()->create(); // null phone

        $results = Resident::where('account_tenant_id', $this->tenant->id)
            ->where('national_phone_number', $phone)
            ->get();

        $this->assertCount(1, $results);
        $this->assertEquals($resident->id, $results->first()->id);
    }

    // ── Soft-delete ──

    public function test_soft_deleted_resident_is_excluded_from_default_queries(): void
    {
        $resident = Resident::factory()->create();
        $resident->delete();

        $found = Resident::find($resident->id);

        $this->assertNull($found);
    }

    public function test_soft_deleted_resident_is_visible_with_trashed(): void
    {
        $resident = Resident::factory()->create();
        $resident->delete();

        $found = Resident::withTrashed()->find($resident->id);

        $this->assertNotNull($found);
        $this->assertModelExists($found);
    }

    // ── IdType enum values ──

    public function test_all_id_type_enum_cases_are_valid(): void
    {
        $cases = IdType::cases();

        $this->assertCount(5, $cases);

        $values = array_column($cases, 'value');
        $this->assertContains('national_id', $values);
        $this->assertContains('passport', $values);
        $this->assertContains('iqama', $values);
        $this->assertContains('emirates_id', $values);
        $this->assertContains('other', $values);
    }
}
