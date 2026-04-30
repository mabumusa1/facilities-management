<?php

namespace App\Http\Controllers\Leasing;

use App\Http\Controllers\Controller;
use App\Models\ExcelSheet;
use App\Models\Lead;
use App\Models\Tenant;
use App\Services\Leasing\LeadImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadImportController extends Controller
{
    public function __construct(private readonly LeadImportService $importService) {}

    /**
     * GET leads/import/review — show the validation review page for an ExcelSheet session.
     */
    public function review(ExcelSheet $excelSheet): Response
    {
        $this->authorize('create', Lead::class);

        abort_unless(
            $excelSheet->account_tenant_id === Tenant::current()?->id,
            403,
        );

        abort_unless($excelSheet->type === 'leads', 404);

        return Inertia::render('documents/LeadsImportErrors', [
            'excelSheet' => [
                'id' => $excelSheet->id,
                'file_name' => $excelSheet->file_name,
                'total_rows' => $excelSheet->total_rows ?? 0,
                'valid_count' => $excelSheet->success_count ?? 0,
                'error_count' => $excelSheet->error_count ?? 0,
                'errors' => $excelSheet->error_details ?? [],
                'valid_rows' => $excelSheet->meta['valid_rows'] ?? [],
            ],
        ]);
    }

    /**
     * POST leads/import/preview — upload and parse file, redirect to review page.
     */
    public function preview(Request $request): RedirectResponse
    {
        $this->authorize('create', Lead::class);

        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:'.(LeadImportService::MAX_FILE_SIZE_MB * 1024),
            ],
        ]);

        $file = $request->file('file');
        $path = $file->store('lead-imports', 'local');

        $tenantId = Tenant::current()?->id;

        $result = $this->importService->parse($path, (int) $tenantId);

        if ($result['total_rows'] === 0) {
            return back()->withErrors([
                'file' => __('This file cannot be read. Please upload an Excel file (.xlsx or .xls) using the provided template.'),
            ]);
        }

        if ($result['total_rows'] > LeadImportService::MAX_ROWS) {
            return back()->withErrors([
                'file' => __('File exceeds :max rows. Please split your import into smaller files.', [
                    'max' => LeadImportService::MAX_ROWS,
                ]),
            ]);
        }

        // Persist the parsed result in an ExcelSheet record
        $excelSheet = ExcelSheet::create([
            'type' => 'leads',
            'import_type' => 'leads',
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'status' => $result['valid_count'] > 0 ? 'pending' : 'error',
            'total_rows' => $result['total_rows'],
            'success_count' => $result['valid_count'],
            'error_count' => $result['error_count'],
            'error_details' => $result['errors'],
            'account_tenant_id' => $tenantId,
            'meta' => ['valid_rows' => $result['valid_rows']],
        ]);

        // If all rows are valid, skip straight to confirm
        if ($result['error_count'] === 0 && $result['valid_count'] > 0) {
            return redirect()->route('leads.import.review', $excelSheet);
        }

        return redirect()->route('leads.import.review', $excelSheet);
    }

    /**
     * POST leads/import/{excelSheet}/confirm — insert valid rows and redirect to list.
     */
    public function confirm(Request $request, ExcelSheet $excelSheet): RedirectResponse
    {
        $this->authorize('create', Lead::class);

        abort_unless(
            $excelSheet->account_tenant_id === Tenant::current()?->id,
            403,
        );

        abort_unless($excelSheet->type === 'leads', 404);
        abort_unless(($excelSheet->success_count ?? 0) > 0, 422);

        $validRows = $excelSheet->meta['valid_rows'] ?? [];
        $tenantId = (int) Tenant::current()?->id;

        $imported = DB::transaction(function () use ($validRows, $tenantId): int {
            $statusId = $this->importService->getNewStatusId();

            return $this->importService->importRows($validRows, $tenantId, $statusId);
        });

        // Mark the ExcelSheet as complete
        $excelSheet->update(['status' => 'complete']);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __(':count leads imported successfully.', ['count' => $imported]),
        ]);

        return redirect()->route('leads.index', ['source_id' => LeadImportService::EXCEL_SOURCE_ID]);
    }

    /**
     * GET leads/import/{excelSheet}/error-report — download error rows as CSV.
     */
    public function errorReport(ExcelSheet $excelSheet): StreamedResponse
    {
        $this->authorize('create', Lead::class);

        abort_unless(
            $excelSheet->account_tenant_id === Tenant::current()?->id,
            403,
        );

        $errors = $excelSheet->error_details ?? [];
        $csvContent = $this->importService->buildErrorReport($errors);
        $fileName = 'error-report-'.$excelSheet->id.'.csv';

        return response()->streamDownload(
            function () use ($csvContent): void {
                echo $csvContent;
            },
            $fileName,
            ['Content-Type' => 'text/csv'],
        );
    }

    /**
     * GET leads/import/template — download the lead import Excel template.
     */
    public function template(): RedirectResponse
    {
        // Delegate to the existing legacy template route
        return redirect()->route('legacy.download-lead-excel');
    }
}
