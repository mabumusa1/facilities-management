<?php

namespace App\Http\Controllers\Properties;

use App\Exports\UnitImportTemplateExport;
use App\Http\Controllers\Controller;
use App\Models\Unit;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UnitTemplateController extends Controller
{
    /**
     * GET /units/import/template
     *
     * Downloads a sample .xlsx template for unit bulk import.
     */
    public function download(): BinaryFileResponse
    {
        $this->authorize('import', Unit::class);

        return Excel::download(
            new UnitImportTemplateExport,
            'unit-import-template.xlsx'
        );
    }
}
