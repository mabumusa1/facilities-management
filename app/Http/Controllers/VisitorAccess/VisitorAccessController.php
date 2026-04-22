<?php

namespace App\Http\Controllers\VisitorAccess;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceVisit;
use App\Models\Status;
use App\Support\StatusWorkflow;
use App\Support\WorkflowNotifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

class VisitorAccessController extends Controller
{
    public function rfIndex(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->integer('per_page', 10), 1), 50);

        $visits = MarketplaceVisit::query()
            ->with(['marketplaceUnit.unit:id,name', 'status:id,name,name_ar,name_en'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'data' => collect($visits->items())->map(fn (MarketplaceVisit $visit): array => [
                'id' => $visit->id,
                'marketplace_unit_id' => $visit->marketplace_unit_id,
                'unit' => $visit->marketplaceUnit?->unit
                    ? [
                        'id' => $visit->marketplaceUnit->unit->id,
                        'name' => $visit->marketplaceUnit->unit->name,
                    ]
                    : null,
                'status' => $visit->status
                    ? [
                        'id' => $visit->status->id,
                        'name' => $visit->status->name_en ?? $visit->status->name,
                    ]
                    : null,
                'visitor_name' => $visit->visitor_name,
                'visitor_phone' => $visit->visitor_phone,
                'scheduled_at' => $visit->scheduled_at?->toJSON(),
                'notes' => $visit->notes,
            ])->values()->all(),
            'meta' => $this->meta($visits),
        ]);
    }

    public function history(): Response
    {
        return Inertia::render('visitor-access/History', [
            'visits' => MarketplaceVisit::query()
                ->with(['marketplaceUnit.unit:id,name', 'status:id,name,name_ar,name_en'])
                ->latest()
                ->paginate(15),
        ]);
    }

    public function details(MarketplaceVisit $marketplaceVisit): Response
    {
        $marketplaceVisit->load([
            'marketplaceUnit.unit:id,name',
            'status:id,name,name_ar,name_en',
        ]);

        return Inertia::render('visitor-access/Details', [
            'visit' => $marketplaceVisit,
        ]);
    }

    public function approve(
        Request $request,
        MarketplaceVisit $marketplaceVisit,
        StatusWorkflow $statusWorkflow,
        WorkflowNotifier $workflowNotifier,
    ): RedirectResponse {
        $fromStatus = Status::query()->find($marketplaceVisit->status_id);

        $statusId = $this->resolveStatusId(['Approved']);

        if ($statusId !== null) {
            $statusWorkflow->ensureTransition('property_visit', $marketplaceVisit->status_id, $statusId);
        }

        $marketplaceVisit->update([
            'status_id' => $statusId ?: $marketplaceVisit->status_id,
        ]);

        $toStatus = $statusId ? Status::query()->find($statusId) : null;

        if ($toStatus instanceof Status) {
            $workflowNotifier->notifyTenantUsers(
                tenantId: (int) $request->session()->get('tenant_id'),
                module: 'visitor-access',
                resourceId: $marketplaceVisit->id,
                fromStatus: $fromStatus?->name_en ?? $fromStatus?->name,
                toStatus: $toStatus->name_en ?? $toStatus->name ?? (string) $toStatus->id,
                url: route('visitor-access.details', $marketplaceVisit, false),
                actor: $request->user()?->name,
            );
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Visitor access approved.')]);

        return back();
    }

    public function reject(
        Request $request,
        MarketplaceVisit $marketplaceVisit,
        StatusWorkflow $statusWorkflow,
        WorkflowNotifier $workflowNotifier,
    ): RedirectResponse {
        $validated = $request->validate([
            'notes' => ['nullable', 'string'],
        ]);

        $fromStatus = Status::query()->find($marketplaceVisit->status_id);

        $statusId = $this->resolveStatusId(['Rejected']);

        if ($statusId !== null) {
            $statusWorkflow->ensureTransition('property_visit', $marketplaceVisit->status_id, $statusId);
        }

        $marketplaceVisit->update([
            'status_id' => $statusId ?: $marketplaceVisit->status_id,
            'notes' => $validated['notes'] ?? $marketplaceVisit->notes,
        ]);

        $toStatus = $statusId ? Status::query()->find($statusId) : null;

        if ($toStatus instanceof Status) {
            $workflowNotifier->notifyTenantUsers(
                tenantId: (int) $request->session()->get('tenant_id'),
                module: 'visitor-access',
                resourceId: $marketplaceVisit->id,
                fromStatus: $fromStatus?->name_en ?? $fromStatus?->name,
                toStatus: $toStatus->name_en ?? $toStatus->name ?? (string) $toStatus->id,
                url: route('visitor-access.details', $marketplaceVisit, false),
                actor: $request->user()?->name,
            );
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Visitor access rejected.')]);

        return back();
    }

    private function resolveStatusId(array $candidateNames): ?int
    {
        return Status::query()
            ->where('type', 'property_visit')
            ->where(function ($query) use ($candidateNames): void {
                foreach ($candidateNames as $name) {
                    $query->orWhere('name_en', $name)
                        ->orWhere('name', $name);
                }
            })
            ->value('id');
    }

    /**
     * @return array<string, mixed>
     */
    private function meta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'from' => $paginator->firstItem(),
            'last_page' => $paginator->lastPage(),
            'path' => $paginator->path(),
            'per_page' => $paginator->perPage(),
            'to' => $paginator->lastItem(),
            'total' => $paginator->total(),
        ];
    }
}
