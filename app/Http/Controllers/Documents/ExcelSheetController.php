<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\ExcelSheet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ExcelSheetController extends Controller
{
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
