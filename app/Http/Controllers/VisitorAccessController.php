<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\VisitorAccess;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VisitorAccessController extends Controller
{
    /**
     * Display a listing of visitor accesses (type=normal).
     */
    public function index(Request $request): Response
    {
        $tenantId = $request->user()?->tenant_id;

        $visitors = VisitorAccess::query()
            ->where('tenant_id', $tenantId)
            ->where('type', 'normal')
            ->with(['status', 'unit'])
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (VisitorAccess $v): array => [
                'id' => $v->id,
                'visitor_name' => $v->visitor_name,
                'visitor_phone' => $v->visitor_phone,
                'visit_date' => $v->visit_date?->toDateString(),
                'status' => $v->status?->name,
                'unit' => $v->unit?->name,
            ]);

        return Inertia::render('visitor-access/index', [
            'visitors' => $visitors,
            'type' => 'normal',
        ]);
    }

    /**
     * Display visitor access history (type=history).
     */
    public function history(Request $request): Response
    {
        $tenantId = $request->user()?->tenant_id;

        $visitors = VisitorAccess::query()
            ->where('tenant_id', $tenantId)
            ->where('type', 'history')
            ->with(['status', 'unit'])
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (VisitorAccess $v): array => [
                'id' => $v->id,
                'visitor_name' => $v->visitor_name,
                'visitor_phone' => $v->visitor_phone,
                'visit_date' => $v->visit_date?->toDateString(),
                'status' => $v->status?->name,
                'unit' => $v->unit?->name,
            ]);

        return Inertia::render('visitor-access/history', [
            'visitors' => $visitors,
            'type' => 'history',
        ]);
    }

    /**
     * Display a visitor access detail.
     */
    public function show(VisitorAccess $visitorAccess): Response
    {
        $visitorAccess->load(['status', 'unit', 'unit.building', 'unit.community']);

        return Inertia::render('visitor-access/show', [
            'visitor' => [
                'id' => $visitorAccess->id,
                'visitor_name' => $visitorAccess->visitor_name,
                'visitor_phone' => $visitorAccess->visitor_phone,
                'visitor_id_number' => $visitorAccess->visitor_id_number,
                'visit_date' => $visitorAccess->visit_date?->toDateString(),
                'visit_time' => $visitorAccess->visit_time,
                'purpose' => $visitorAccess->purpose,
                'status' => $visitorAccess->status?->name,
                'unit' => $visitorAccess->unit?->name,
                'building' => $visitorAccess->unit?->building?->name,
                'community' => $visitorAccess->unit?->community?->name,
                'notes' => $visitorAccess->notes,
                'created_at' => $visitorAccess->created_at?->toDateTimeString(),
            ],
        ]);
    }
}
