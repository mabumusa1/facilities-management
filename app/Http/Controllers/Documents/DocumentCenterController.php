<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\ExcelSheet;
use App\Models\Media;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DocumentCenterController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $mediaFiles = Media::query()
            ->when($user !== null, function ($query) use ($user): void {
                $query->where('mediable_type', $user::class)
                    ->where('mediable_id', $user->id);
            })
            ->latest()
            ->limit(25)
            ->get([
                'id',
                'url',
                'name',
                'notes',
                'collection',
                'created_at',
            ]);

        $excelImports = ExcelSheet::query()
            ->latest()
            ->limit(25)
            ->get([
                'id',
                'type',
                'file_name',
                'file_path',
                'status',
                'rf_community_id',
                'created_at',
            ]);

        $communities = Community::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return Inertia::render('documents/Index', [
            'communities' => $communities,
            'mediaFiles' => $mediaFiles,
            'excelImports' => $excelImports,
        ]);
    }
}
