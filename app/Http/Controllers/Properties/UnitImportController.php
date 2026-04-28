<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Jobs\ImportUnitsJob;
use App\Models\ExcelSheet;
use App\Models\Unit;
use App\Services\ColumnMapper;
use App\Services\ImportUnitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Multitenancy\Models\Tenant;

class UnitImportController extends Controller
{
    /**
     * Maximum rows to import synchronously (>50 → async job).
     */
    private const ASYNC_THRESHOLD = 50;

    /**
     * POST /units/import/upload
     *
     * Accepts a .xlsx file, stores it to temp disk, detects headers.
     * Returns: import_session_id, headers[], row_count
     */
    public function upload(Request $request, ImportUnitService $importService): JsonResponse
    {
        $this->authorize('import', Unit::class);

        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx', 'max:10240'],
        ]);

        $file = $validated['file'];
        $fileName = $file->getClientOriginalName();

        // Store to local (private) disk under unit-imports/
        $path = $file->store('unit-imports', 'local');

        // Parse headers from the uploaded file
        $headers = $importService->parseHeaders($path);
        $rowCount = $importService->countRows($path);

        // Auto-match headers to system fields
        $autoMapping = ColumnMapper::autoMatch($headers);

        // Create an ExcelSheet session record
        $excelSheet = ExcelSheet::create([
            'type' => 'import',
            'import_type' => 'unit',
            'file_path' => $path,
            'file_name' => $fileName,
            'status' => 'uploaded',
            'total_rows' => $rowCount,
            'account_tenant_id' => Tenant::current()?->id,
        ]);

        return response()->json([
            'import_session_id' => $excelSheet->id,
            'headers' => $headers,
            'row_count' => $rowCount,
            'auto_mapping' => $autoMapping,
        ]);
    }

    /**
     * POST /units/import/validate
     *
     * Validates all rows using the supplied mapping.
     * Returns per-row validation errors and summary counts.
     */
    public function validate(Request $request, ImportUnitService $importService): JsonResponse
    {
        $this->authorize('import', Unit::class);

        $data = $request->validate([
            'import_session_id' => [
                'required',
                'integer',
                Rule::exists('rf_excel_sheets', 'id')
                    ->where('account_tenant_id', Tenant::current()?->id)
                    ->where('import_type', 'unit'),
            ],
            'mapping' => ['required', 'array'],
            'mapping.*' => ['nullable', 'string'],
        ]);

        /** @var ExcelSheet $excelSheet */
        $excelSheet = ExcelSheet::where('id', $data['import_session_id'])
            ->where('account_tenant_id', Tenant::current()?->id)
            ->where('import_type', 'unit')
            ->firstOrFail();

        $result = $importService->validateRows(
            path: $excelSheet->file_path,
            mapping: $data['mapping'],
            tenantId: (int) Tenant::current()?->id,
        );

        // Persist error details to session record
        $excelSheet->update([
            'status' => 'validated',
            'total_rows' => $result['total_rows'],
            'error_count' => $result['error_count'],
            'meta' => ['validation_errors' => $result['errors'], 'mapping' => $data['mapping']],
        ]);

        return response()->json($result);
    }

    /**
     * POST /units/import/execute
     *
     * Executes the import. For ≤50 rows runs inline; for >50 dispatches a job.
     */
    public function execute(Request $request, ImportUnitService $importService): JsonResponse
    {
        $this->authorize('import', Unit::class);

        $data = $request->validate([
            'import_session_id' => [
                'required',
                'integer',
                Rule::exists('rf_excel_sheets', 'id')
                    ->where('account_tenant_id', Tenant::current()?->id)
                    ->where('import_type', 'unit'),
            ],
            'mapping' => ['required', 'array'],
            'mapping.*' => ['nullable', 'string'],
            'import_valid_only' => ['sometimes', 'boolean'],
        ]);

        /** @var ExcelSheet $excelSheet */
        $excelSheet = ExcelSheet::where('id', $data['import_session_id'])
            ->where('account_tenant_id', Tenant::current()?->id)
            ->where('import_type', 'unit')
            ->firstOrFail();

        $tenantId = (int) Tenant::current()?->id;
        $mapping = $data['mapping'];

        // Retrieve pre-validated errors from meta
        $validationErrors = $excelSheet->meta['validation_errors'] ?? [];

        $rowCount = $excelSheet->total_rows ?? 0;

        if ($rowCount > self::ASYNC_THRESHOLD) {
            // Dispatch async job
            $excelSheet->update(['status' => 'queued']);

            ImportUnitsJob::dispatch(
                excelSheet: $excelSheet,
                filePath: $excelSheet->file_path,
                tenantId: $tenantId,
                mapping: $mapping,
                validationErrors: $validationErrors,
            );

            return response()->json([
                'status' => 'queued',
                'import_session_id' => $excelSheet->id,
                'message' => 'Import queued. You will be notified on completion.',
            ]);
        }

        // Inline import for small files
        $excelSheet->update(['status' => 'processing']);

        $result = $importService->importRows(
            path: $excelSheet->file_path,
            mapping: $mapping,
            tenantId: $tenantId,
            excelSheet: $excelSheet,
            validationErrors: $validationErrors,
        );

        return response()->json([
            'status' => 'completed',
            'success_count' => $result['success_count'],
            'error_count' => $result['error_count'],
        ]);
    }

    /**
     * GET /units/import/progress/{id}
     *
     * Returns current import progress for async imports.
     * Uses a manual ID lookup (bypassing the global tenant scope) so that
     * cross-tenant requests receive 403 rather than 404.
     */
    public function progress(int $id): JsonResponse
    {
        $this->authorize('import', Unit::class);

        /** @var ExcelSheet|null $excelSheet */
        $excelSheet = ExcelSheet::withoutGlobalScope('account_tenant')->find($id);

        abort_if($excelSheet === null, 404);

        abort_unless(
            $excelSheet->account_tenant_id === (int) Tenant::current()?->id && $excelSheet->import_type === 'unit',
            403
        );

        return response()->json([
            'status' => $excelSheet->status,
            'total_rows' => $excelSheet->total_rows,
            'success_count' => $excelSheet->success_count,
            'error_count' => $excelSheet->error_count,
        ]);
    }
}
