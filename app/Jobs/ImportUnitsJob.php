<?php

namespace App\Jobs;

use App\Models\ExcelSheet;
use App\Services\ImportUnitService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Async job that reads an Excel file and creates unit records.
 * Dispatched when the row count exceeds the inline-import threshold.
 */
class ImportUnitsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var int Maximum execution time in seconds */
    public int $timeout = 600;

    /** @var int Retry attempts */
    public int $tries = 3;

    /**
     * @param  array<string, string>  $mapping  System field => Excel header
     * @param  list<array{row: int, field: string, message: string}>  $validationErrors  Pre-validated row errors
     */
    public function __construct(
        public readonly ExcelSheet $excelSheet,
        public readonly string $filePath,
        public readonly int $tenantId,
        public readonly array $mapping,
        public readonly array $validationErrors = [],
    ) {}

    public function handle(ImportUnitService $importService): void
    {
        $this->excelSheet->update(['status' => 'processing']);

        $result = $importService->importRows(
            path: $this->filePath,
            mapping: $this->mapping,
            tenantId: $this->tenantId,
            excelSheet: $this->excelSheet,
            validationErrors: $this->validationErrors,
        );

        $this->excelSheet->update([
            'status' => 'completed',
            'success_count' => $result['success_count'],
            'error_count' => $result['error_count'],
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        $this->excelSheet->update(['status' => 'failed']);
    }
}
