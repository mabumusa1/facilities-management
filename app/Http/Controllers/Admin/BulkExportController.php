<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\Lease;
use App\Models\Owner;
use App\Models\Resident;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BulkExportController extends Controller
{
    private const EXPORTABLE = [
        'communities' => Community::class,
        'units' => Unit::class,
        'leases' => Lease::class,
        'residents' => Resident::class,
        'owners' => Owner::class,
    ];

    public function export(Request $request, string $model): JsonResponse
    {
        if (! array_key_exists($model, self::EXPORTABLE)) {
            abort(404, "Export not available for '{$model}'.");
        }

        $modelClass = self::EXPORTABLE[$model];
        $query = $modelClass::query();

        if (method_exists($modelClass, 'scopeTenantScoped')) {
            $query->tenantScoped();
        }

        $records = $query->limit(1000)->get();

        if ($records->isEmpty()) {
            return response()->json(['message' => 'No records to export.'], 200);
        }

        $csv = fopen('php://temp', 'r+');
        $first = $records->first()->toArray();
        fputcsv($csv, array_keys($first));

        foreach ($records as $record) {
            fputcsv($csv, $record->toArray());
        }

        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        return response()->json([
            'exported_at' => now()->toJSON(),
            'row_count' => $records->count(),
        ], 200);
    }
}
