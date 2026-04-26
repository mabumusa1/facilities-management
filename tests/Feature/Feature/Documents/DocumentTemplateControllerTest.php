<?php

namespace Tests\Feature\Feature\Documents;

use App\Enums\RolesEnum;
use App\Models\AccountMembership;
use App\Models\DocumentTemplate;
use App\Models\DocumentVersion;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DocumentTemplateControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function authenticateAdmin(): User
    {
        Role::create(['name' => RolesEnum::ACCOUNT_ADMINS->value, 'guard_name' => 'web']);

        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Test Docs']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::ACCOUNT_ADMINS->value,
        ]);

        $user->assignRole(RolesEnum::ACCOUNT_ADMINS);

        $this->actingAs($user);
        session()->put('tenant_id', $tenant->id);

        return $user;
    }

    private function authenticateNonAdmin(): array
    {
        Role::create(['name' => RolesEnum::TENANTS->value, 'guard_name' => 'web']);

        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => fake()->unique()->company()]);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::TENANTS->value,
        ]);

        $user->assignRole(RolesEnum::TENANTS);

        $this->actingAs($user);
        session()->put('tenant_id', $tenant->id);

        return [$user, $tenant];
    }

    public function test_index_returns_templates_list(): void
    {
        $user = $this->authenticateAdmin();

        $tenant = Tenant::find(session('tenant_id'));
        $tenant?->makeCurrent();
        $this->assertNotNull($tenant, 'Tenant should be current');

        DocumentTemplate::create([
            'account_tenant_id' => $tenant->id,
            'name' => ['en' => 'Test', 'ar' => null],
            'type' => 'lease',
            'format' => 'in_platform',
            'created_by' => $user->id,
        ]);

        $response = $this->get('/admin/documents');

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('admin/documents/Index')
                ->has('templates.data', 1)
        );
    }

    public function test_store_creates_template_with_version(): void
    {
        $this->authenticateAdmin();

        $response = $this->post('/admin/documents', [
            'name_en' => 'Lease Agreement',
            'name_ar' => 'عقد إيجار',
            'type' => 'lease',
            'format' => 'in_platform',
            'body_en' => 'Dear {{tenant.name}}, your lease starts {{lease.start_date}}.',
        ]);

        $response->assertRedirect('/admin/documents');

        $this->assertDatabaseHas('rf_document_templates', ['type' => 'lease']);
        $this->assertDatabaseHas('rf_document_versions', ['version_number' => 1]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->authenticateAdmin();

        $response = $this->post('/admin/documents', [
            'name_en' => '',
            'type' => '',
            'format' => '',
        ]);

        $response->assertSessionHasErrors(['name_en', 'type', 'format']);
    }

    public function test_update_creates_new_version(): void
    {
        $this->authenticateAdmin();
        $template = DocumentTemplate::factory()->create([
            'name' => ['en' => 'Original', 'ar' => null],
            'type' => 'lease',
            'format' => 'in_platform',
        ]);
        DocumentVersion::factory()->create([
            'document_template_id' => $template->id,
            'version_number' => 1,
            'body' => json_encode(['en' => 'Old body', 'ar' => '']),
        ]);
        $template->update(['current_version_id' => DocumentVersion::first()->id]);

        $response = $this->put("/admin/documents/{$template->id}", [
            'name_en' => 'Updated Agreement',
            'name_ar' => 'عقد محدث',
            'type' => 'lease',
            'format' => 'in_platform',
            'body_en' => 'New body content.',
        ]);

        $response->assertRedirect('/admin/documents');

        $this->assertDatabaseHas('rf_document_versions', ['version_number' => 2]);
        $template->refresh();
        $this->assertSame('Updated Agreement', $template->name['en']);
    }

    public function test_activate_changes_status(): void
    {
        $this->authenticateAdmin();
        $template = DocumentTemplate::factory()->create(['status' => 'draft']);

        $response = $this->post("/admin/documents/{$template->id}/activate");

        $response->assertRedirect('/admin/documents');
        $this->assertDatabaseHas('rf_document_templates', ['id' => $template->id, 'status' => 'active']);
    }

    public function test_archive_changes_status(): void
    {
        $this->authenticateAdmin();
        $template = DocumentTemplate::factory()->create(['status' => 'active']);

        $response = $this->post("/admin/documents/{$template->id}/archive");

        $response->assertRedirect('/admin/documents');
        $this->assertDatabaseHas('rf_document_templates', ['id' => $template->id, 'status' => 'archived']);
    }

    public function test_destroy_soft_deletes_template(): void
    {
        $this->authenticateAdmin();
        $template = DocumentTemplate::factory()->create();

        $response = $this->delete("/admin/documents/{$template->id}");

        $response->assertRedirect('/admin/documents');
        $this->assertSoftDeleted($template);
    }

    public function test_unauthenticated_user_cannot_access_index(): void
    {
        $response = $this->get('/admin/documents');

        $this->assertContains($response->status(), [403, 302], 'Should be forbidden or redirected');
    }

    public function test_store_name_ar_is_optional(): void
    {
        $this->authenticateAdmin();

        $response = $this->post('/admin/documents', [
            'name_en' => 'Only English Name',
            'name_ar' => null,
            'type' => 'invoice',
            'format' => 'word_upload',
            'body_en' => 'Body content.',
        ]);

        $response->assertRedirect('/admin/documents');
        $this->assertDatabaseHas('rf_document_templates', ['type' => 'invoice']);
    }

    public function test_store_validates_merge_field_missing_required_keys(): void
    {
        $this->authenticateAdmin();

        $response = $this->post('/admin/documents', [
            'name_en' => 'Merge Test',
            'type' => 'lease',
            'format' => 'in_platform',
            'merge_fields' => [
                [
                    'key' => 'test.key',
                    'type' => 'string',
                ],
            ],
        ]);

        $response->assertSessionHasErrors(['merge_fields.0.label_en', 'merge_fields.0.source_path']);
    }

    public function test_store_validates_merge_field_invalid_type(): void
    {
        $this->authenticateAdmin();

        $response = $this->post('/admin/documents', [
            'name_en' => 'Invalid Merge Type',
            'type' => 'lease',
            'format' => 'in_platform',
            'merge_fields' => [
                [
                    'key' => 'test.key',
                    'label_en' => 'Test',
                    'type' => 'invalid_type',
                    'source_path' => 'test.path',
                ],
            ],
        ]);

        $response->assertSessionHasErrors(['merge_fields.0.type']);
    }

    public function test_store_creates_with_valid_merge_fields(): void
    {
        $this->authenticateAdmin();

        $response = $this->post('/admin/documents', [
            'name_en' => 'Template With Fields',
            'type' => 'lease',
            'format' => 'in_platform',
            'body_en' => 'Dear {{resident.name}}, your lease starts {{lease.start_date}}.',
            'merge_fields' => [
                [
                    'key' => 'resident.name',
                    'label_en' => 'Resident Name',
                    'label_ar' => 'اسم المقيم',
                    'type' => 'string',
                    'source_path' => 'resident.full_name',
                ],
                [
                    'key' => 'lease.start_date',
                    'label_en' => 'Start Date',
                    'label_ar' => null,
                    'type' => 'date',
                    'source_path' => 'lease.start_date',
                ],
            ],
        ]);

        $response->assertRedirect('/admin/documents');

        $this->assertDatabaseHas('rf_document_versions', ['version_number' => 1]);
    }

    public function test_show_returns_template_with_versions_descending(): void
    {
        $this->authenticateAdmin();

        $template = DocumentTemplate::factory()->create([
            'name' => ['en' => 'Version Test', 'ar' => null],
            'type' => 'lease',
            'format' => 'in_platform',
            'status' => 'draft',
        ]);

        $v1 = DocumentVersion::factory()->create([
            'document_template_id' => $template->id,
            'version_number' => 1,
            'body' => json_encode(['en' => 'Version 1 body', 'ar' => '']),
            'merge_fields' => [],
        ]);

        $v2 = DocumentVersion::factory()->create([
            'document_template_id' => $template->id,
            'version_number' => 2,
            'body' => json_encode(['en' => 'Version 2 body', 'ar' => '']),
            'merge_fields' => [],
        ]);

        $template->update(['current_version_id' => $v2->id]);

        $response = $this->get("/admin/documents/{$template->id}");

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('admin/documents/Editor')
                ->has('template.versions', 2)
                ->where('template.versions.0.version_number', 2)
                ->where('template.versions.1.version_number', 1)
        );
    }

    public function test_non_admin_user_gets_403_on_index(): void
    {
        $this->authenticateNonAdmin();

        $this->get('/admin/documents')->assertForbidden();
    }

    public function test_non_admin_user_gets_403_on_store(): void
    {
        $this->authenticateNonAdmin();

        $this->post('/admin/documents', [
            'name_en' => 'Should Fail',
            'type' => 'lease',
            'format' => 'in_platform',
        ])->assertForbidden();
    }

    public function test_non_admin_user_gets_403_on_update(): void
    {
        $admin = $this->authenticateAdmin();
        $template = DocumentTemplate::factory()->create([
            'name' => ['en' => 'Admin Template', 'ar' => null],
            'type' => 'lease',
            'format' => 'in_platform',
            'created_by' => $admin->id,
        ]);

        $this->authenticateNonAdmin();

        $this->put("/admin/documents/{$template->id}", [
            'name_en' => 'Hijack Attempt',
            'name_ar' => null,
            'type' => 'lease',
            'format' => 'in_platform',
        ])->assertForbidden();
    }

    public function test_non_admin_user_gets_403_on_activate(): void
    {
        $admin = $this->authenticateAdmin();
        $template = DocumentTemplate::factory()->create([
            'name' => ['en' => 'Draft', 'ar' => null],
            'status' => 'draft',
            'created_by' => $admin->id,
        ]);

        $this->authenticateNonAdmin();

        $this->post("/admin/documents/{$template->id}/activate")->assertForbidden();
    }

    public function test_non_admin_user_gets_403_on_archive(): void
    {
        $admin = $this->authenticateAdmin();
        $template = DocumentTemplate::factory()->create([
            'name' => ['en' => 'Active', 'ar' => null],
            'status' => 'active',
            'created_by' => $admin->id,
        ]);

        $this->authenticateNonAdmin();

        $this->post("/admin/documents/{$template->id}/archive")->assertForbidden();
    }

    public function test_non_admin_user_gets_403_on_destroy(): void
    {
        $admin = $this->authenticateAdmin();
        $template = DocumentTemplate::factory()->create([
            'name' => ['en' => 'To Delete', 'ar' => null],
            'created_by' => $admin->id,
        ]);

        $this->authenticateNonAdmin();

        $this->delete("/admin/documents/{$template->id}")->assertForbidden();
    }

    public function test_non_admin_user_gets_403_on_show(): void
    {
        $admin = $this->authenticateAdmin();
        $template = DocumentTemplate::factory()->create([
            'name' => ['en' => 'View Attempt', 'ar' => null],
            'created_by' => $admin->id,
        ]);

        $this->authenticateNonAdmin();

        $this->get("/admin/documents/{$template->id}")->assertForbidden();
    }

    public function test_archived_template_appears_in_index(): void
    {
        $user = $this->authenticateAdmin();

        $tenant = Tenant::find(session('tenant_id'));
        $tenant?->makeCurrent();

        DocumentTemplate::create([
            'account_tenant_id' => $tenant->id,
            'name' => ['en' => 'Archived Template', 'ar' => null],
            'type' => 'booking',
            'format' => 'in_platform',
            'status' => 'archived',
            'created_by' => $user->id,
        ]);

        DocumentTemplate::create([
            'account_tenant_id' => $tenant->id,
            'name' => ['en' => 'Active Template', 'ar' => null],
            'type' => 'lease',
            'format' => 'in_platform',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $response = $this->get('/admin/documents');

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('admin/documents/Index')
                ->has('templates.data', 2)
        );
    }

    public function test_show_returns_404_for_nonexistent_template(): void
    {
        $this->authenticateAdmin();

        $response = $this->get('/admin/documents/99999');

        $response->assertNotFound();
    }

    public function test_cross_tenant_template_not_accessible(): void
    {
        $this->authenticateAdmin();

        $tenant = Tenant::find(session('tenant_id'));
        $tenant?->makeCurrent();

        $template = DocumentTemplate::create([
            'account_tenant_id' => $tenant->id,
            'name' => ['en' => 'My Template', 'ar' => null],
            'type' => 'lease',
            'format' => 'in_platform',
            'created_by' => User::factory()->create()->id,
        ]);

        $this->authenticateNonAdmin();

        $this->get("/admin/documents/{$template->id}")->assertForbidden();
    }
}
