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

        // Ensure category and type exist for unit creation using factories (category_id is NOT NULL)
        $category = UnitCategory::firstOrCreate(
            ['name' => 'Apartment'],
            UnitCategory::factory()->make(['name' => 'Apartment'])->toArray()
        );
        UnitType::firstOrCreate(
            ['name' => 'Studio'],
            UnitType::factory()->make(['name' => 'Studio', 'category_id' => $category->id])->toArray()
        );
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

        // Should fail — 422 because the scoped Rule::exists rejects cross-tenant session IDs at validation
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['import_session_id']);
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

    // ─── QA gap tests ─────────────────────────────────────────────────────────

    // ── AC1: authentication gate ───────────────────────────────────────────────

    /**
     * AC1 / Failure-path: unauthenticated requests to import routes redirect to login.
     */
    public function test_unauthenticated_user_cannot_upload(): void
    {
        $this->app['auth']->forgetGuards();

        $file = UploadedFile::fake()->create('import.xlsx', 10,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $response = $this->postJson('/units/import/upload', ['file' => $file]);

        $response->assertUnauthorized();
    }

    /**
     * AC1 / Failure-path: user without properties.CREATE permission is forbidden.
     */
    public function test_user_without_create_permission_cannot_upload(): void
    {
        Storage::fake('local');

        $unprivileged = User::factory()->create();
        AccountMembership::create([
            'user_id' => $unprivileged->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);
        // Intentionally do NOT assign any role — zero permissions

        $file = UploadedFile::fake()->create('import.xlsx', 10,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $response = $this
            ->actingAs($unprivileged)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->postJson('/units/import/upload', ['file' => $file]);

        $response->assertForbidden();
    }

    /**
     * AC1 / Failure-path: user without permission cannot call execute.
     */
    public function test_user_without_create_permission_cannot_execute(): void
    {
        $unprivileged = User::factory()->create();
        AccountMembership::create([
            'user_id' => $unprivileged->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);

        $response = $this
            ->actingAs($unprivileged)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->postJson('/units/import/execute', [
                'import_session_id' => 999,
                'mapping' => ['name' => 'Unit Name'],
            ]);

        $response->assertForbidden();
    }

    // ── AC1: upload validation edge cases ──────────────────────────────────────

    /**
     * AC1 / Failure-path: upload without any file returns 422.
     */
    public function test_upload_without_file_returns_422(): void
    {
        $response = $this->postJson('/units/import/upload', []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['file']);
    }

    /**
     * AC1 / Edge: upload an Excel file with only a header row (zero data rows).
     */
    public function test_upload_xlsx_with_header_only_returns_zero_row_count(): void
    {
        Storage::fake('local');

        $file = $this->makeXlsxFile([
            ['Unit Name', 'Community', 'Building', 'Status'],
        ]);

        $response = $this->postJson('/units/import/upload', ['file' => $file]);

        $response->assertOk();
        $this->assertSame(0, $response->json('row_count'));
    }

    /**
     * AC1 / Edge: empty xlsx (no rows at all) is accepted with 0 row_count.
     */
    public function test_upload_completely_empty_xlsx_returns_zero_rows(): void
    {
        Storage::fake('local');

        $file = $this->makeXlsxFile([]);

        $response = $this->postJson('/units/import/upload', ['file' => $file]);

        $response->assertOk();
        $this->assertSame(0, $response->json('row_count'));
    }

    // ── AC2: validate endpoint edge cases ──────────────────────────────────────

    /**
     * AC2 / Failure-path: validate with non-existent session ID returns 422 (not found via exists rule).
     */
    public function test_validate_with_nonexistent_session_id_returns_422(): void
    {
        $response = $this->postJson('/units/import/validate', [
            'import_session_id' => 999999,
            'mapping' => ['name' => 'Unit Name'],
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['import_session_id']);
    }

    /**
     * AC2 / Failure-path: validate without a mapping array returns 422.
     */
    public function test_validate_without_mapping_returns_422(): void
    {
        Storage::fake('local');

        $file = $this->makeXlsxFile([
            ['Unit Name', 'Building', 'Status'],
            ['X-001', 'Building A', 'available'],
        ]);
        $uploadResponse = $this->postJson('/units/import/upload', ['file' => $file]);
        $sessionId = $uploadResponse->json('import_session_id');

        $response = $this->postJson('/units/import/validate', [
            'import_session_id' => $sessionId,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['mapping']);
    }

    /**
     * AC2 / AC3 / Edge: duplicate unit numbers within the file — first row valid, second flagged.
     * Verifies the AC3 Gherkin: "the second duplicate row is flagged; the first row is treated as valid".
     */
    public function test_validate_intra_file_duplicate_flags_second_row_only(): void
    {
        Storage::fake('local');

        $file = $this->makeXlsxFile([
            ['Unit Name', 'Community', 'Building', 'Status'],
            ['DUP-001', 'Test Community', 'Building A', 'available'],  // row 2 — valid
            ['DUP-001', 'Test Community', 'Building A', 'available'],  // row 3 — duplicate
            ['DUP-002', 'Test Community', 'Building A', 'available'],  // row 4 — valid
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
        $this->assertSame(3, $validateResponse->json('total_rows'));
        $this->assertSame(1, $validateResponse->json('error_count'), 'Only second duplicate should be flagged');
        $this->assertSame(2, $validateResponse->json('valid_count'));

        // The error must be on row 3 (the duplicate), not row 2 (the original)
        $errorRows = array_column($validateResponse->json('errors'), 'row');
        $this->assertContains(3, $errorRows, 'Row 3 (second occurrence) must be flagged');
        $this->assertNotContains(2, $errorRows, 'Row 2 (first occurrence) must NOT be flagged');
    }

    /**
     * AC2 / Edge: file with a DB-duplicate unit (same name + building already in DB).
     */
    public function test_validate_flags_unit_that_already_exists_in_database(): void
    {
        Storage::fake('local');

        // Pre-create a unit so it's in the DB
        Unit::factory()->create([
            'name' => 'DB-EXIST-001',
            'rf_building_id' => $this->building->id,
            'rf_community_id' => $this->community->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $file = $this->makeXlsxFile([
            ['Unit Name', 'Community', 'Building', 'Status'],
            ['DB-EXIST-001', 'Test Community', 'Building A', 'available'],  // exists in DB
            ['NEW-001', 'Test Community', 'Building A', 'available'],        // new — valid
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
        $this->assertSame(1, $validateResponse->json('valid_count'));

        $errorFields = array_column($validateResponse->json('errors'), 'field');
        $this->assertContains('name', $errorFields);
    }

    /**
     * AC2 / Edge: invalid status value is flagged as an error row.
     */
    public function test_validate_flags_invalid_status_value(): void
    {
        Storage::fake('local');

        $file = $this->makeXlsxFile([
            ['Unit Name', 'Community', 'Building', 'Status'],
            ['S-001', 'Test Community', 'Building A', 'bogus_status'],
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
        $this->assertGreaterThan(0, $validateResponse->json('error_count'));
        $errorMessages = array_column($validateResponse->json('errors'), 'message');
        $this->assertTrue(
            count(array_filter($errorMessages, fn ($m) => str_contains($m, 'bogus_status'))) > 0,
            'Error message should mention the invalid status value'
        );
    }

    /**
     * AC2 / Edge: row with a missing unit name is flagged (required field).
     */
    public function test_validate_flags_row_with_missing_unit_name(): void
    {
        Storage::fake('local');

        $file = $this->makeXlsxFile([
            ['Unit Name', 'Community', 'Building', 'Status'],
            ['', 'Test Community', 'Building A', 'available'],  // empty name
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

        $errorFields = array_column($validateResponse->json('errors'), 'field');
        $this->assertContains('name', $errorFields);
    }

    // ── AC2: execute partial-import (valid-only) ───────────────────────────────

    /**
     * AC2 — partial import: when file has mixed valid/invalid rows, executing with
     * import_valid_only=true skips the invalid rows and only persists valid ones.
     */
    public function test_execute_imports_only_valid_rows_when_file_has_errors(): void
    {
        Storage::fake('local');
        Queue::fake();

        $file = $this->makeXlsxFile([
            ['Unit Name', 'Community', 'Building', 'Status'],
            ['VALID-001', 'Test Community', 'Building A', 'available'],        // valid
            ['VALID-002', 'Test Community', 'Building A', 'available'],        // valid
            ['INVALID-003', 'Test Community', 'Nonexistent Building', 'available'], // bad building
        ]);

        $uploadResponse = $this->postJson('/units/import/upload', ['file' => $file]);
        $sessionId = $uploadResponse->json('import_session_id');

        // Validate first so validation_errors are stored in meta
        $this->postJson('/units/import/validate', [
            'import_session_id' => $sessionId,
            'mapping' => [
                'name' => 'Unit Name',
                'rf_community_id' => 'Community',
                'rf_building_id' => 'Building',
                'status' => 'Status',
            ],
        ]);

        // Execute import of valid rows only
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
        $this->assertSame(2, $executeResponse->json('success_count'));
        $this->assertGreaterThan(0, $executeResponse->json('error_count'));

        // Verify only valid units were created
        $this->assertDatabaseHas('rf_units', [
            'name' => 'VALID-001',
            'account_tenant_id' => $this->tenant->id,
        ]);
        $this->assertDatabaseHas('rf_units', [
            'name' => 'VALID-002',
            'account_tenant_id' => $this->tenant->id,
        ]);
        $this->assertDatabaseMissing('rf_units', [
            'name' => 'INVALID-003',
            'account_tenant_id' => $this->tenant->id,
        ]);
    }

    // ── Tenant isolation: progress endpoint ───────────────────────────────────

    /**
     * Tenant boundary: user from tenant A cannot view progress of tenant B's import session.
     */
    public function test_progress_endpoint_denies_access_to_other_tenants_session(): void
    {
        $otherTenant = Tenant::create(['name' => 'Other Tenant For Progress']);
        $otherSession = ExcelSheet::create([
            'type' => 'import',
            'import_type' => 'unit',
            'file_path' => 'unit-imports/other.xlsx',
            'file_name' => 'other.xlsx',
            'status' => 'queued',
            'total_rows' => 50,
            'success_count' => 0,
            'error_count' => 0,
            'account_tenant_id' => $otherTenant->id,
        ]);

        // Our user (tenant A) tries to read tenant B's progress
        $response = $this->getJson("/units/import/progress/{$otherSession->id}");

        $response->assertForbidden();
    }

    // ── ImportUnitsJob failure handling ───────────────────────────────────────

    /**
     * AC1 (non-functional) / Edge: if the job fails, the ExcelSheet status is set to "failed".
     */
    public function test_import_job_failed_callback_marks_session_as_failed(): void
    {
        $session = ExcelSheet::create([
            'type' => 'import',
            'import_type' => 'unit',
            'file_path' => 'unit-imports/fake.xlsx',
            'file_name' => 'fake.xlsx',
            'status' => 'queued',
            'total_rows' => 10,
            'success_count' => 0,
            'error_count' => 0,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $job = new ImportUnitsJob(
            excelSheet: $session,
            filePath: 'unit-imports/fake.xlsx',
            tenantId: (int) $this->tenant->id,
            mapping: ['name' => 'Unit Name'],
            validationErrors: [],
        );

        $job->failed(new \RuntimeException('Simulated failure'));

        $session->refresh();
        $this->assertSame('failed', $session->status);
    }

    // ── ExcelSheet session scoping ─────────────────────────────────────────────

    /**
     * Tenant boundary: validate endpoint must not accept a session ID from another tenant
     * even if the ExcelSheet record exists in the DB.
     * With scoped Rule::exists, the cross-tenant probe is rejected at validation time (422).
     */
    public function test_validate_with_another_tenants_session_returns_422(): void
    {
        $otherTenant = Tenant::create(['name' => 'Other Tenant For Validate']);
        $otherSession = ExcelSheet::create([
            'type' => 'import',
            'import_type' => 'unit',
            'file_path' => 'unit-imports/other.xlsx',
            'file_name' => 'other.xlsx',
            'status' => 'uploaded',
            'total_rows' => 5,
            'account_tenant_id' => $otherTenant->id,
        ]);

        $response = $this->postJson('/units/import/validate', [
            'import_session_id' => $otherSession->id,
            'mapping' => ['name' => 'Unit Name'],
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['import_session_id']);
    }

    /**
     * Tenant boundary: execute endpoint must not accept a session ID from another tenant.
     * With scoped Rule::exists, the cross-tenant probe is rejected at validation time (422).
     */
    public function test_execute_with_another_tenants_session_returns_422(): void
    {
        $otherTenant = Tenant::create(['name' => 'Other Tenant For Execute']);
        $otherSession = ExcelSheet::create([
            'type' => 'import',
            'import_type' => 'unit',
            'file_path' => 'unit-imports/other.xlsx',
            'file_name' => 'other.xlsx',
            'status' => 'validated',
            'total_rows' => 5,
            'account_tenant_id' => $otherTenant->id,
        ]);

        $response = $this->postJson('/units/import/execute', [
            'import_session_id' => $otherSession->id,
            'mapping' => ['name' => 'Unit Name'],
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['import_session_id']);
    }

    // ── Execute request validation ─────────────────────────────────────────────

    /**
     * Failure-path: execute without import_session_id returns 422.
     */
    public function test_execute_without_session_id_returns_422(): void
    {
        $response = $this->postJson('/units/import/execute', [
            'mapping' => ['name' => 'Unit Name'],
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['import_session_id']);
    }

    /**
     * Failure-path: execute without mapping returns 422.
     */
    public function test_execute_without_mapping_returns_422(): void
    {
        Storage::fake('local');

        $file = $this->makeXlsxFile([
            ['Unit Name', 'Building'],
            ['E-001', 'Building A'],
        ]);
        $uploadResponse = $this->postJson('/units/import/upload', ['file' => $file]);
        $sessionId = $uploadResponse->json('import_session_id');

        $response = $this->postJson('/units/import/execute', [
            'import_session_id' => $sessionId,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['mapping']);
    }

    // ── ExcelSheet record is updated after inline import ──────────────────────

    /**
     * Edge: After a successful inline import, the ExcelSheet record status is 'completed'
     * and success_count matches the number of created units.
     */
    public function test_excel_sheet_record_is_updated_to_completed_after_inline_import(): void
    {
        Storage::fake('local');
        Queue::fake();

        $file = $this->makeXlsxFile([
            ['Unit Name', 'Community', 'Building', 'Status'],
            ['TRACK-001', 'Test Community', 'Building A', 'available'],
            ['TRACK-002', 'Test Community', 'Building A', 'available'],
        ]);

        $uploadResponse = $this->postJson('/units/import/upload', ['file' => $file]);
        $sessionId = $uploadResponse->json('import_session_id');

        $this->postJson('/units/import/validate', [
            'import_session_id' => $sessionId,
            'mapping' => [
                'name' => 'Unit Name',
                'rf_community_id' => 'Community',
                'rf_building_id' => 'Building',
                'status' => 'Status',
            ],
        ]);

        $this->postJson('/units/import/execute', [
            'import_session_id' => $sessionId,
            'mapping' => [
                'name' => 'Unit Name',
                'rf_community_id' => 'Community',
                'rf_building_id' => 'Building',
                'status' => 'Status',
            ],
            'import_valid_only' => true,
        ]);

        $excelSheet = ExcelSheet::find($sessionId);
        $this->assertSame('completed', $excelSheet->status);
        $this->assertSame(2, $excelSheet->success_count);
        $this->assertSame(0, $excelSheet->error_count);
    }

    // ── Arabic / unicode unit names ────────────────────────────────────────────

    /**
     * Edge (Arabic/RTL): unit names containing Arabic characters are validated and imported correctly.
     */
    public function test_validate_accepts_arabic_unit_names(): void
    {
        Storage::fake('local');

        $file = $this->makeXlsxFile([
            ['Unit Name', 'Community', 'Building', 'Status'],
            ['وحدة-001', 'Test Community', 'Building A', 'available'],
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
        $this->assertSame(0, $validateResponse->json('error_count'), 'Arabic unit name should be valid');
        $this->assertSame(1, $validateResponse->json('valid_count'));
    }

    // ── Template download (authenticated) ─────────────────────────────────────

    /**
     * Failure-path: unauthenticated request to template download is rejected.
     */
    public function test_unauthenticated_user_cannot_download_template(): void
    {
        $this->app['auth']->forgetGuards();

        $response = $this->get('/units/import/template');

        // Web routes redirect unauthenticated requests
        $response->assertRedirect();
    }
}
