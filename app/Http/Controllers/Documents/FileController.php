<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class FileController extends Controller
{
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'file', 'max:10240'],
            'collection' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $file = $request->file('image');
        $path = $file->store('uploads/files', 'public');

        $user = $request->user();

        $media = Media::create([
            'url' => Storage::disk('public')->url($path),
            'name' => $file->getClientOriginalName(),
            'notes' => $validated['notes'] ?? null,
            'collection' => $validated['collection'] ?? 'documents',
            'mediable_type' => $user ? $user::class : Media::class,
            'mediable_id' => $user?->id ?? 0,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $media->id,
                    'url' => $media->url,
                    'name' => $media->name,
                ],
                'message' => __('File uploaded successfully.'),
            ]);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('File uploaded successfully.'),
        ]);

        return back();
    }

    public function destroy(Request $request, Media $media): JsonResponse|RedirectResponse
    {
        if (Str::contains($media->url, '/storage/')) {
            $storagePath = Str::after($media->url, '/storage/');
            Storage::disk('public')->delete($storagePath);
        }

        $media->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $media->id,
                ],
                'message' => __('File deleted successfully.'),
            ]);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('File deleted successfully.'),
        ]);

        return back();
    }
}
