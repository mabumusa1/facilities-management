<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Building;
use App\Models\Community;
use App\Models\Lead;
use App\Models\Lease;
use App\Models\MarketplaceVisit;
use App\Models\Request as ServiceRequest;
use App\Models\Resident;
use App\Models\Transaction;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Dashboard', [
            'stats' => Inertia::defer(fn () => $this->statisticsItems()),
            'recentLeases' => Inertia::defer(fn () => Lease::with(['tenant', 'status'])
                ->latest()
                ->take(5)
                ->get()
                ->map(fn ($lease) => [
                    'id' => $lease->id,
                    'contract_number' => $lease->contract_number,
                    'tenant_name' => $lease->tenant?->first_name.' '.$lease->tenant?->last_name,
                    'status' => $lease->status?->name_en ?? $lease->status?->name,
                    'start_date' => $lease->start_date,
                    'end_date' => $lease->end_date,
                    'amount' => $lease->rental_total_amount,
                ])),
            'recentRequests' => Inertia::defer(fn () => ServiceRequest::with(['category', 'status'])
                ->latest()
                ->take(5)
                ->get()
                ->map(fn ($req) => [
                    'id' => $req->id,
                    'category' => $req->category?->name_en ?? $req->category?->name,
                    'status' => $req->status?->name_en ?? $req->status?->name,
                    'priority' => $req->priority,
                    'created_at' => $req->created_at->toDateString(),
                ])),
            'requiresAttention' => Inertia::defer(fn () => $this->requiresAttentionItems()),
        ]);
    }

    public function requiresAttention(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->requiresAttentionItems(),
        ]);
    }

    public function statistics(Request $request): JsonResponse
    {
        $stats = $this->statisticsItems();

        return response()->json([
            'data' => [
                'requests_approval' => $stats['openRequests'],
                'pending_complaints' => 0,
                'expiring_leases' => $stats['expiringLeases'],
                'overdue_recipes' => $stats['overdueTransactions'],
                ...$stats,
            ],
        ]);
    }

    /**
     * @return array<string, int|float|string>
     */
    private function statisticsItems(): array
    {
        return [
            'communities' => Community::count(),
            'buildings' => Building::count(),
            'units' => Unit::count(),
            'tenants' => Resident::count(),
            'activeLeases' => Lease::whereHas('status', fn ($q) => $q->where('name_en', 'Active'))->count(),
            'openRequests' => ServiceRequest::whereHas('status', fn ($q) => $q->whereIn('name_en', ['Open', 'In Progress', 'New']))->count(),
            'pendingTransactions' => Transaction::where('is_paid', false)->count(),
            'totalRevenue' => (float) Transaction::where('is_paid', true)->sum('amount'),
            'expiringLeases' => Lease::query()
                ->whereDate('end_date', '>=', now()->toDateString())
                ->whereDate('end_date', '<=', now()->addDays(30)->toDateString())
                ->count(),
            'overdueTransactions' => Transaction::query()
                ->where('is_paid', false)
                ->whereDate('due_on', '<', now()->toDateString())
                ->count(),
        ];
    }

    /**
     * @return array<int, array{key: string, title: string, count: int, href: string}>
     */
    private function requiresAttentionItems(): array
    {
        $openRequests = ServiceRequest::query()
            ->whereHas('status', fn ($query) => $query->whereIn('name_en', ['Open', 'In Progress', 'New']))
            ->count();

        $pendingVisits = MarketplaceVisit::query()
            ->where(function ($query): void {
                $query->whereNull('status_id')
                    ->orWhereHas('status', fn ($statusQuery) => $statusQuery->whereIn('name_en', ['New', 'Pending']));
            })
            ->count();

        $overdueTransactions = Transaction::query()
            ->where('is_paid', false)
            ->whereDate('due_on', '<', now()->toDateString())
            ->count();

        $draftAnnouncements = Announcement::query()
            ->where('status', false)
            ->count();

        $unassignedLeads = Lead::query()
            ->whereNull('lead_owner_id')
            ->count();

        return [
            ['key' => 'open_requests', 'title' => 'Open Requests', 'count' => $openRequests, 'href' => '/requests'],
            ['key' => 'pending_visits', 'title' => 'Pending Visits', 'count' => $pendingVisits, 'href' => '/marketplace/visits'],
            ['key' => 'overdue_transactions', 'title' => 'Overdue Transactions', 'count' => $overdueTransactions, 'href' => '/transactions'],
            ['key' => 'draft_announcements', 'title' => 'Draft Announcements', 'count' => $draftAnnouncements, 'href' => '/announcements'],
            ['key' => 'unassigned_leads', 'title' => 'Unassigned Leads', 'count' => $unassignedLeads, 'href' => '/marketplace/customers'],
        ];
    }
}
