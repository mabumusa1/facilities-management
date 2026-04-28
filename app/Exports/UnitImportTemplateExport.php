<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Generates a blank .xlsx template for unit bulk import.
 */
class UnitImportTemplateExport implements FromArray, ShouldAutoSize, WithHeadings
{
    use Exportable;

    public function headings(): array
    {
        return [
            'Unit Name',
            'Community',
            'Building',
            'Area (sqm)',
            'Status',
        ];
    }

    public function array(): array
    {
        // Return a single example row
        return [
            ['A-101', 'Example Community', 'Building A', '85.5', 'available'],
        ];
    }
}
