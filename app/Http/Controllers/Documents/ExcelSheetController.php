<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\ExcelSheet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ExcelSheetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $sheets = ExcelSheet::query()
            ->latest()
            ->paginate($this->perPage($request));

        return response()->json([
            'data' => collect($sheets->items())->map(fn (ExcelSheet $sheet): array => [
                'id' => $sheet->id,
                'type' => $sheet->type,
                'file_path' => $sheet->file_path,
                'file_name' => $sheet->file_name,
                'status' => $sheet->status,
                'error_details' => $sheet->error_details,
                'rf_community_id' => $sheet->rf_community_id,
                'created_at' => $sheet->created_at?->toJSON(),
                'updated_at' => $sheet->updated_at?->toJSON(),
            ]),
            'meta' => $this->meta($sheets),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,xls,xlsx,ods'],
            'rf_community_id' => ['required', 'integer', 'exists:rf_communities,id'],
        ]);

        $sheet = $this->createSheetRecord(
            type: 'general',
            file: $request->file('file'),
            communityId: (int) $validated['rf_community_id'],
        );

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $sheet->id,
                    'status' => $sheet->status,
                ],
                'message' => __('Excel file uploaded successfully.'),
            ]);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Excel file uploaded successfully.'),
        ]);

        return back();
    }

    public function storeLand(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'rf_community_id' => ['required', 'integer', 'exists:rf_communities,id'],
            'file' => ['nullable', 'file', 'mimes:csv,xls,xlsx,ods'],
        ]);

        $sheet = $this->createSheetRecord(
            type: 'land',
            file: $request->file('file'),
            communityId: (int) $validated['rf_community_id'],
        );

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $sheet->id,
                    'status' => $sheet->status,
                ],
                'message' => __('Land import submitted successfully.'),
            ]);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Land import submitted successfully.'),
        ]);

        return back();
    }

    public function storeLeads(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'file' => ['nullable', 'file', 'mimes:csv,xls,xlsx,ods'],
        ]);

        $sheet = $this->createSheetRecord(
            type: 'leads',
            file: $request->file('file'),
            communityId: null,
        );

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $sheet->id,
                    'status' => $sheet->status,
                ],
                'message' => __('Leads import submitted successfully.'),
            ]);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Leads import submitted successfully.'),
        ]);

        return back();
    }

    public function leadsErrors(): Response
    {
        $errors = ExcelSheet::query()
            ->where('type', 'leads')
            ->where('status', 'error')
            ->latest()
            ->paginate(15);

        return Inertia::render('documents/LeadsImportErrors', [
            'errors' => $errors,
        ]);
    }

    private function perPage(Request $request): int
    {
        return min(max((int) $request->integer('per_page', 10), 1), 50);
    }

    /**
     * @return array<string, mixed>
     */
    private function meta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'from' => $paginator->firstItem(),
            'last_page' => $paginator->lastPage(),
            'path' => $paginator->path(),
            'per_page' => $paginator->perPage(),
            'to' => $paginator->lastItem(),
            'total' => $paginator->total(),
        ];
    }

    private function createSheetRecord(string $type, $file, ?int $communityId): ExcelSheet
    {
        $path = $file
            ? $file->store('imports/excel', 'public')
            : 'imports/excel/manual-entry';

        return ExcelSheet::create([
            'type' => $type,
            'file_path' => Storage::disk('public')->url($path),
            'file_name' => $file?->getClientOriginalName(),
            'status' => 'uploaded',
            'rf_community_id' => $communityId,
            'error_details' => null,
        ]);
    }
}
