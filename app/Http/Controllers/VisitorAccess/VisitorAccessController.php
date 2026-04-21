<?php

namespace App\Http\Controllers\VisitorAccess;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceVisit;
use App\Models\Status;
use App\Support\StatusWorkflow;
use App\Support\WorkflowNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VisitorAccessController extends Controller
{
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
}
