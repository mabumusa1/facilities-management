<?php

namespace Tests\Feature\Http\Properties;

use App\Jobs\ImportUnitsJob;
use App\Models\AccountMembership;
use App\Models\Building;
use App\Models\Community;
use App\Models\ExcelSheet;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\UnitCategory;
use App\Models\UnitType;
use App\Models\User;
use DB;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class UnitImportTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    private Community $community;

    private Building $building;

    private Status $unitStatus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->tenant = Tenant::create(['name' => 'Import Test Account']);
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

        $this->community = Community::factory()->create([
            'name' => 'Test Community',
            'account_tenant_id' => $this->tenant->id,
        ]);

        $this->building = Building::factory()->create([
            'name' => 'Building A',
            'rf_community_id' => $this->community->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $this->unitStatus = Status::firstOrCreate(
            ['type' => 'unit', 'name' => 'available'],
            [
                'name' => 'available',
                'name_en' => 'Available',
                'name_ar' => 'متاحة',
                'type' => 'unit',
            ]
        );

        // Ensure category and type exist for unit creation
        UnitCategory::firstOrCreate(['name' => 'Apartment'], ['name' => 'Apartment']);
        UnitType::firstOrCreate(['name' => 'Studio'], ['name' => 'Studio']);
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    private function ensureAccountAdminsRoleExists(): void
    {
        $exists = DB::table('roles')
            ->where('name', 'accountAdmins')
            ->where('guard_name', 'web')
            ->exists();

        if (! $exists) {
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

    /**
     * Generate an in-memory xlsx file with the given rows.
     * First element of $rows is the header row.
     *
     * @param  list<list<string>>  $rows
     */
    private function makeXlsxFile(array $rows, string $fileName = 'test.xlsx'): UploadedFile
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($rows as $rowIdx => $row) {
            foreach ($row as $colIdx => $value) {
                $sheet->setCellValueByColumnAndRow($colIdx + 1, $rowIdx + 1, $value);
            }
        }

        $tempPath = sys_get_temp_dir().'/'.uniqid('unit_import_').'_test.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return new UploadedFile(
            path: $tempPath,
            originalName: $fileName,
            mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            error: UPLOAD_ERR_OK,
            test: true
        );
    }

    // ─── Upload endpoint ───────────────────────────────────────────────────────

    public function test_upload_returns_session_id_and_detected_headers(): void
    {
        Storage::fake('local');

        $file = $this->makeXlsxFile([
            ['Unit Name', 'Community', 'Building', 'Area (sqm)', 'Status'],
            ['A-101', 'Test Community', 'Building A', '85.5', 'available'],
            ['A-102', 'Test Community', 'Building A', '90.0', 'available'],
        ]);

        $response = $this->postJson('/units/import/upload', ['file' => $file]);

        $response->assertOk();
        $response->assertJsonStructure(['import_session_id', 'headers', 'row_count', 'auto_mapping']);
        $this->assertSame(2, $response->json('row_count'));
        $this->assertContains('Unit Name', $response->json('headers'));

        $this->assertDatabaseHas('rf_excel_sheets', [
            'id' => $response->json('import_session_id'),
            'import_type' => 'unit',
            'status' => 'uploaded',
            'account_tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_upload_rejects_non_xlsx_file(): void
    {
        $file = UploadedFile::fake()->create('import.csv', 1, 'text/csv');

        $response = $this->postJson('/units/import/upload', ['file' => $file]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['file']);
    }

    // ─── Validate endpoint ─────────────────────────────────────────────────────

    public function test_validate_returns_valid_and_error_counts(): void
    {
        Storage::fake('local');

        // Create a unit that already exists to test duplicate detection
        Unit::factory()->create([
            'name' => 'EXISTING-101',
            'rf_building_id' => $this->building->id,
            'rf_community_id' => $this->community->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $file = $this->makeXlsxFile([
            ['Unit Name', 'Community', 'Building', 'Status'],
            ['A-101', 'Test Community', 'Building A', 'available'],           // valid
            ['EXISTING-101', 'Test Community', 'Building A', 'available'],    // duplicate
            ['A-103', 'Test Community', 'Nonexistent Building', 'available'], // bad building
        ]);

        // First upload the file
        $uploadResponse = $this->postJson('/units/import/upload', ['file' => $file]);
        $sessionId = $uploadResponse->json('import_session_id');

        // Now validate
        $validateResponse = $this->postJson('/units/import/validate', [
            'import_session_id' => $sessionId,
            'mapping' => [
                'name' => 'Unit Name',
                'rf_community_id' => 'Community',
                'rf_building_id' => 'Building',
                'status' => 'Status',
            ],
        ]);

        $validateResponse->assertOk();
        $validateResponse->assertJsonStructure(['total_rows', 'valid_count', 'error_count', 'errors']);
        $this->assertSame(3, $validateResponse->json('total_rows'));
        $this->assertGreaterThan(0, $validateResponse->json('error_count'));
        $this->assertNotEmpty($validateResponse->json('errors'));
    }

    // ─── Happy path: ≤50 rows → inline import ─────────────────────────────────

    public function test_execute_inline_import_creates_units_for_valid_rows(): void
    {
        Storage::fake('local');
        Queue::fake();

        $file = $this->makeXlsxFile([
            ['Unit Name', 'Community', 'Building', 'Status'],
            ['A-101', 'Test Community', 'Building A', 'available'],
            ['A-102', 'Test Community', 'Building A', 'available'],
            ['A-103', 'Test Community', 'Building A', 'available'],
        ]);

        $uploadResponse = $this->postJson('/units/import/upload', ['file' => $file]);
        $sessionId = $uploadResponse->json('import_session_id');

        // Validate first
        $this->postJson('/units/import/validate', [
            'import_session_id' => $sessionId,
            'mapping' => [
                'name' => 'Unit Name',
                'rf_community_id' => 'Community',
                'rf_building_id' => 'Building',
                'status' => 'Status',
            ],
        ]);

        // Execute
        $executeResponse = $this->postJson('/units/import/execute', [
            'import_session_id' => $sessionId,
            'mapping' => [
                'name' => 'Unit Name',
                'rf_community_id' => 'Community',
                'rf_building_id' => 'Building',
                'status' => 'Status',
            ],
            'import_valid_only' => true,
        ]);

        $executeResponse->assertOk();
        $executeResponse->assertJsonPath('status', 'completed');
        $this->assertGreaterThan(0, $executeResponse->json('success_count'));

        // Verify units were created with correct tenant
        $this->assertDatabaseHas('rf_units', [
            'name' => 'A-101',
            'rf_community_id' => $this->community->id,
            'account_tenant_id' => $this->tenant->id,
        ]);
    }

    // ─── Async path: >50 rows → job dispatched ─────────────────────────────────

    public function test_execute_dispatches_job_for_large_import(): void
    {
        Storage::fake('local');
        Queue::fake();

        // Create 55 rows
        $rows = [['Unit Name', 'Community', 'Building', 'Status']];
        for ($i = 1; $i <= 55; $i++) {
            $rows[] = ["B-{$i}", 'Test Community', 'Building A', 'available'];
        }

        $file = $this->makeXlsxFile($rows);
        $uploadResponse = $this->postJson('/units/import/upload', ['file' => $file]);
        $sessionId = $uploadResponse->json('import_session_id');

        // Validate
        $this->postJson('/units/import/validate', [
            'import_session_id' => $sessionId,
            'mapping' => [
                'name' => 'Unit Name',
                'rf_community_id' => 'Community',
                'rf_building_id' => 'Building',
                'status' => 'Status',
            ],
        ]);

        // Execute should dispatch job
        $executeResponse = $this->postJson('/units/import/execute', [
            'import_session_id' => $sessionId,
            'mapping' => [
                'name' => 'Unit Name',
                'rf_community_id' => 'Community',
                'rf_building_id' => 'Building',
                'status' => 'Status',
            ],
            'import_valid_only' => true,
        ]);

        $executeResponse->assertOk();
        $executeResponse->assertJsonPath('status', 'queued');

        Queue::assertPushed(ImportUnitsJob::class);
    }

    // ─── Template download ─────────────────────────────────────────────────────

    public function test_template_download_returns_xlsx_file(): void
    {
        $response = $this->get('/units/import/template');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    // ─── Tenant isolation ─────────────────────────────────────────────────────

    public function test_cannot_access_another_tenants_import_session(): void
    {
        Storage::fake('local');

        // Create a session belonging to another tenant
        $otherTenant = Tenant::create(['name' => 'Other Tenant']);
        $otherSession = ExcelSheet::create([
            'type' => 'import',
            'import_type' => 'unit',
            'file_path' => 'unit-imports/fake.xlsx',
            'file_name' => 'fake.xlsx',
            'status' => 'uploaded',
            'total_rows' => 5,
            'account_tenant_id' => $otherTenant->id,
        ]);

        // Try to validate using the other tenant's session ID
        $response = $this->postJson('/units/import/validate', [
            'import_session_id' => $otherSession->id,
            'mapping' => ['name' => 'Unit Name'],
        ]);

        // Should fail — 404 because query is scoped to current tenant
        $response->assertNotFound();
    }

    // ─── Progress endpoint ─────────────────────────────────────────────────────

    public function test_progress_returns_status_for_own_session(): void
    {
        $session = ExcelSheet::create([
            'type' => 'import',
            'import_type' => 'unit',
            'file_path' => 'unit-imports/fake.xlsx',
            'file_name' => 'fake.xlsx',
            'status' => 'queued',
            'total_rows' => 100,
            'success_count' => 0,
            'error_count' => 0,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $response = $this->getJson("/units/import/progress/{$session->id}");

        $response->assertOk();
        $response->assertJsonPath('status', 'queued');
        $response->assertJsonPath('total_rows', 100);
    }

    // ─── Validation: missing building ─────────────────────────────────────────

    public function test_validate_flags_missing_building(): void
    {
        Storage::fake('local');

        $file = $this->makeXlsxFile([
            ['Unit Name', 'Community', 'Building', 'Status'],
            ['C-201', 'Test Community', 'West Wing', 'available'],
        ]);

        $uploadResponse = $this->postJson('/units/import/upload', ['file' => $file]);
        $sessionId = $uploadResponse->json('import_session_id');

        $validateResponse = $this->postJson('/units/import/validate', [
            'import_session_id' => $sessionId,
            'mapping' => [
                'name' => 'Unit Name',
                'rf_community_id' => 'Community',
                'rf_building_id' => 'Building',
                'status' => 'Status',
            ],
        ]);

        $validateResponse->assertOk();
        $this->assertSame(1, $validateResponse->json('error_count'));
        $errors = $validateResponse->json('errors');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('West Wing', $errors[0]['message']);
    }
}
