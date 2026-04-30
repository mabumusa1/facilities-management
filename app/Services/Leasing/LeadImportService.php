<?php

namespace App\Services\Leasing;

use App\Imports\RawSheetImport;
use App\Models\Lead;
use App\Models\Status;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Parses an uploaded Excel file for lead import.
 *
 * Expected columns (case-insensitive after trimming):
 *   Name (EN), Name (AR), Phone, Email, Source, Notes
 */
class LeadImportService
{
    public const EXCEL_SOURCE_ID = 12;

    public const MAX_ROWS = 500;

    public const MAX_FILE_SIZE_MB = 5;

    /**
     * Expected header columns (canonical lowercase names).
     */
    private const HEADERS = ['name (en)', 'name (ar)', 'phone', 'email', 'source', 'notes'];

    /**
     * Parse and validate all data rows in the file.
     *
     * @param  string  $path  Local disk path to the uploaded file
     * @param  int  $tenantId  Current tenant ID for duplicate check
     * @return array{
     *     total_rows: int,
     *     valid_count: int,
     *     error_count: int,
     *     errors: list<array{row: int, field: string, message: string}>,
     *     valid_rows: list<array{name_en: string|null, name_ar: string|null, phone_number: string, email: string|null, notes: string|null}>
     * }
     */
    public function parse(string $path, int $tenantId): array
    {
        $rows = Excel::toArray(new RawSheetImport, $path, 'local');

        if (empty($rows) || empty($rows[0])) {
            return $this->emptyResult();
        }

        $allRows = $rows[0];
        $headers = $this->normalizeHeaders($allRows[0] ?? []);

        if (! $this->hasRequiredHeaders($headers)) {
            return $this->emptyResult();
        }

        $dataRows = array_slice($allRows, 1);

        // Skip completely empty rows
        $dataRows = array_values(array_filter(
            $dataRows,
            fn (array $row) => count(array_filter($row, fn ($cell) => $cell !== null && $cell !== '')) > 0,
        ));

        $totalRows = count($dataRows);
        $errors = [];
        $validRows = [];

        // Collect phone numbers and emails already seen in this file (for intra-file duplicate detection)
        $seenPhones = [];
        $seenEmails = [];

        foreach ($dataRows as $index => $row) {
            $rowNum = $index + 2; // +2: 1-indexed + header row
            $data = $this->mapRow($headers, $row);

            $rowErrors = $this->validateRow($data, $rowNum, $tenantId, $seenPhones, $seenEmails);

            if (! empty($rowErrors)) {
                foreach ($rowErrors as $err) {
                    $errors[] = $err;
                }
            } else {
                $validRows[] = [
                    'name_en' => $this->nullIfEmpty($data['name (en)'] ?? ''),
                    'name_ar' => $this->nullIfEmpty($data['name (ar)'] ?? ''),
                    'phone_number' => trim((string) ($data['phone'] ?? '')),
                    'email' => $this->nullIfEmpty($data['email'] ?? ''),
                    'notes' => $this->nullIfEmpty($data['notes'] ?? ''),
                ];

                // Track for intra-file duplicate detection
                $phone = trim((string) ($data['phone'] ?? ''));
                if ($phone !== '') {
                    $seenPhones[] = $phone;
                }
                $email = $this->nullIfEmpty($data['email'] ?? '');
                if ($email !== null) {
                    $seenEmails[] = strtolower($email);
                }
            }
        }

        return [
            'total_rows' => $totalRows,
            'valid_count' => count($validRows),
            'error_count' => count(array_unique(array_column($errors, 'row'))),
            'errors' => $errors,
            'valid_rows' => $validRows,
        ];
    }

    /**
     * Generate an Excel download of error rows from a stored error_details array.
     *
     * @param  list<array{row: int, field: string, message: string}>  $errors
     */
    public function buildErrorReport(array $errors): string
    {
        // Build CSV content for error report
        $lines = ['Row,Field,Error'];
        foreach ($errors as $err) {
            $row = (int) $err['row'];
            $field = str_replace(',', ';', (string) ($err['field'] ?? ''));
            $message = str_replace(',', ';', (string) ($err['message'] ?? ''));
            $lines[] = "{$row},{$field},{$message}";
        }

        return implode("\n", $lines);
    }

    /**
     * @param  list<array{name_en: string|null, name_ar: string|null, phone_number: string, email: string|null, notes: string|null}>  $validRows
     */
    public function importRows(array $validRows, int $tenantId, int $statusId): int
    {
        $imported = 0;

        foreach ($validRows as $row) {
            Lead::create([
                'name_en' => $row['name_en'],
                'name_ar' => $row['name_ar'],
                'name' => $row['name_en'] ?? $row['name_ar'],
                'phone_number' => $row['phone_number'],
                'phone_country_code' => '+966',
                'email' => $row['email'],
                'notes' => $row['notes'],
                'source_id' => self::EXCEL_SOURCE_ID,
                'status_id' => $statusId,
                'account_tenant_id' => $tenantId,
            ]);

            $imported++;
        }

        return $imported;
    }

    public function getNewStatusId(): int
    {
        return (int) Status::query()
            ->where('type', 'lead')
            ->where('name_en', 'New')
            ->orderBy('priority')
            ->firstOrFail()
            ->id;
    }

    /**
     * @param  list<mixed>  $headerRow
     * @return array<int, string>
     */
    private function normalizeHeaders(array $headerRow): array
    {
        return array_map(
            fn ($cell) => strtolower(trim((string) $cell)),
            $headerRow,
        );
    }

    /**
     * @param  array<int, string>  $headers
     */
    private function hasRequiredHeaders(array $headers): bool
    {
        return in_array('phone', $headers, true);
    }

    /**
     * @param  array<int, string>  $headers
     * @param  list<mixed>  $row
     * @return array<string, mixed>
     */
    private function mapRow(array $headers, array $row): array
    {
        $mapped = [];
        foreach ($headers as $colIndex => $headerName) {
            $mapped[$headerName] = $row[$colIndex] ?? null;
        }

        return $mapped;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  string[]  $seenPhones
     * @param  string[]  $seenEmails
     * @return list<array{row: int, field: string, message: string}>
     */
    private function validateRow(
        array $data,
        int $rowNum,
        int $tenantId,
        array $seenPhones,
        array $seenEmails,
    ): array {
        $rowErrors = [];

        $nameEn = $this->nullIfEmpty($data['name (en)'] ?? '');
        $nameAr = $this->nullIfEmpty($data['name (ar)'] ?? '');
        $phone = trim((string) ($data['phone'] ?? ''));
        $email = $this->nullIfEmpty($data['email'] ?? '');

        // At least one name is required
        if ($nameEn === null && $nameAr === null) {
            $rowErrors[] = [
                'row' => $rowNum,
                'field' => 'Name (EN)',
                'message' => 'At least one of Name (EN) or Name (AR) is required.',
            ];
        }

        // Phone is required
        if ($phone === '') {
            $rowErrors[] = [
                'row' => $rowNum,
                'field' => 'Phone',
                'message' => 'Phone is required.',
            ];
        } elseif (strlen($phone) > 50) {
            $rowErrors[] = [
                'row' => $rowNum,
                'field' => 'Phone',
                'message' => 'Phone must be at most 50 characters.',
            ];
        } else {
            // Check for duplicate phone in this upload batch
            if (in_array($phone, $seenPhones, true)) {
                $rowErrors[] = [
                    'row' => $rowNum,
                    'field' => 'Phone',
                    'message' => 'Duplicate phone number in this file.',
                ];
            }

            // Check for duplicate phone in existing tenant leads
            if (
                $phone !== '' &&
                Lead::query()
                    ->where('account_tenant_id', $tenantId)
                    ->where('phone_number', $phone)
                    ->exists()
            ) {
                $rowErrors[] = [
                    'row' => $rowNum,
                    'field' => 'Phone',
                    'message' => 'A lead with this phone number already exists.',
                ];
            }
        }

        // Email validation
        if ($email !== null) {
            $emailValidator = Validator::make(
                ['email' => $email],
                ['email' => ['email', 'max:255']],
            );

            if ($emailValidator->fails()) {
                $rowErrors[] = [
                    'row' => $rowNum,
                    'field' => 'Email',
                    'message' => 'Invalid email format.',
                ];
            } elseif (strtolower($email) !== $email || in_array(strtolower($email), $seenEmails, true)) {
                // Check intra-file duplicate email
                if (in_array(strtolower($email), $seenEmails, true)) {
                    $rowErrors[] = [
                        'row' => $rowNum,
                        'field' => 'Email',
                        'message' => 'Duplicate email address in this file.',
                    ];
                }
            }

            // Check for duplicate email in existing tenant leads
            if (
                $email !== null &&
                empty(array_filter($rowErrors, fn ($e) => $e['field'] === 'Email')) &&
                Lead::query()
                    ->where('account_tenant_id', $tenantId)
                    ->whereNotNull('email')
                    ->whereRaw('LOWER(email) = ?', [strtolower($email)])
                    ->exists()
            ) {
                $rowErrors[] = [
                    'row' => $rowNum,
                    'field' => 'Email',
                    'message' => 'A lead with this email already exists.',
                ];
            }
        }

        return $rowErrors;
    }

    /**
     * @return array{total_rows: 0, valid_count: 0, error_count: 0, errors: array, valid_rows: array}
     */
    private function emptyResult(): array
    {
        return [
            'total_rows' => 0,
            'valid_count' => 0,
            'error_count' => 0,
            'errors' => [],
            'valid_rows' => [],
        ];
    }

    private function nullIfEmpty(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $str = trim((string) $value);

        return $str === '' ? null : $str;
    }
}
