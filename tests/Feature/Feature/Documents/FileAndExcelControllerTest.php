<?php

namespace Tests\Feature\Feature\Documents;

use App\Models\AccountMembership;
use App\Models\Community;
use App\Models\ExcelSheet;
use App\Models\Media;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FileAndExcelControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function authenticateUser(): Tenant
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Documents Account']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($user);

        return $tenant;
    }

    /**
     * Verify rf/files upload then delete lifecycle.
     */
    public function test_file_upload_and_delete_work_as_expected(): void
    {
        $tenant = $this->authenticateUser();
        Storage::fake('public');

        $upload = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson(route('rf.files.store'), [
                'image' => UploadedFile::fake()->image('proof.png'),
                'collection' => 'documents',
                'notes' => 'Lease contract',
            ]);

        $upload
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['id', 'url', 'name'],
                'message',
            ]);

        $media = Media::query()->firstOrFail();
        $storedPath = Str::after(parse_url($media->url, PHP_URL_PATH) ?? '', '/storage/');

        Storage::disk('public')->assertExists($storedPath);
        $this->assertDatabaseHas('media', [
            'id' => $media->id,
            'collection' => 'documents',
        ]);

        $delete = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->deleteJson(route('rf.files.destroy', $media));

        $delete
            ->assertOk()
            ->assertJsonPath('data.id', $media->id);

        Storage::disk('public')->assertMissing($storedPath);
        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }

    public function test_excel_import_store_and_leads_errors_page_contract(): void
    {
        $tenant = $this->authenticateUser();
        Storage::fake('public');

        $community = Community::factory()->create([
            'name' => 'Import Community',
            'account_tenant_id' => $tenant->id,
        ]);

        $store = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson(route('rf.excel-sheets.store'), [
                'file' => UploadedFile::fake()->create('units.xlsx', 100),
                'rf_community_id' => $community->id,
            ]);

        $store
            ->assertOk()
            ->assertJsonPath('data.status', 'uploaded');

        $this->assertDatabaseHas('rf_excel_sheets', [
            'type' => 'general',
            'rf_community_id' => $community->id,
            'account_tenant_id' => $tenant->id,
        ]);

        ExcelSheet::create([
            'type' => 'leads',
            'file_path' => '/storage/imports/excel/leads-errors.xlsx',
            'file_name' => 'leads-errors.xlsx',
            'status' => 'error',
            'error_details' => ['row' => 3, 'message' => 'Missing phone number'],
            'rf_community_id' => null,
            'account_tenant_id' => $tenant->id,
        ]);

        $page = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('rf.excel-sheets.leads.errors'));

        $page
            ->assertOk()
            ->assertInertia(fn (Assert $inertia) => $inertia
                ->component('documents/LeadsImportErrors')
                ->has('errors.data', 1)
                ->where('errors.data.0.status', 'error')
            );
    }

    public function test_land_and_leads_import_endpoints_accept_valid_payloads(): void
    {
        $tenant = $this->authenticateUser();
        Storage::fake('public');

        $community = Community::factory()->create([
            'name' => 'Land Community',
            'account_tenant_id' => $tenant->id,
        ]);

        $land = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson(route('rf.excel-sheets.land'), [
                'rf_community_id' => $community->id,
                'file' => UploadedFile::fake()->create('land.xlsx', 50),
            ]);

        $land
            ->assertOk()
            ->assertJsonPath('data.status', 'uploaded');

        $leads = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson(route('rf.excel-sheets.leads'), [
                'file' => UploadedFile::fake()->create('leads.xlsx', 50),
            ]);

        $leads
            ->assertOk()
            ->assertJsonPath('data.status', 'uploaded');

        $this->assertDatabaseHas('rf_excel_sheets', [
            'type' => 'land',
            'account_tenant_id' => $tenant->id,
        ]);

        $this->assertDatabaseHas('rf_excel_sheets', [
            'type' => 'leads',
            'account_tenant_id' => $tenant->id,
        ]);
    }

    public function test_documents_center_page_returns_expected_inertia_payload(): void
    {
        $tenant = $this->authenticateUser();

        $community = Community::factory()->create([
            'name' => 'Docs Community',
            'account_tenant_id' => $tenant->id,
        ]);

        $currentUser = User::query()->firstOrFail();
        $otherUser = User::factory()->create();

        $visibleMedia = Media::create([
            'url' => '/storage/uploads/files/current-user.png',
            'name' => 'Current User Doc',
            'notes' => 'Visible in documents page',
            'mediable_type' => $currentUser::class,
            'mediable_id' => $currentUser->id,
            'collection' => 'documents',
        ]);

        Media::create([
            'url' => '/storage/uploads/files/other-user.png',
            'name' => 'Other User Doc',
            'notes' => 'Should not be visible',
            'mediable_type' => $otherUser::class,
            'mediable_id' => $otherUser->id,
            'collection' => 'documents',
        ]);

        $sheet = ExcelSheet::create([
            'type' => 'general',
            'file_path' => '/storage/imports/excel/docs.xlsx',
            'file_name' => 'docs.xlsx',
            'status' => 'uploaded',
            'rf_community_id' => $community->id,
            'account_tenant_id' => $tenant->id,
        ]);

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('documents.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $inertia) => $inertia
                ->component('documents/Index')
                ->has('communities', 1)
                ->where('communities.0.id', $community->id)
                ->has('mediaFiles', 1)
                ->where('mediaFiles.0.id', $visibleMedia->id)
                ->has('excelImports', 1)
                ->where('excelImports.0.id', $sheet->id)
            );
    }

    public function test_documents_endpoints_support_non_json_form_submissions(): void
    {
        $tenant = $this->authenticateUser();
        Storage::fake('public');

        $community = Community::factory()->create([
            'name' => 'Documents Form Community',
            'account_tenant_id' => $tenant->id,
        ]);

        $this
            ->from(route('documents.index'))
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('rf.files.store'), [
                'image' => UploadedFile::fake()->image('contract.png'),
                'collection' => 'documents',
                'notes' => 'Form upload',
            ])
            ->assertRedirect(route('documents.index'));

        $media = Media::query()->firstOrFail();

        $this
            ->from(route('documents.index'))
            ->withSession(['tenant_id' => $tenant->id])
            ->delete(route('rf.files.destroy', $media))
            ->assertRedirect(route('documents.index'));

        $this
            ->from(route('documents.index'))
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('rf.excel-sheets.store'), [
                'file' => UploadedFile::fake()->create('units.xlsx', 100),
                'rf_community_id' => $community->id,
            ])
            ->assertRedirect(route('documents.index'));

        $this
            ->from(route('documents.index'))
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('rf.excel-sheets.land'), [
                'rf_community_id' => $community->id,
                'file' => UploadedFile::fake()->create('land.xlsx', 50),
            ])
            ->assertRedirect(route('documents.index'));

        $this
            ->from(route('documents.index'))
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('rf.excel-sheets.leads'), [
                'file' => UploadedFile::fake()->create('leads.xlsx', 50),
            ])
            ->assertRedirect(route('documents.index'));

        $this->assertDatabaseCount('rf_excel_sheets', 3);
    }
}
