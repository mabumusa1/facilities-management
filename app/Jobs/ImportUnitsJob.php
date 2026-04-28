<?php

namespace App\Jobs;

use App\Models\ExcelSheet;
use App\Services\ImportUnitService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Async job that reads an Excel file and creates unit records.
 * Dispatched when the row count exceeds the inline-import threshold.
 */
class ImportUnitsJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var int Maximum execution time in seconds */
    public int $timeout = 600;

    /** @var int Retry attempts */
    public int $tries = 3;

    /**
     * Unique key prevents duplicate dispatch while this job is still running.
     * This is safer than raising retry_after globally because it only affects this job.
     */
    public function uniqueId(): string
    {
        return (string) $this->excelSheet->id;
    }

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

        // importRows() handles the final status/count update on the ExcelSheet internally.
        $importService->importRows(
            path: $this->filePath,
            mapping: $this->mapping,
            tenantId: $this->tenantId,
            excelSheet: $this->excelSheet,
            validationErrors: $this->validationErrors,
        );
    }

    public function failed(\Throwable $exception): void
    {
        $this->excelSheet->update(['status' => 'failed']);
    }
}
