<?php

namespace App\Services;

use App\Imports\RawSheetImport;
use App\Models\Building;
use App\Models\Community;
use App\Models\ExcelSheet;
use App\Models\Status;
use App\Models\Unit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ImportUnitService
{
    /**
     * Parse headers from an uploaded Excel file.
     *
     * @return string[]
     */
    public function parseHeaders(string $path): array
    {
        $rows = Excel::toArray(new RawSheetImport, $path, 'local');

        if (empty($rows) || empty($rows[0])) {
            return [];
        }

        return array_map(
            fn ($cell) => is_string($cell) ? trim($cell) : (string) $cell,
            $rows[0][0] ?? []
        );
    }

    /**
     * Count data rows (excluding the header row).
     */
    public function countRows(string $path): int
    {
        $rows = Excel::toArray(new RawSheetImport, $path, 'local');

        if (empty($rows) || empty($rows[0])) {
            return 0;
        }

        return max(0, count($rows[0]) - 1);
    }

    /**
     * Validate all rows in the file against the provided column mapping.
     *
     * @param  array<string, string>  $mapping  System field => Excel header name
     * @return array{total_rows: int, valid_count: int, error_count: int, errors: list<array{row: int, field: string, message: string}>}
     */
    public function validateRows(string $path, array $mapping, int $tenantId): array
    {
        $rows = Excel::toArray(new RawSheetImport, $path, 'local');

        if (empty($rows) || empty($rows[0])) {
            return ['total_rows' => 0, 'valid_count' => 0, 'error_count' => 0, 'errors' => []];
        }

        $sheet = $rows[0];
        $headers = array_map(fn ($h) => is_string($h) ? trim($h) : (string) $h, $sheet[0]);

        // Build column index map: system_field => column_index
        $columnIndex = [];
        foreach ($mapping as $systemField => $excelHeader) {
            if ($excelHeader === null || $excelHeader === '') {
                continue;
            }
            $idx = array_search($excelHeader, $headers, true);
            if ($idx !== false) {
                $columnIndex[$systemField] = $idx;
            }
        }

        // Preload tenant-scoped lookup tables for efficient validation
        $communities = Community::where('account_tenant_id', $tenantId)
            ->pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [strtolower(trim($name)) => $id]);

        $buildings = Building::where('account_tenant_id', $tenantId)
            ->pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [strtolower(trim($name)) => $id]);

        $validStatuses = Status::where('type', 'unit')
            ->pluck('name')
            ->map(fn ($name) => strtolower(trim($name)));

        // Track existing unit names per building for duplicate detection
        $existingUnits = Unit::where('account_tenant_id', $tenantId)
            ->whereNotNull('rf_building_id')
            ->select('rf_building_id', 'name')
            ->get()
            ->groupBy('rf_building_id')
            ->map(fn (Collection $units) => $units->pluck('name')->map(fn ($n) => strtolower(trim($n)))->values());

        $errors = [];
        $seenInFile = []; // track unit name+building within this file to catch file-level duplicates

        $dataRows = array_slice($sheet, 1);
        $rowCount = count($dataRows);

        foreach ($dataRows as $rowIdx => $row) {
            $lineNumber = $rowIdx + 2; // 1-indexed, +1 for header
            $getValue = function (string $field) use ($row, $columnIndex): ?string {
                if (! isset($columnIndex[$field])) {
                    return null;
                }
                $val = $row[$columnIndex[$field]] ?? null;

                return $val !== null && $val !== '' ? trim((string) $val) : null;
            };

            $unitName = $getValue('name');
            $communityName = $getValue('rf_community_id');
            $buildingName = $getValue('rf_building_id');
            $statusValue = $getValue('status');

            // Validate unit name
            if ($unitName === null || $unitName === '') {
                $errors[] = ['row' => $lineNumber, 'field' => 'name', 'message' => 'Unit name is required'];

                continue; // Can't check duplicates without a name
            }

            // Validate community
            $communityId = null;
            if ($communityName !== null) {
                $communityId = $communities->get(strtolower($communityName));
                if ($communityId === null) {
                    $errors[] = ['row' => $lineNumber, 'field' => 'rf_community_id', 'message' => "Community \"{$communityName}\" not found"];
                }
            }

            // Validate building
            $buildingId = null;
            if ($buildingName !== null) {
                $buildingId = $buildings->get(strtolower($buildingName));
                if ($buildingId === null) {
                    $errors[] = ['row' => $lineNumber, 'field' => 'rf_building_id', 'message' => "Building \"{$buildingName}\" not found"];
                }
            }

            // Validate status
            if ($statusValue !== null && ! $validStatuses->contains(strtolower($statusValue))) {
                $errors[] = ['row' => $lineNumber, 'field' => 'status', 'message' => "Status \"{$statusValue}\" is not valid"];
            }

            // Check for duplicates within building
            if ($buildingId !== null) {
                $key = "{$buildingId}:{$unitName}";
                $lowerName = strtolower($unitName);

                // Check against DB
                if (isset($existingUnits[$buildingId]) && $existingUnits[$buildingId]->contains($lowerName)) {
                    $errors[] = ['row' => $lineNumber, 'field' => 'name', 'message' => 'Duplicate unit number in this building'];
                }

                // Check against other rows in the same file
                if (isset($seenInFile[$key])) {
                    $errors[] = ['row' => $lineNumber, 'field' => 'name', 'message' => 'Duplicate unit number in this building'];
                } else {
                    $seenInFile[$key] = true;
                }
            }
        }

        $errorCount = count(array_unique(array_column($errors, 'row')));
        $validCount = $rowCount - $errorCount;

        return [
            'total_rows' => $rowCount,
            'valid_count' => max(0, $validCount),
            'error_count' => $errorCount,
            'errors' => $errors,
        ];
    }

    /**
     * Import valid rows from the file. Creates units in the database.
     * Returns array with success_count and error_count.
     *
     * @param  array<string, string>  $mapping
     * @param  list<array{row: int, field: string, message: string}>  $validationErrors
     * @return array{success_count: int, error_count: int}
     */
    public function importRows(
        string $path,
        array $mapping,
        int $tenantId,
        ExcelSheet $excelSheet,
        array $validationErrors = []
    ): array {
        $rows = Excel::toArray(new RawSheetImport, $path, 'local');

        if (empty($rows) || empty($rows[0])) {
            return ['success_count' => 0, 'error_count' => 0];
        }

        $sheet = $rows[0];
        $headers = array_map(fn ($h) => is_string($h) ? trim($h) : (string) $h, $sheet[0]);

        $columnIndex = [];
        foreach ($mapping as $systemField => $excelHeader) {
            if ($excelHeader === null || $excelHeader === '') {
                continue;
            }
            $idx = array_search($excelHeader, $headers, true);
            if ($idx !== false) {
                $columnIndex[$systemField] = $idx;
            }
        }

        // Preload lookups
        $communities = Community::where('account_tenant_id', $tenantId)->pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [strtolower(trim($name)) => $id]);

        $buildings = Building::where('account_tenant_id', $tenantId)->pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [strtolower(trim($name)) => $id]);

        $statuses = Status::where('type', 'unit')->get()
            ->mapWithKeys(fn (Status $s) => [strtolower(trim($s->name)) => $s->id]);

        // Row numbers of invalid rows (to skip)
        $errorRows = collect($validationErrors)->pluck('row')->unique()->flip()->all();

        $dataRows = array_slice($sheet, 1);
        $successCount = 0;
        $errorCount = 0;

        $batch = [];
        $batchSize = 50;

        foreach ($dataRows as $rowIdx => $row) {
            $lineNumber = $rowIdx + 2;

            if (isset($errorRows[$lineNumber])) {
                $errorCount++;

                continue;
            }

            $getValue = function (string $field) use ($row, $columnIndex): ?string {
                if (! isset($columnIndex[$field])) {
                    return null;
                }
                $val = $row[$columnIndex[$field]] ?? null;

                return $val !== null && $val !== '' ? trim((string) $val) : null;
            };

            $unitName = $getValue('name');
            $communityName = $getValue('rf_community_id');
            $buildingName = $getValue('rf_building_id');
            $statusValue = $getValue('status');
            $netArea = $getValue('net_area');

            if ($unitName === null || $unitName === '') {
                $errorCount++;

                continue;
            }

            $communityId = $communityName ? $communities->get(strtolower($communityName)) : null;
            $buildingId = $buildingName ? $buildings->get(strtolower($buildingName)) : null;
            $statusId = $statusValue ? $statuses->get(strtolower($statusValue)) : null;

            if ($communityId === null) {
                // Community is required — skip row
                $errorCount++;

                continue;
            }

            $batch[] = [
                'name' => $unitName,
                'rf_community_id' => $communityId,
                'rf_building_id' => $buildingId,
                'status_id' => $statusId,
                'net_area' => is_numeric($netArea) ? (float) $netArea : null,
                'account_tenant_id' => $tenantId,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= $batchSize) {
                $this->insertBatch($batch, $successCount, $errorCount);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            $this->insertBatch($batch, $successCount, $errorCount);
        }

        $excelSheet->update([
            'status' => 'completed',
            'success_count' => $successCount,
            'error_count' => $errorCount,
        ]);

        return ['success_count' => $successCount, 'error_count' => $errorCount];
    }

    /**
     * @param  list<array<string, mixed>>  $batch
     */
    private function insertBatch(array $batch, int &$successCount, int &$errorCount): void
    {
        DB::transaction(function () use ($batch, &$successCount, &$errorCount): void {
            foreach ($batch as $row) {
                try {
                    // Check if category_id and type_id are required — they are NOT NULL in DB
                    // so we must skip rows without them unless defaults exist.
                    // The import only creates minimal unit records; category/type can be set later.
                    // We use the lowest-id category and type as defaults.
                    $categoryId = DB::table('rf_unit_categories')->orderBy('id')->value('id');
                    $typeId = DB::table('rf_unit_types')->orderBy('id')->value('id');

                    if ($categoryId === null || $typeId === null) {
                        $errorCount++;

                        continue;
                    }

                    Unit::create(array_merge($row, [
                        'category_id' => $categoryId,
                        'type_id' => $typeId,
                    ]));
                    $successCount++;
                } catch (\Throwable) {
                    $errorCount++;
                }
            }
        });
    }
}
