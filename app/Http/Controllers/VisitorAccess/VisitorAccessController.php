<?php

namespace App\Http\Controllers\VisitorAccess;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceVisit;
use App\Models\Status;
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

    public function approve(MarketplaceVisit $marketplaceVisit): RedirectResponse
    {
        $statusId = $this->resolveStatusId(['Approved']);

        $marketplaceVisit->update([
            'status_id' => $statusId ?: $marketplaceVisit->status_id,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Visitor access approved.')]);

        return back();
    }

    public function reject(Request $request, MarketplaceVisit $marketplaceVisit): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string'],
        ]);

        $statusId = $this->resolveStatusId(['Rejected']);

        $marketplaceVisit->update([
            'status_id' => $statusId ?: $marketplaceVisit->status_id,
            'notes' => $validated['notes'] ?? $marketplaceVisit->notes,
        ]);

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
