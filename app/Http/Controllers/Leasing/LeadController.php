<?php

namespace App\Http\Controllers\Leasing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Leasing\StoreLeadRequest;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Status;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeadController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Lead::class);

        $search = trim((string) $request->input('search', ''));
        $statusId = $request->input('status_id');
        $sourceId = $request->input('source_id');
        $perPage = min(max((int) $request->integer('per_page', 15), 5), 50);

        return Inertia::render('leasing/leads/Index', [
            'leads' => Inertia::defer(function () use ($search, $statusId, $sourceId, $perPage) {
                return Lead::query()
                    ->with(['source', 'status', 'assignedTo'])
                    ->when($search !== '', function ($query) use ($search): void {
                        $query->where(function ($q) use ($search): void {
                            $q->where('name_en', 'like', "%{$search}%")
                                ->orWhere('name_ar', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%")
                                ->orWhere('phone_number', 'like', "%{$search}%");
                        });
                    })
                    ->when($statusId, fn ($q) => $q->where('status_id', (int) $statusId))
                    ->when($sourceId, fn ($q) => $q->where('source_id', (int) $sourceId))
                    ->latest()
                    ->paginate($perPage)
                    ->withQueryString();
            }),
            'sources' => LeadSource::query()
                ->select('id', 'name', 'name_en', 'name_ar')
                ->orderBy('name_en')
                ->get(),
            'statuses' => Status::query()
                ->where('type', 'lead')
                ->select('id', 'name', 'name_en', 'name_ar')
                ->orderBy('priority')
                ->orderBy('id')
                ->get(),
            'filters' => [
                'search' => $search,
                'status_id' => $statusId ? (string) $statusId : '',
                'source_id' => $sourceId ? (string) $sourceId : '',
                'per_page' => (string) $perPage,
            ],
        ]);
    }

    public function store(StoreLeadRequest $request): RedirectResponse
    {
        $this->authorize('create', Lead::class);

        $newStatus = Status::query()
            ->where('type', 'lead')
            ->where('name_en', 'New')
            ->orderBy('priority')
            ->firstOrFail();

        Lead::create([
            ...$request->validated(),
            'status_id' => $newStatus->id,
            'account_tenant_id' => Tenant::current()?->id,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Lead added successfully.')]);

        return redirect()->route('leads.index');
    }
}
