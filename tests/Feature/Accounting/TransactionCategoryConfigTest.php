<?php

namespace Tests\Feature\Accounting;

use App\Models\AccountMembership;
use App\Models\Setting;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionCategoryConfigTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    /**
     * @return array{0: User, 1: Tenant}
     */
    private function authenticateUserWithTenant(): array
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Accounting Test Account']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($user);

        return [$user, $tenant];
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('accounting.settings.categories.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_transaction_categories_page(): void
    {
        [, $tenant] = $this->authenticateUserWithTenant();

        Setting::factory()->create([
            'type' => 'transaction_category',
            'subtype' => 'income',
            'name_en' => 'Rent',
            'name_ar' => 'إيجار',
            'is_active' => true,
            'is_default' => true,
        ]);

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('accounting.settings.categories.index'));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('accounting/settings/categories/Index')
                ->has('categories', 1)
                ->where('categories.0.name_en', 'Rent')
                ->where('categories.0.category_type', 'income')
                ->where('categories.0.is_default', true)
        );
    }

    public function test_authenticated_user_can_create_income_category(): void
    {
        [, $tenant] = $this->authenticateUserWithTenant();

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('accounting.settings.categories.store'), [
                'name_en' => 'Parking Fee',
                'name_ar' => 'رسوم الموقف',
                'category_type' => 'income',
            ]);

        $response->assertRedirect(route('accounting.settings.categories.index'));

        $this->assertDatabaseHas('rf_settings', [
            'name_en' => 'Parking Fee',
            'name_ar' => 'رسوم الموقف',
            'type' => 'transaction_category',
            'subtype' => 'income',
            'is_active' => true,
            'is_default' => false,
        ]);
    }

    public function test_authenticated_user_can_create_expense_category(): void
    {
        [, $tenant] = $this->authenticateUserWithTenant();

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('accounting.settings.categories.store'), [
                'name_en' => 'Landscaping',
                'name_ar' => 'تنسيق الحدائق',
                'category_type' => 'expense',
            ]);

        $response->assertRedirect(route('accounting.settings.categories.index'));

        $this->assertDatabaseHas('rf_settings', [
            'name_en' => 'Landscaping',
            'name_ar' => 'تنسيق الحدائق',
            'type' => 'transaction_category',
            'subtype' => 'expense',
        ]);
    }

    public function test_create_requires_name_en(): void
    {
        [, $tenant] = $this->authenticateUserWithTenant();

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('accounting.settings.categories.store'), [
                'name_en' => '',
                'name_ar' => 'اختبار',
                'category_type' => 'income',
            ]);

        $response->assertSessionHasErrors('name_en');
    }

    public function test_create_requires_valid_category_type(): void
    {
        [, $tenant] = $this->authenticateUserWithTenant();

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('accounting.settings.categories.store'), [
                'name_en' => 'Test',
                'name_ar' => 'اختبار',
                'category_type' => 'invalid',
            ]);

        $response->assertSessionHasErrors('category_type');
    }

    public function test_authenticated_user_can_update_category(): void
    {
        [, $tenant] = $this->authenticateUserWithTenant();

        $category = Setting::factory()->create([
            'type' => 'transaction_category',
            'subtype' => 'income',
            'name_en' => 'Old Name',
            'name_ar' => 'الاسم القديم',
            'is_active' => true,
            'is_default' => false,
        ]);

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->put(route('accounting.settings.categories.update', $category), [
                'name_en' => 'New Name',
                'name_ar' => 'الاسم الجديد',
            ]);

        $response->assertRedirect(route('accounting.settings.categories.index'));

        $this->assertDatabaseHas('rf_settings', [
            'id' => $category->id,
            'name_en' => 'New Name',
            'name_ar' => 'الاسم الجديد',
        ]);
    }

    public function test_update_does_not_change_category_type(): void
    {
        [, $tenant] = $this->authenticateUserWithTenant();

        $category = Setting::factory()->create([
            'type' => 'transaction_category',
            'subtype' => 'income',
            'name_en' => 'Test Category',
            'name_ar' => 'فئة اختبار',
        ]);

        $this
            ->withSession(['tenant_id' => $tenant->id])
            ->put(route('accounting.settings.categories.update', $category), [
                'name_en' => 'Updated Name',
                'name_ar' => 'اسم محدث',
            ]);

        $this->assertDatabaseHas('rf_settings', [
            'id' => $category->id,
            'subtype' => 'income',
        ]);
    }

    public function test_toggle_deactivates_active_category(): void
    {
        [, $tenant] = $this->authenticateUserWithTenant();

        $category = Setting::factory()->create([
            'type' => 'transaction_category',
            'subtype' => 'income',
            'is_active' => true,
        ]);

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('accounting.settings.categories.toggle', $category));

        $response->assertRedirect(route('accounting.settings.categories.index'));

        $this->assertDatabaseHas('rf_settings', [
            'id' => $category->id,
            'is_active' => false,
        ]);
    }

    public function test_toggle_reactivates_inactive_category(): void
    {
        [, $tenant] = $this->authenticateUserWithTenant();

        $category = Setting::factory()->create([
            'type' => 'transaction_category',
            'subtype' => 'expense',
            'is_active' => false,
        ]);

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('accounting.settings.categories.toggle', $category));

        $response->assertRedirect(route('accounting.settings.categories.index'));

        $this->assertDatabaseHas('rf_settings', [
            'id' => $category->id,
            'is_active' => true,
        ]);
    }

    public function test_non_transaction_category_returns_404_on_update(): void
    {
        [, $tenant] = $this->authenticateUserWithTenant();

        $otherSetting = Setting::factory()->create([
            'type' => 'payment_schedule',
        ]);

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->put(route('accounting.settings.categories.update', $otherSetting), [
                'name_en' => 'Test',
                'name_ar' => 'اختبار',
            ]);

        $response->assertNotFound();
    }

    public function test_default_category_cannot_be_deleted(): void
    {
        [, $tenant] = $this->authenticateUserWithTenant();

        $defaultCategory = Setting::factory()->create([
            'type' => 'transaction_category',
            'subtype' => 'income',
            'is_default' => true,
        ]);

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->delete(route('accounting.settings.categories.destroy', $defaultCategory));

        $response->assertSessionHasErrors('category');

        $this->assertModelExists($defaultCategory);
    }

    public function test_non_default_category_can_be_deleted(): void
    {
        [, $tenant] = $this->authenticateUserWithTenant();

        $category = Setting::factory()->create([
            'type' => 'transaction_category',
            'subtype' => 'expense',
            'is_default' => false,
        ]);

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->delete(route('accounting.settings.categories.destroy', $category));

        $response->assertRedirect(route('accounting.settings.categories.index'));

        $this->assertModelMissing($category);
    }

    public function test_seeded_categories_exist_in_database(): void
    {
        $this->artisan('db:seed', ['--class' => 'SettingSeeder']);

        $incomeCategories = Setting::query()
            ->where('type', 'transaction_category')
            ->where('subtype', 'income')
            ->where('is_default', true)
            ->get();

        $expenseCategories = Setting::query()
            ->where('type', 'transaction_category')
            ->where('subtype', 'expense')
            ->where('is_default', true)
            ->get();

        $this->assertCount(3, $incomeCategories);
        $this->assertCount(3, $expenseCategories);

        $incomeNames = $incomeCategories->pluck('name_en')->toArray();
        $this->assertContains('Rent', $incomeNames);
        $this->assertContains('Late Fee', $incomeNames);
        $this->assertContains('Service Fee', $incomeNames);

        $expenseNames = $expenseCategories->pluck('name_en')->toArray();
        $this->assertContains('Maintenance', $expenseNames);
        $this->assertContains('Utility', $expenseNames);
        $this->assertContains('Repairs', $expenseNames);
    }

    public function test_transaction_can_use_seeded_category(): void
    {
        $this->artisan('db:seed', ['--class' => 'SettingSeeder']);

        $rentCategory = Setting::where('name_en', 'Rent')
            ->where('type', 'transaction_category')
            ->first();

        $this->assertNotNull($rentCategory);

        $this->assertDatabaseHas('rf_settings', [
            'id' => $rentCategory->id,
            'type' => 'transaction_category',
            'subtype' => 'income',
            'is_active' => true,
        ]);
    }
}
