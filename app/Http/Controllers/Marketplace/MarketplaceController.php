<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\Lead;
use App\Models\MarketplaceOffer;
use App\Models\MarketplaceUnit;
use App\Models\MarketplaceVisit;
use App\Models\Request as ServiceRequest;
use App\Models\Status;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class MarketplaceController extends Controller
{
    public function overview(): Response
    {
        return Inertia::render('marketplace/Overview', [
            'stats' => [
                'activeListings' => MarketplaceUnit::query()->where('is_active', true)->count(),
                'totalLeads' => Lead::query()->count(),
                'scheduledVisits' => MarketplaceVisit::query()->whereNotNull('scheduled_at')->count(),
                'listedCommunities' => Community::query()->where('is_market_place', true)->count(),
            ],
            'recentListings' => MarketplaceUnit::query()
                ->with(['unit:id,name,rf_community_id,rf_building_id'])
                ->latest()
                ->take(10)
                ->get(),
        ]);
    }

    public function customers(Request $request): Response
    {
        $leads = Lead::query()
            ->with(['source:id,name,name_ar,name_en', 'status:id,name,name_ar,name_en', 'leadOwner:id,first_name,last_name'])
            ->latest()
            ->paginate(15);

        return Inertia::render('marketplace/Customers', [
            'leads' => $leads,
        ]);
    }

    public function createSalesLead(Request $request): RedirectResponse
    {
        $validated = $this->validateLeadPayload($request);
        $validated['interested'] = 'sale';

        Lead::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Sales lead created.')]);

        return back();
    }

    public function createPropertyLead(Request $request): RedirectResponse
    {
        $validated = $this->validateLeadPayload($request);
        $validated['interested'] = $validated['interested'] ?? 'rent';

        Lead::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Property lead created.')]);

        return back();
    }

    public function listing(): Response
    {
        return Inertia::render('marketplace/Listing', [
            'listings' => MarketplaceUnit::query()
                ->with(['unit:id,name,rf_community_id,rf_building_id'])
                ->latest()
                ->paginate(15),
            'units' => Unit::query()
                ->select('id', 'name', 'rf_community_id', 'rf_building_id')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function storeListing(Request $request): RedirectResponse
    {
        MarketplaceUnit::create($this->validateListingPayload($request));

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Marketplace listing created.')]);

        return to_route('marketplace.listing');
    }

    public function updateListing(Request $request, MarketplaceUnit $marketplaceUnit): RedirectResponse
    {
        $marketplaceUnit->update($this->validateListingPayload($request));

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Marketplace listing updated.')]);

        return to_route('marketplace.listing');
    }

    public function destroyListing(MarketplaceUnit $marketplaceUnit): RedirectResponse
    {
        $marketplaceUnit->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Marketplace listing deleted.')]);

        return to_route('marketplace.listing');
    }

    public function storeOffer(Request $request): JsonResponse
    {
        $offer = MarketplaceOffer::create($this->validateOfferPayload($request));

        return response()->json([
            'data' => $offer->only([
                'id',
                'unit_id',
                'title',
                'description',
                'discount_type',
                'discount_value',
                'start_date',
                'end_date',
                'is_active',
            ]),
            'message' => __('Marketplace offer created.'),
        ]);
    }

    public function updateOffer(Request $request, MarketplaceOffer $marketplaceOffer): JsonResponse
    {
        $marketplaceOffer->update($this->validateOfferPayload($request, true));

        return response()->json([
            'data' => $marketplaceOffer->only([
                'id',
                'unit_id',
                'title',
                'description',
                'discount_type',
                'discount_value',
                'start_date',
                'end_date',
                'is_active',
            ]),
            'message' => __('Marketplace offer updated.'),
        ]);
    }

    public function destroyOffer(MarketplaceOffer $marketplaceOffer): JsonResponse
    {
        $offerId = $marketplaceOffer->id;
        $marketplaceOffer->delete();

        return response()->json([
            'data' => [
                'id' => $offerId,
            ],
            'message' => __('Marketplace offer deleted.'),
        ]);
    }

    public function visits(): Response
    {
        return Inertia::render('marketplace/Visits', [
            'visits' => MarketplaceVisit::query()
                ->with(['marketplaceUnit.unit:id,name', 'status:id,name,name_ar,name_en'])
                ->latest()
                ->paginate(15),
            'listings' => MarketplaceUnit::query()
                ->with('unit:id,name')
                ->where('is_active', true)
                ->get(),
        ]);
    }

    public function showVisit(MarketplaceVisit $marketplaceVisit): Response
    {
        $marketplaceVisit->load([
            'marketplaceUnit.unit:id,name',
            'status:id,name,name_ar,name_en',
        ]);

        return Inertia::render('marketplace/VisitShow', [
            'visit' => $marketplaceVisit,
        ]);
    }

    public function scheduleViewing(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'marketplace_unit_id' => ['required', 'integer', 'exists:rf_marketplace_units,id'],
            'visitor_name' => ['required', 'string', 'max:255'],
            'visitor_phone' => ['required', 'string', 'max:50'],
            'scheduled_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $defaultStatusId = Status::query()
            ->where('type', 'property_visit')
            ->value('id');

        MarketplaceVisit::create([
            ...$validated,
            'status_id' => $defaultStatusId,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Viewing scheduled.')]);

        return back();
    }

    public function cancelVisit(MarketplaceVisit $marketplaceVisit): RedirectResponse
    {
        $cancelStatus = Status::query()
            ->where('type', 'property_visit')
            ->where(function ($query): void {
                $query->where('name_en', 'Canceled')
                    ->orWhere('name_en', 'Cancelled')
                    ->orWhere('name', 'Canceled')
                    ->orWhere('name', 'Cancelled');
            })
            ->value('id');

        $marketplaceVisit->update([
            'status_id' => $cancelStatus ?: $marketplaceVisit->status_id,
            'notes' => trim(($marketplaceVisit->notes ? $marketplaceVisit->notes."\n" : '').'Visit canceled on '.Carbon::now()->toDateTimeString()),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Visit canceled.')]);

        return back();
    }

    public function sendContract(MarketplaceVisit $marketplaceVisit): RedirectResponse
    {
        $marketplaceVisit->update([
            'notes' => trim(($marketplaceVisit->notes ? $marketplaceVisit->notes."\n" : '').'Contract sent on '.Carbon::now()->toDateTimeString()),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Contract sent to visitor.')]);

        return back();
    }

    public function assignRequest(Request $httpRequest, ServiceRequest $request): RedirectResponse
    {
        $validated = $httpRequest->validate([
            'professional_id' => ['required', 'integer', 'exists:rf_professionals,id'],
            'admin_notes' => ['nullable', 'string'],
        ]);

        $request->update([
            'professional_id' => $validated['professional_id'],
            'admin_notes' => $validated['admin_notes'] ?? $request->admin_notes,
            'assigned_at' => now(),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Request assigned successfully.')]);

        return back();
    }

    public function unitsApi(Request $request): JsonResponse
    {
        $units = MarketplaceUnit::query()
            ->with('unit:id,name,rf_community_id,rf_building_id')
            ->latest()
            ->paginate($this->perPage($request));

        return response()->json([
            'data' => $units->items(),
            'meta' => $this->meta($units),
        ]);
    }

    public function visitsApi(Request $request): JsonResponse
    {
        $visits = MarketplaceVisit::query()
            ->with(['marketplaceUnit.unit:id,name', 'status:id,name,name_ar,name_en'])
            ->latest()
            ->paginate($this->perPage($request));

        return response()->json([
            'data' => $visits->items(),
            'meta' => $this->meta($visits),
        ]);
    }

    public function listCommunity(Request $request, Community $community): JsonResponse
    {
        $validated = $request->validate([
            'allow_cash_sale' => ['required', 'boolean'],
            'allow_bank_financing' => ['nullable', 'boolean'],
        ]);

        $community->update([
            'is_market_place' => true,
            'allow_cash_sale' => $validated['allow_cash_sale'],
            'allow_bank_financing' => (bool) ($validated['allow_bank_financing'] ?? false),
        ]);

        return response()->json([
            'data' => [
                'id' => $community->id,
                'is_market_place' => $community->is_market_place,
                'allow_cash_sale' => $community->allow_cash_sale,
                'allow_bank_financing' => $community->allow_bank_financing,
            ],
            'message' => __('Community listed in marketplace successfully.'),
        ]);
    }

    public function unlistCommunity(Community $community): JsonResponse
    {
        $community->update(['is_market_place' => false]);

        return response()->json([
            'data' => [
                'id' => $community->id,
                'is_market_place' => $community->is_market_place,
            ],
            'message' => __('Community unlisted from marketplace.'),
        ]);
    }

    public function updateCommunitySalesInformation(Request $request, Community $community): JsonResponse
    {
        $validated = $request->validate([
            'description' => ['nullable', 'string'],
            'completion_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'listed_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $community->update($validated);

        return response()->json([
            'data' => $community->only(['id', 'description', 'completion_percent', 'listed_percentage']),
            'message' => __('Community sales information updated.'),
        ]);
    }

    public function resendCommunitySalesInformation(Community $community): JsonResponse
    {
        return response()->json([
            'data' => [
                'id' => $community->id,
            ],
            'message' => __('Community sales information resent successfully.'),
        ]);
    }

    public function updateUnitPricesVisibility(Request $request, MarketplaceUnit $marketplaceUnit): JsonResponse
    {
        $validated = $request->validate([
            'price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ]);

        $marketplaceUnit->update($validated);

        return response()->json([
            'data' => $marketplaceUnit->only(['id', 'price', 'is_active']),
            'message' => __('Unit listing visibility updated.'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateListingPayload(Request $request): array
    {
        return $request->validate([
            'unit_id' => ['required', 'integer', 'exists:rf_units,id'],
            'listing_type' => ['required', 'string', 'in:rent,sale,both'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateLeadPayload(Request $request): array
    {
        return $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'source_id' => ['nullable', 'integer', 'exists:rf_lead_sources,id'],
            'status_id' => ['nullable', 'integer', 'exists:rf_statuses,id'],
            'priority_id' => ['nullable', 'integer'],
            'lead_owner_id' => ['nullable', 'integer', 'exists:rf_admins,id'],
            'interested' => ['nullable', 'string', 'max:50'],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateOfferPayload(Request $request, bool $isUpdate = false): array
    {
        $requiredOrSometimes = $isUpdate ? 'sometimes' : 'required';

        return $request->validate([
            'unit_id' => [$requiredOrSometimes, 'integer', 'exists:rf_units,id'],
            'title' => [$requiredOrSometimes, 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'discount_type' => [$requiredOrSometimes, 'string', Rule::in(['percentage', 'fixed'])],
            'discount_value' => [
                $requiredOrSometimes,
                'numeric',
                'min:0',
                Rule::when($request->input('discount_type') === 'percentage', ['max:100']),
            ],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }

    private function perPage(Request $request): int
    {
        return min(max((int) $request->integer('per_page', 10), 1), 50);
    }

    /**
     * @return array<string, mixed>
     */
    private function meta($paginator): array
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
