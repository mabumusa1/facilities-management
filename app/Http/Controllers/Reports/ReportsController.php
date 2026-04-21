<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\AccountMembership;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportsController extends Controller
{
    public function powerBiReports(Request $request): Response
    {
        $this->authorizeReportsAccess($request);

        return Inertia::render('reports/Index', [
            'reportMode' => 'power-bi',
            'title' => 'Power BI Reports',
        ]);
    }

    public function reports(Request $request): Response
    {
        $this->authorizeReportsAccess($request);

        return Inertia::render('reports/Index', [
            'reportMode' => 'reports',
            'title' => 'Reports',
        ]);
    }

    public function systemReports(Request $request): Response
    {
        $this->authorizeReportsAccess($request);

        return Inertia::render('reports/Index', [
            'reportMode' => 'system-reports',
            'title' => 'System Reports',
        ]);
    }

    public function leaseReports(Request $request): Response
    {
        $this->authorizeReportsAccess($request);

        return Inertia::render('reports/Index', [
            'reportMode' => 'system-reports-lease',
            'title' => 'Lease Reports',
        ]);
    }

    public function maintenanceReports(Request $request): Response
    {
        $this->authorizeReportsAccess($request);

        return Inertia::render('reports/Index', [
            'reportMode' => 'system-reports-maintenance',
            'title' => 'Maintenance Reports',
        ]);
    }

    public function load(Request $request): JsonResponse
    {
        $this->authorizeReportsAccess($request);

        return $this->jsonOk('Report loaded.', [
            'reportId' => (string) $request->input('reportId', 'default-report'),
        ]);
    }

    public function prepare(Request $request): JsonResponse
    {
        $this->authorizeReportsAccess($request);

        return $this->jsonOk('Report prepared.', [
            'prepared' => true,
        ]);
    }

    public function renderReport(Request $request): JsonResponse
    {
        $this->authorizeReportsAccess($request);

        return $this->jsonOk('Report rendered.', [
            'rendered' => true,
            'token' => sha1((string) $request->user()?->id.'|render'),
        ]);
    }

    public function pages(Request $request): JsonResponse
    {
        $this->authorizeReportsAccess($request);

        return response()->json([
            'data' => [
                ['name' => 'Overview', 'display_name' => 'Overview', 'is_active' => true],
                ['name' => 'Collections', 'display_name' => 'Collections', 'is_active' => false],
                ['name' => 'Performance', 'display_name' => 'Performance', 'is_active' => false],
            ],
        ]);
    }

    public function activePage(Request $request): JsonResponse
    {
        $this->authorizeReportsAccess($request);

        return response()->json([
            'data' => [
                'name' => 'Overview',
                'display_name' => 'Overview',
            ],
        ]);
    }

    public function filters(Request $request): JsonResponse
    {
        $this->authorizeReportsAccess($request);

        return response()->json([
            'data' => [
                ['name' => 'community', 'value' => null],
                ['name' => 'building', 'value' => null],
                ['name' => 'date_range', 'value' => null],
            ],
        ]);
    }

    public function bookmarks(Request $request): JsonResponse
    {
        $this->authorizeReportsAccess($request);

        return response()->json([
            'data' => [],
        ]);
    }

    public function settings(Request $request): JsonResponse
    {
        $this->authorizeReportsAccess($request);

        return response()->json([
            'data' => [
                'allow_export' => true,
                'allow_print' => true,
                'default_theme' => 'light',
            ],
        ]);
    }

    public function print(Request $request): JsonResponse
    {
        $this->authorizeReportsAccess($request);

        return $this->jsonOk('Print queued.', ['queued' => true]);
    }

    public function refresh(Request $request): JsonResponse
    {
        $this->authorizeReportsAccess($request);

        return $this->jsonOk('Report refreshed.', ['refreshed' => true]);
    }

    public function save(Request $request): JsonResponse
    {
        $this->authorizeReportsAccess($request);

        return $this->jsonOk('Report saved.', ['saved' => true]);
    }

    public function saveAs(Request $request): JsonResponse
    {
        $this->authorizeReportsAccess($request);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        return $this->jsonOk('Report saved as new copy.', [
            'name' => $validated['name'] ?? 'Untitled Copy',
        ]);
    }

    public function theme(Request $request): JsonResponse
    {
        $this->authorizeReportsAccess($request);

        $validated = $request->validate([
            'theme' => ['nullable', 'string', 'max:50'],
        ]);

        return $this->jsonOk('Theme applied.', [
            'theme' => $validated['theme'] ?? 'light',
        ]);
    }

    public function zoom(Request $request): JsonResponse
    {
        $this->authorizeReportsAccess($request);

        $validated = $request->validate([
            'value' => ['nullable', 'numeric', 'min:0.25', 'max:4'],
        ]);

        return $this->jsonOk('Zoom updated.', [
            'value' => (float) ($validated['value'] ?? 1),
        ]);
    }

    private function authorizeReportsAccess(Request $request): void
    {
        $user = $request->user();

        abort_unless($user !== null, 401);

        $hasMembership = AccountMembership::query()
            ->where('user_id', $user->id)
            ->exists();

        abort_unless($hasMembership, 403);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function jsonOk(string $message, array $data = []): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
        ]);
    }
}
