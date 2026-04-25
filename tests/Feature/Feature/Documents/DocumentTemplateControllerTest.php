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
}
