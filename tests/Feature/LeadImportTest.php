<?php

namespace Tests\Feature;

use App\Enums\RolesEnum;
use App\Models\AccountMembership;
use App\Models\ExcelSheet;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Leasing\LeadImportService;
use Database\Seeders\LeadSourceSeeder;
use Database\Seeders\RbacSeeder;
use Database\Seeders\StatusSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class LeadImportTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenant;

    private User $adminUser;

    private Status $newStatus;

    private LeadSource $excelSource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(RbacSeeder::class);
        $this->seed(StatusSeeder::class);
        $this->seed(LeadSourceSeeder::class);

        $this->adminUser = User::factory()->create();
        $this->tenant = Tenant::create(['name' => 'Import Test Account']);

        AccountMembership::create([
            'user_id' => $this->adminUser->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);

        $this->adminUser->assignRole(RolesEnum::ACCOUNT_ADMINS->value);

        $this->newStatus = Status::where('type', 'lead')->where('name_en', 'New')->firstOrFail();
        $this->excelSource = LeadSource::where('id', LeadImportService::EXCEL_SOURCE_ID)->firstOrFail();

        $this->tenant->makeCurrent();

        Storage::fake('local');
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helper: create a real xlsx file
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * @param  list<list<string>>  $rows  First row is headers
     */
    private function makeXlsxFile(array $rows, string $fileName = 'leads.xlsx'): UploadedFile
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($rows as $rowIdx => $row) {
            foreach ($row as $colIdx => $value) {
                $sheet->setCellValueByColumnAndRow($colIdx + 1, $rowIdx + 1, $value);
            }
        }

        $tempPath = sys_get_temp_dir().'/'.uniqid('lead_import_').'_test.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return new UploadedFile(
            path: $tempPath,
            originalName: $fileName,
            mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            error: UPLOAD_ERR_OK,
            test: true,
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Scenario 1: upload valid file — redirects to review page
    // ─────────────────────────────────────────────────────────────────────────

    public function test_upload_valid_file_creates_excel_sheet_and_redirects_to_review(): void
    {
        $rows = [
            ['Name (EN)', 'Name (AR)', 'Phone', 'Email', 'Source', 'Notes'],
            ['Alice Smith', 'أليس', '501234561', 'alice@example.com', 'Direct', ''],
            ['Bob Jones', 'بوب', '501234562', 'bob@example.com', 'Direct', 'VIP'],
        ];

        $file = $this->makeXlsxFile($rows);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.preview'), ['file' => $file]);

        $response->assertRedirect();

        $sheet = ExcelSheet::where('type', 'leads')
            ->where('account_tenant_id', $this->tenant->id)
            ->latest()
            ->firstOrFail();

        $this->assertSame(2, $sheet->total_rows);
        $this->assertSame(2, $sheet->success_count);
        $this->assertSame(0, $sheet->error_count);
        $this->assertSame('pending', $sheet->status);

        $response->assertRedirect(route('leads.import.review', $sheet));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Scenario 2: upload mixed file — review page shows errors
    // ─────────────────────────────────────────────────────────────────────────

    public function test_upload_mixed_file_records_error_rows(): void
    {
        $rows = [
            ['Name (EN)', 'Name (AR)', 'Phone', 'Email', 'Source', 'Notes'],
            ['Alice Smith', 'أليس', '501234561', 'alice@example.com', 'Direct', ''],
            ['Bob Error', '', '', '', '', ''], // has a name but no phone — invalid, but not empty
        ];

        $file = $this->makeXlsxFile($rows);

        $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.preview'), ['file' => $file]);

        $sheet = ExcelSheet::where('type', 'leads')
            ->where('account_tenant_id', $this->tenant->id)
            ->latest()
            ->first();

        $this->assertNotNull($sheet);
        $this->assertSame(2, $sheet->total_rows);
        $this->assertSame(1, $sheet->success_count);
        $this->assertGreaterThan(0, $sheet->error_count);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Scenario 3: review page renders Inertia component with correct data
    // ─────────────────────────────────────────────────────────────────────────

    public function test_review_page_renders_for_valid_sheet(): void
    {
        $sheet = ExcelSheet::create([
            'type' => 'leads',
            'import_type' => 'leads',
            'file_path' => 'lead-imports/test.xlsx',
            'file_name' => 'test.xlsx',
            'status' => 'pending',
            'total_rows' => 2,
            'success_count' => 2,
            'error_count' => 0,
            'error_details' => [],
            'account_tenant_id' => $this->tenant->id,
            'meta' => [
                'valid_rows' => [
                    ['name_en' => 'Alice', 'name_ar' => null, 'phone_number' => '501234561', 'email' => null, 'notes' => null],
                ],
            ],
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('leads.import.review', $sheet));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('documents/LeadsImportErrors')
                ->has('excelSheet')
                ->where('excelSheet.id', $sheet->id)
                ->where('excelSheet.total_rows', 2)
                ->where('excelSheet.valid_count', 2)
                ->where('excelSheet.error_count', 0),
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Scenario 4: confirm import — inserts valid rows as leads
    // ─────────────────────────────────────────────────────────────────────────

    public function test_confirm_import_inserts_valid_leads_with_excel_source_and_new_status(): void
    {
        $sheet = ExcelSheet::create([
            'type' => 'leads',
            'import_type' => 'leads',
            'file_path' => 'lead-imports/test.xlsx',
            'file_name' => 'test.xlsx',
            'status' => 'pending',
            'total_rows' => 2,
            'success_count' => 2,
            'error_count' => 0,
            'error_details' => [],
            'account_tenant_id' => $this->tenant->id,
            'meta' => [
                'valid_rows' => [
                    ['name_en' => 'Alice Smith', 'name_ar' => null, 'phone_number' => '501234561', 'email' => 'alice@test.com', 'notes' => null],
                    ['name_en' => 'Bob Jones', 'name_ar' => null, 'phone_number' => '501234562', 'email' => null, 'notes' => 'VIP'],
                ],
            ],
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.confirm', $sheet));

        $response->assertRedirect(route('leads.index', ['source_id' => LeadImportService::EXCEL_SOURCE_ID]));

        $this->assertDatabaseHas('rf_leads', [
            'name_en' => 'Alice Smith',
            'phone_number' => '501234561',
            'source_id' => LeadImportService::EXCEL_SOURCE_ID,
            'status_id' => $this->newStatus->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $this->assertDatabaseHas('rf_leads', [
            'name_en' => 'Bob Jones',
            'phone_number' => '501234562',
            'source_id' => LeadImportService::EXCEL_SOURCE_ID,
            'status_id' => $this->newStatus->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $sheet->refresh();
        $this->assertSame('complete', $sheet->status);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Scenario 5: all-invalid file — upload saves zero valid rows
    // ─────────────────────────────────────────────────────────────────────────

    public function test_all_invalid_file_records_zero_valid_rows(): void
    {
        $rows = [
            ['Name (EN)', 'Name (AR)', 'Phone', 'Email', 'Source', 'Notes'],
            ['', '', '', 'not-an-email', '', ''],
            ['', '', '', 'also-bad', '', ''],
        ];

        $file = $this->makeXlsxFile($rows);

        $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.preview'), ['file' => $file]);

        $sheet = ExcelSheet::where('type', 'leads')
            ->where('account_tenant_id', $this->tenant->id)
            ->latest()
            ->first();

        $this->assertNotNull($sheet);
        $this->assertSame(0, $sheet->success_count);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Scenario 6: wrong file type is rejected by validation
    // ─────────────────────────────────────────────────────────────────────────

    public function test_non_excel_file_is_rejected_at_validation(): void
    {
        $fakeFile = UploadedFile::fake()->create('leads.pdf', 50, 'application/pdf');

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.preview'), ['file' => $fakeFile]);

        $response->assertSessionHasErrors('file');
        $this->assertDatabaseMissing('rf_excel_sheets', ['type' => 'leads', 'account_tenant_id' => $this->tenant->id]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Scenario 7: duplicate phone in upload batch — flagged as error
    // ─────────────────────────────────────────────────────────────────────────

    public function test_duplicate_phone_in_file_flagged_as_error(): void
    {
        $rows = [
            ['Name (EN)', 'Name (AR)', 'Phone', 'Email', 'Source', 'Notes'],
            ['Alice', '', '501234561', '', '', ''],
            ['Alice2', '', '501234561', '', '', ''], // same phone
        ];

        $file = $this->makeXlsxFile($rows);

        $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.preview'), ['file' => $file]);

        $sheet = ExcelSheet::where('type', 'leads')
            ->where('account_tenant_id', $this->tenant->id)
            ->latest()
            ->first();

        $this->assertNotNull($sheet);
        // Only the first row with the phone can be valid; second is a duplicate
        $this->assertSame(1, $sheet->success_count);
        $this->assertGreaterThan(0, $sheet->error_count);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Scenario 8: confirm blocked when no valid rows
    // ─────────────────────────────────────────────────────────────────────────

    public function test_confirm_returns_422_when_no_valid_rows(): void
    {
        $sheet = ExcelSheet::create([
            'type' => 'leads',
            'import_type' => 'leads',
            'file_path' => 'lead-imports/test.xlsx',
            'file_name' => 'test.xlsx',
            'status' => 'error',
            'total_rows' => 2,
            'success_count' => 0,
            'error_count' => 2,
            'error_details' => [['row' => 2, 'field' => 'Phone', 'message' => 'Required']],
            'account_tenant_id' => $this->tenant->id,
            'meta' => ['valid_rows' => []],
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.confirm', $sheet));

        $response->assertStatus(422);

        // No leads should have been created
        $this->assertDatabaseMissing('rf_leads', [
            'account_tenant_id' => $this->tenant->id,
            'source_id' => LeadImportService::EXCEL_SOURCE_ID,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Scenario 9: cross-tenant isolation — cannot confirm another tenant's sheet
    // ─────────────────────────────────────────────────────────────────────────

    public function test_cross_tenant_confirm_is_blocked(): void
    {
        $otherTenant = Tenant::create(['name' => 'Other Tenant']);

        $sheet = ExcelSheet::create([
            'type' => 'leads',
            'import_type' => 'leads',
            'file_path' => 'lead-imports/other.xlsx',
            'file_name' => 'other.xlsx',
            'status' => 'pending',
            'total_rows' => 1,
            'success_count' => 1,
            'error_count' => 0,
            'error_details' => [],
            'account_tenant_id' => $otherTenant->id,
            'meta' => [
                'valid_rows' => [
                    ['name_en' => 'Evil', 'name_ar' => null, 'phone_number' => '500000000', 'email' => null, 'notes' => null],
                ],
            ],
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.confirm', $sheet));

        // ExcelSheet global scope (BelongsToAccountTenant) filters cross-tenant records at the
        // DB level, so route model binding returns 404 (resource not found in current tenant).
        $response->assertNotFound();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Scenario 10: error report download returns CSV
    // ─────────────────────────────────────────────────────────────────────────

    public function test_error_report_download_returns_csv_for_authorized_user(): void
    {
        $sheet = ExcelSheet::create([
            'type' => 'leads',
            'import_type' => 'leads',
            'file_path' => 'lead-imports/test.xlsx',
            'file_name' => 'test.xlsx',
            'status' => 'error',
            'total_rows' => 2,
            'success_count' => 0,
            'error_count' => 2,
            'error_details' => [
                ['row' => 2, 'field' => 'Phone', 'message' => 'Phone is required.'],
                ['row' => 3, 'field' => 'Name (EN)', 'message' => 'At least one name is required.'],
            ],
            'account_tenant_id' => $this->tenant->id,
            'meta' => ['valid_rows' => []],
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('leads.import.error-report', $sheet));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Scenario 11: duplicate phone against existing DB leads — flagged as error
    // ─────────────────────────────────────────────────────────────────────────

    public function test_phone_already_existing_in_tenant_leads_flagged_as_duplicate(): void
    {
        // Pre-create a lead with this phone in the tenant
        Lead::create([
            'name_en' => 'Existing Lead',
            'phone_number' => '505050505',
            'source_id' => $this->excelSource->id,
            'status_id' => $this->newStatus->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $rows = [
            ['Name (EN)', 'Name (AR)', 'Phone', 'Email', 'Source', 'Notes'],
            ['New Lead', '', '505050505', '', '', ''], // duplicate phone
        ];

        $file = $this->makeXlsxFile($rows);

        $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.preview'), ['file' => $file]);

        $sheet = ExcelSheet::where('type', 'leads')
            ->where('account_tenant_id', $this->tenant->id)
            ->latest()
            ->first();

        $this->assertNotNull($sheet);
        $this->assertSame(0, $sheet->success_count);
        $this->assertSame(1, $sheet->error_count);
    }

    // =========================================================================
    // QA GAP TESTS — failure paths + edge cases
    // =========================================================================

    // ─────────────────────────────────────────────────────────────────────────
    // Authorization: user without leads.CREATE (dependents role) is denied
    // ─────────────────────────────────────────────────────────────────────────

    public function test_user_without_leads_create_cannot_upload_preview(): void
    {
        $noPermUser = User::factory()->create();
        AccountMembership::create([
            'user_id' => $noPermUser->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'dependents',
        ]);
        $noPermUser->assignRole(RolesEnum::DEPENDENTS->value);

        $file = $this->makeXlsxFile([
            ['Name (EN)', 'Name (AR)', 'Phone', 'Email', 'Source', 'Notes'],
            ['Alice', '', '501234561', '', '', ''],
        ]);

        $response = $this
            ->actingAs($noPermUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.preview'), ['file' => $file]);

        $response->assertForbidden();
    }

    public function test_user_without_leads_create_cannot_access_review_page(): void
    {
        $noPermUser = User::factory()->create();
        AccountMembership::create([
            'user_id' => $noPermUser->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'dependents',
        ]);
        $noPermUser->assignRole(RolesEnum::DEPENDENTS->value);

        $sheet = ExcelSheet::create([
            'type' => 'leads',
            'import_type' => 'leads',
            'file_path' => 'lead-imports/test.xlsx',
            'file_name' => 'test.xlsx',
            'status' => 'pending',
            'total_rows' => 1,
            'success_count' => 1,
            'error_count' => 0,
            'error_details' => [],
            'account_tenant_id' => $this->tenant->id,
            'meta' => ['valid_rows' => []],
        ]);

        $response = $this
            ->actingAs($noPermUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('leads.import.review', $sheet));

        $response->assertForbidden();
    }

    public function test_user_without_leads_create_cannot_confirm_import(): void
    {
        $noPermUser = User::factory()->create();
        AccountMembership::create([
            'user_id' => $noPermUser->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'dependents',
        ]);
        $noPermUser->assignRole(RolesEnum::DEPENDENTS->value);

        $sheet = ExcelSheet::create([
            'type' => 'leads',
            'import_type' => 'leads',
            'file_path' => 'lead-imports/test.xlsx',
            'file_name' => 'test.xlsx',
            'status' => 'pending',
            'total_rows' => 1,
            'success_count' => 1,
            'error_count' => 0,
            'error_details' => [],
            'account_tenant_id' => $this->tenant->id,
            'meta' => [
                'valid_rows' => [
                    ['name_en' => 'Alice', 'name_ar' => null, 'phone_number' => '501234561', 'email' => null, 'notes' => null],
                ],
            ],
        ]);

        $response = $this
            ->actingAs($noPermUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.confirm', $sheet));

        $response->assertForbidden();
    }

    public function test_user_without_leads_create_cannot_download_error_report(): void
    {
        $noPermUser = User::factory()->create();
        AccountMembership::create([
            'user_id' => $noPermUser->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'dependents',
        ]);
        $noPermUser->assignRole(RolesEnum::DEPENDENTS->value);

        $sheet = ExcelSheet::create([
            'type' => 'leads',
            'import_type' => 'leads',
            'file_path' => 'lead-imports/test.xlsx',
            'file_name' => 'test.xlsx',
            'status' => 'error',
            'total_rows' => 1,
            'success_count' => 0,
            'error_count' => 1,
            'error_details' => [['row' => 2, 'field' => 'Phone', 'message' => 'Required']],
            'account_tenant_id' => $this->tenant->id,
            'meta' => ['valid_rows' => []],
        ]);

        $response = $this
            ->actingAs($noPermUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('leads.import.error-report', $sheet));

        $response->assertForbidden();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Authentication: unauthenticated requests are redirected to login
    // ─────────────────────────────────────────────────────────────────────────

    public function test_unauthenticated_user_cannot_upload_preview(): void
    {
        $file = $this->makeXlsxFile([
            ['Name (EN)', 'Name (AR)', 'Phone', 'Email', 'Source', 'Notes'],
            ['Alice', '', '501234561', '', '', ''],
        ]);

        $response = $this->post(route('leads.import.preview'), ['file' => $file]);

        $response->assertRedirect(route('login'));
    }

    public function test_unauthenticated_user_cannot_confirm_import(): void
    {
        $sheet = ExcelSheet::create([
            'type' => 'leads',
            'import_type' => 'leads',
            'file_path' => 'lead-imports/test.xlsx',
            'file_name' => 'test.xlsx',
            'status' => 'pending',
            'total_rows' => 1,
            'success_count' => 1,
            'error_count' => 0,
            'error_details' => [],
            'account_tenant_id' => $this->tenant->id,
            'meta' => ['valid_rows' => []],
        ]);

        $response = $this->post(route('leads.import.confirm', $sheet));

        $response->assertRedirect(route('login'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // File-shape failure: CSV file is rejected (only xlsx/xls accepted)
    // ─────────────────────────────────────────────────────────────────────────

    public function test_csv_file_is_rejected_at_validation(): void
    {
        $fakeFile = UploadedFile::fake()->create('leads.csv', 10, 'text/csv');

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.preview'), ['file' => $fakeFile]);

        $response->assertSessionHasErrors('file');
        $this->assertDatabaseMissing('rf_excel_sheets', ['type' => 'leads', 'account_tenant_id' => $this->tenant->id]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // File-shape failure: no file uploaded
    // ─────────────────────────────────────────────────────────────────────────

    public function test_missing_file_returns_validation_error(): void
    {
        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.preview'), []);

        $response->assertSessionHasErrors('file');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // File-shape failure: file exceeds maximum size (5 MB)
    // ─────────────────────────────────────────────────────────────────────────

    public function test_file_exceeding_size_limit_is_rejected(): void
    {
        // Create a fake file larger than MAX_FILE_SIZE_MB (5 MB = 5120 KB)
        $oversizedFile = UploadedFile::fake()->create(
            'big.xlsx',
            (LeadImportService::MAX_FILE_SIZE_MB * 1024) + 1,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        );

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.preview'), ['file' => $oversizedFile]);

        $response->assertSessionHasErrors('file');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // File-shape failure: header row only — no data rows
    // ─────────────────────────────────────────────────────────────────────────

    public function test_header_only_file_redirects_back_with_error(): void
    {
        $rows = [
            ['Name (EN)', 'Name (AR)', 'Phone', 'Email', 'Source', 'Notes'],
        ];

        $file = $this->makeXlsxFile($rows);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.preview'), ['file' => $file]);

        // Parser returns total_rows=0 when no data rows exist → back with file error
        $response->assertRedirect();
        $response->assertSessionHasErrors('file');
        $this->assertDatabaseMissing('rf_excel_sheets', ['type' => 'leads', 'account_tenant_id' => $this->tenant->id]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // File-shape failure: missing required 'Phone' column in header
    // ─────────────────────────────────────────────────────────────────────────

    public function test_file_missing_phone_column_redirects_with_error(): void
    {
        $rows = [
            ['Name (EN)', 'Name (AR)', 'Email', 'Source', 'Notes'], // no Phone column
            ['Alice', '', 'alice@example.com', 'Direct', ''],
        ];

        $file = $this->makeXlsxFile($rows);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.preview'), ['file' => $file]);

        // hasRequiredHeaders() requires 'phone' — returns emptyResult → total_rows=0 → back with error
        $response->assertRedirect();
        $response->assertSessionHasErrors('file');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // File-shape failure: extra unknown columns are silently ignored
    // ─────────────────────────────────────────────────────────────────────────

    public function test_extra_unknown_columns_are_ignored_and_file_processes_normally(): void
    {
        $rows = [
            ['Name (EN)', 'Name (AR)', 'Phone', 'Email', 'Source', 'Notes', 'Internal Code', 'Rating'],
            ['Alice Smith', '', '501234561', '', '', '', 'ABC123', '5'],
        ];

        $file = $this->makeXlsxFile($rows);

        $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.preview'), ['file' => $file]);

        $sheet = ExcelSheet::where('type', 'leads')
            ->where('account_tenant_id', $this->tenant->id)
            ->latest()
            ->first();

        $this->assertNotNull($sheet);
        $this->assertSame(1, $sheet->success_count);
        $this->assertSame(0, $sheet->error_count);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Row validation: phone number too long (> 50 chars)
    // ─────────────────────────────────────────────────────────────────────────

    public function test_phone_too_long_is_flagged_as_error(): void
    {
        $longPhone = str_repeat('5', 51);

        $rows = [
            ['Name (EN)', 'Name (AR)', 'Phone', 'Email', 'Source', 'Notes'],
            ['Alice', '', $longPhone, '', '', ''],
        ];

        $file = $this->makeXlsxFile($rows);

        $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.preview'), ['file' => $file]);

        $sheet = ExcelSheet::where('type', 'leads')
            ->where('account_tenant_id', $this->tenant->id)
            ->latest()
            ->first();

        $this->assertNotNull($sheet);
        $this->assertSame(0, $sheet->success_count);
        $this->assertSame(1, $sheet->error_count);

        $errorFields = array_column($sheet->error_details, 'field');
        $this->assertContains('Phone', $errorFields);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Row validation: Arabic-only name (no EN) is valid
    // ─────────────────────────────────────────────────────────────────────────

    public function test_arabic_only_name_with_valid_phone_is_accepted(): void
    {
        $rows = [
            ['Name (EN)', 'Name (AR)', 'Phone', 'Email', 'Source', 'Notes'],
            ['', 'أليس سميث', '501234561', '', '', ''],
        ];

        $file = $this->makeXlsxFile($rows);

        $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.preview'), ['file' => $file]);

        $sheet = ExcelSheet::where('type', 'leads')
            ->where('account_tenant_id', $this->tenant->id)
            ->latest()
            ->first();

        $this->assertNotNull($sheet);
        $this->assertSame(1, $sheet->success_count);
        $this->assertSame(0, $sheet->error_count);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Row validation: invalid email format is flagged as error
    // ─────────────────────────────────────────────────────────────────────────

    public function test_invalid_email_format_is_flagged_as_error(): void
    {
        $rows = [
            ['Name (EN)', 'Name (AR)', 'Phone', 'Email', 'Source', 'Notes'],
            ['Bob', '', '501234561', 'not-an-email', '', ''],
        ];

        $file = $this->makeXlsxFile($rows);

        $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.preview'), ['file' => $file]);

        $sheet = ExcelSheet::where('type', 'leads')
            ->where('account_tenant_id', $this->tenant->id)
            ->latest()
            ->first();

        $this->assertNotNull($sheet);
        $this->assertSame(0, $sheet->success_count);
        $this->assertSame(1, $sheet->error_count);

        $errorFields = array_column($sheet->error_details, 'field');
        $this->assertContains('Email', $errorFields);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Row validation: intra-file duplicate email flagged as error
    // ─────────────────────────────────────────────────────────────────────────

    public function test_intra_file_duplicate_email_is_flagged_as_error(): void
    {
        $rows = [
            ['Name (EN)', 'Name (AR)', 'Phone', 'Email', 'Source', 'Notes'],
            ['Alice', '', '501234561', 'alice@example.com', '', ''],
            ['Alice2', '', '501234562', 'alice@example.com', '', ''], // same email
        ];

        $file = $this->makeXlsxFile($rows);

        $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.preview'), ['file' => $file]);

        $sheet = ExcelSheet::where('type', 'leads')
            ->where('account_tenant_id', $this->tenant->id)
            ->latest()
            ->first();

        $this->assertNotNull($sheet);
        // First row valid; second row duplicate email → error
        $this->assertSame(1, $sheet->success_count);
        $this->assertGreaterThan(0, $sheet->error_count);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Row validation: DB-level duplicate email against existing tenant lead
    // ─────────────────────────────────────────────────────────────────────────

    public function test_email_already_existing_in_tenant_leads_flagged_as_duplicate(): void
    {
        Lead::create([
            'name_en' => 'Existing Lead',
            'phone_number' => '501111111',
            'email' => 'existing@example.com',
            'source_id' => $this->excelSource->id,
            'status_id' => $this->newStatus->id,
            'account_tenant_id' => $this->tenant->id,
        ]);

        $rows = [
            ['Name (EN)', 'Name (AR)', 'Phone', 'Email', 'Source', 'Notes'],
            ['New Lead', '', '502222222', 'existing@example.com', '', ''],
        ];

        $file = $this->makeXlsxFile($rows);

        $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.preview'), ['file' => $file]);

        $sheet = ExcelSheet::where('type', 'leads')
            ->where('account_tenant_id', $this->tenant->id)
            ->latest()
            ->first();

        $this->assertNotNull($sheet);
        $this->assertSame(0, $sheet->success_count);
        $this->assertSame(1, $sheet->error_count);

        $errorFields = array_column($sheet->error_details, 'field');
        $this->assertContains('Email', $errorFields);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // File exceeds MAX_ROWS limit (500) → rejected with file error message
    // ─────────────────────────────────────────────────────────────────────────

    public function test_file_exceeding_max_rows_is_rejected_with_error(): void
    {
        // Build a valid header + 501 data rows
        $rows = [['Name (EN)', 'Name (AR)', 'Phone', 'Email', 'Source', 'Notes']];
        for ($i = 1; $i <= LeadImportService::MAX_ROWS + 1; $i++) {
            $rows[] = ["Lead {$i}", '', "50{$i}", '', '', ''];
        }

        $file = $this->makeXlsxFile($rows);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.preview'), ['file' => $file]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('file');
        $this->assertDatabaseMissing('rf_excel_sheets', ['type' => 'leads', 'account_tenant_id' => $this->tenant->id]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Confirm: already-confirmed sheet (status = 'complete') — idempotency BUG
    //
    // BUG: The controller's guard is `abort_unless($sheet->success_count > 0, 422)`.
    // A complete sheet still has success_count > 0, so re-confirming re-inserts all
    // valid rows a second time (duplicates). The controller must also check
    // `$sheet->status !== 'complete'` to be idempotent.
    // This test documents the current (broken) behaviour; the Engineer must fix.
    // ─────────────────────────────────────────────────────────────────────────

    public function test_confirming_already_complete_sheet_is_blocked(): void
    {
        $sheet = ExcelSheet::create([
            'type' => 'leads',
            'import_type' => 'leads',
            'file_path' => 'lead-imports/test.xlsx',
            'file_name' => 'test.xlsx',
            'status' => 'complete',
            'total_rows' => 1,
            'success_count' => 1,
            'error_count' => 0,
            'error_details' => [],
            'account_tenant_id' => $this->tenant->id,
            'meta' => [
                'valid_rows' => [
                    ['name_en' => 'Already Imported', 'name_ar' => null, 'phone_number' => '509000001', 'email' => null, 'notes' => null],
                ],
            ],
        ]);

        $leadCountBefore = Lead::where('account_tenant_id', $this->tenant->id)->count();

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.confirm', $sheet));

        $response->assertStatus(422);

        // No new leads must have been inserted
        $this->assertSame($leadCountBefore, Lead::where('account_tenant_id', $this->tenant->id)->count());
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Confirm: ExcelSheet of wrong type ('documents') is 404
    // ─────────────────────────────────────────────────────────────────────────

    public function test_confirm_wrong_excel_sheet_type_returns_404(): void
    {
        $sheet = ExcelSheet::create([
            'type' => 'documents', // not 'leads'
            'import_type' => 'documents',
            'file_path' => 'uploads/other.xlsx',
            'file_name' => 'other.xlsx',
            'status' => 'pending',
            'total_rows' => 1,
            'success_count' => 1,
            'error_count' => 0,
            'error_details' => [],
            'account_tenant_id' => $this->tenant->id,
            'meta' => ['valid_rows' => []],
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.confirm', $sheet));

        $response->assertNotFound();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Cross-tenant: review page for another tenant's sheet is blocked
    // ─────────────────────────────────────────────────────────────────────────

    public function test_cross_tenant_review_is_blocked(): void
    {
        $otherTenant = Tenant::create(['name' => 'Other Tenant For Review']);

        $sheet = ExcelSheet::create([
            'type' => 'leads',
            'import_type' => 'leads',
            'file_path' => 'lead-imports/other.xlsx',
            'file_name' => 'other.xlsx',
            'status' => 'pending',
            'total_rows' => 1,
            'success_count' => 1,
            'error_count' => 0,
            'error_details' => [],
            'account_tenant_id' => $otherTenant->id,
            'meta' => ['valid_rows' => []],
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('leads.import.review', $sheet));

        // BelongsToAccountTenant global scope returns 404 at route model binding
        $response->assertNotFound();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Cross-tenant: error report for another tenant's sheet is blocked
    // ─────────────────────────────────────────────────────────────────────────

    public function test_cross_tenant_error_report_is_blocked(): void
    {
        $otherTenant = Tenant::create(['name' => 'Other Tenant For Error Report']);

        $sheet = ExcelSheet::create([
            'type' => 'leads',
            'import_type' => 'leads',
            'file_path' => 'lead-imports/other.xlsx',
            'file_name' => 'other.xlsx',
            'status' => 'error',
            'total_rows' => 1,
            'success_count' => 0,
            'error_count' => 1,
            'error_details' => [['row' => 2, 'field' => 'Phone', 'message' => 'Required']],
            'account_tenant_id' => $otherTenant->id,
            'meta' => ['valid_rows' => []],
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('leads.import.error-report', $sheet));

        $response->assertNotFound();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Error report: all-valid sheet has no errors — CSV has header only
    // ─────────────────────────────────────────────────────────────────────────

    public function test_error_report_for_all_valid_sheet_returns_header_only_csv(): void
    {
        $sheet = ExcelSheet::create([
            'type' => 'leads',
            'import_type' => 'leads',
            'file_path' => 'lead-imports/test.xlsx',
            'file_name' => 'test.xlsx',
            'status' => 'pending',
            'total_rows' => 1,
            'success_count' => 1,
            'error_count' => 0,
            'error_details' => [],
            'account_tenant_id' => $this->tenant->id,
            'meta' => ['valid_rows' => []],
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('leads.import.error-report', $sheet));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // With no errors, only the CSV header row should be present
        $content = $response->streamedContent();
        $this->assertStringContainsString('Row,Field,Error', $content);
        // Ensure no data rows follow the header
        $lines = array_filter(explode("\n", trim($content)));
        $this->assertCount(1, $lines);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Error report: content includes correct row numbers and field names
    // ─────────────────────────────────────────────────────────────────────────

    public function test_error_report_content_has_correct_row_numbers_and_fields(): void
    {
        $sheet = ExcelSheet::create([
            'type' => 'leads',
            'import_type' => 'leads',
            'file_path' => 'lead-imports/test.xlsx',
            'file_name' => 'test.xlsx',
            'status' => 'error',
            'total_rows' => 3,
            'success_count' => 1,
            'error_count' => 2,
            'error_details' => [
                ['row' => 2, 'field' => 'Phone', 'message' => 'Phone is required.'],
                ['row' => 3, 'field' => 'Name (EN)', 'message' => 'At least one of Name (EN) or Name (AR) is required.'],
            ],
            'account_tenant_id' => $this->tenant->id,
            'meta' => ['valid_rows' => []],
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('leads.import.error-report', $sheet));

        $response->assertOk();
        $content = $response->streamedContent();

        $this->assertStringContainsString('2,Phone', $content);
        $this->assertStringContainsString('3,Name (EN)', $content);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Confirm: inserted leads belong to the current tenant (source verified)
    // ─────────────────────────────────────────────────────────────────────────

    public function test_confirmed_leads_are_scoped_to_current_tenant_with_excel_source(): void
    {
        $sheet = ExcelSheet::create([
            'type' => 'leads',
            'import_type' => 'leads',
            'file_path' => 'lead-imports/test.xlsx',
            'file_name' => 'test.xlsx',
            'status' => 'pending',
            'total_rows' => 1,
            'success_count' => 1,
            'error_count' => 0,
            'error_details' => [],
            'account_tenant_id' => $this->tenant->id,
            'meta' => [
                'valid_rows' => [
                    ['name_en' => 'Scope Test Lead', 'name_ar' => null, 'phone_number' => '509876543', 'email' => null, 'notes' => null],
                ],
            ],
        ]);

        $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('leads.import.confirm', $sheet));

        $this->assertDatabaseHas('rf_leads', [
            'name_en' => 'Scope Test Lead',
            'phone_number' => '509876543',
            'account_tenant_id' => $this->tenant->id,
            'source_id' => LeadImportService::EXCEL_SOURCE_ID,
        ]);

        // Ensure it was NOT created in any other tenant scope
        $lead = Lead::where('phone_number', '509876543')->first();
        $this->assertNotNull($lead);
        $this->assertSame($this->tenant->id, $lead->account_tenant_id);
    }
}
