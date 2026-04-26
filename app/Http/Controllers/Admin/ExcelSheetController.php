<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExcelSheet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelSheetController extends Controller
{
    public function index(): Response
    {
        $sheets = ExcelSheet::query()
            ->withCount('imports')
            ->latest('id')
            ->paginate(15)
            ->through(fn (ExcelSheet $sheet): array => [
                'id' => $sheet->id,
                'import_type' => $sheet->import_type,
                'file_name' => $sheet->file_name,
                'status' => $sheet->status,
                'column_schema' => $sheet->column_schema,
                'imports_count' => $sheet->imports_count,
                'updated_at' => $sheet->updated_at?->toJSON(),
            ]);

        return Inertia::render('admin/documents/excel-sheets/Index', [
            'sheets' => $sheets,
            'importTypes' => [
                ['value' => 'resident', 'label' => 'Residents'],
                ['value' => 'owner', 'label' => 'Owners'],
                ['value' => 'professional', 'label' => 'Professionals'],
                ['value' => 'unit', 'label' => 'Units'],
                ['value' => 'lead', 'label' => 'Leads'],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'import_type' => ['required', 'string', Rule::in(['resident', 'owner', 'professional', 'unit', 'lead'])],
            'file_name' => ['required', 'string', 'max:255'],
            'column_schema' => ['required', 'array'],
            'column_schema.*.name' => ['required', 'string'],
            'column_schema.*.label_en' => ['required', 'string'],
            'column_schema.*.label_ar' => ['nullable', 'string'],
            'column_schema.*.required' => ['boolean'],
            'column_schema.*.type' => ['required', 'string'],
        ]);

        ExcelSheet::create([
            'account_tenant_id' => tenant('id'),
            'import_type' => $validated['import_type'],
            'file_name' => $validated['file_name'],
            'type' => 'import_template',
            'column_schema' => $validated['column_schema'],
            'status' => 'active',
        ]);

        return redirect()->route('admin.excel-sheets.index')
            ->with('success', __('Import template created.'));
    }

    public function downloadTemplate(ExcelSheet $excelSheet): StreamedResponse
    {
        $schema = $excelSheet->column_schema ?? [];
        $rows = [];
        $headers = [];
        $headersAr = [];

        foreach ($schema as $col) {
            $headers[] = $col['label_en'] ?? $col['name'];
            $headersAr[] = $col['label_ar'] ?? $col['label_en'] ?? $col['name'];
        }

        $rows[] = $headers;
        $rows[] = $headersAr;
        $rows[] = array_map(fn ($col) => $col['type'] === 'date' ? '2026-01-01' : ($col['type'] === 'number' ? '0' : 'example'), $schema);

        $csv = fopen('php://temp', 'r+');

        foreach ($rows as $row) {
            fputcsv($csv, $row);
        }

        rewind($csv);

        return response()->streamDownload(
            fn () => fpassthru($csv),
            ($excelSheet->file_name ?? 'template').'.csv',
            ['Content-Type' => 'text/csv']
        );
    }

    public function importHistory(ExcelSheet $excelSheet): Response
    {
        $imports = $excelSheet->imports()
            ->with('importer:id,name')
            ->latest('id')
            ->paginate(15)
            ->through(fn ($import): array => [
                'id' => $import->id,
                'imported_by_name' => $import->importer?->name,
                'imported_at' => $import->imported_at?->toJSON(),
                'total_rows' => $import->total_rows,
                'successful_rows' => $import->successful_rows,
                'failed_rows' => $import->failed_rows,
            ]);

        return Inertia::render('admin/documents/excel-sheets/History', [
            'sheet' => [
                'id' => $excelSheet->id,
                'import_type' => $excelSheet->import_type,
                'file_name' => $excelSheet->file_name,
            ],
            'imports' => $imports,
        ]);
    }
}
