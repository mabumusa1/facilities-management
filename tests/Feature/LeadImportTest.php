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
}
