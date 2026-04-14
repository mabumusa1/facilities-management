<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\Facility;
use App\Models\MarketplaceCustomer;
use App\Models\MarketplaceOffer;
use App\Models\MarketplaceSetting;
use App\Models\MarketplaceUnit;
use App\Models\MarketplaceVisit;
use App\Models\Unit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MarketplaceController extends Controller
{
    /**
     * Main marketplace landing page.
     */
    public function index(Request $request): Response
    {
        $tenantId = $request->user()?->tenant_id;

        return Inertia::render('marketplace/index', [
            'stats' => [
                'listings' => Unit::query()->where('tenant_id', $tenantId)->where('is_marketplace', true)->count(),
                'customers' => MarketplaceCustomer::query()->where('tenant_id', $tenantId)->count(),
            ],
        ]);
    }

    /**
     * Marketplace listing page.
     */
    public function listing(Request $request): Response
    {
        $tenantId = $request->user()?->tenant_id;

        $units = MarketplaceUnit::query()
            ->where('tenant_id', $tenantId)
            ->with(['unit', 'unit.community', 'unit.building'])
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (MarketplaceUnit $u): array => [
                'id' => $u->id,
                'unit' => $u->unit?->name,
                'community' => $u->unit?->community?->name,
                'building' => $u->unit?->building?->name,
                'price' => $u->price,
                'is_active' => $u->is_active,
            ]);

        return Inertia::render('marketplace/listing', [
            'units' => $units,
        ]);
    }

    /**
     * Marketplace customers page.
     */
    public function customers(Request $request): Response
    {
        $tenantId = $request->user()?->tenant_id;

        $customers = MarketplaceCustomer::query()
            ->where('tenant_id', $tenantId)
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (MarketplaceCustomer $c): array => [
                'id' => $c->id,
                'name' => $c->name,
                'email' => $c->email,
                'phone' => $c->phone,
                'created_at' => $c->created_at?->toDateString(),
            ]);

        return Inertia::render('marketplace/customers', [
            'customers' => $customers,
        ]);
    }

    /**
     * Marketplace favorites page.
     */
    public function favorites(Request $request): Response
    {
        $tenantId = $request->user()?->tenant_id;

        $favorites = Unit::query()
            ->where('tenant_id', $tenantId)
            ->where('is_marketplace', true)
            ->with(['community', 'building'])
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Unit $u): array => [
                'id' => $u->id,
                'name' => $u->name,
                'community' => $u->community?->name,
                'building' => $u->building?->name,
                'market_rent' => $u->market_rent,
            ]);

        return Inertia::render('marketplace/favorites', [
            'favorites' => $favorites,
        ]);
    }

    /**
     * Off-plan form page.
     */
    public function offPlanForm(): Response
    {
        return Inertia::render('marketplace/off-plan-form');
    }

    /**
     * Upload leads page.
     */
    public function uploadLeads(): Response
    {
        return Inertia::render('marketplace/upload-leads');
    }

    /**
     * Admin: bookings page.
     */
    public function adminBookings(Request $request): Response
    {
        $tenantId = $request->user()?->tenant_id;

        $visits = MarketplaceVisit::query()
            ->where('tenant_id', $tenantId)
            ->with(['unit'])
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (MarketplaceVisit $v): array => [
                'id' => $v->id,
                'unit' => $v->unit?->name,
                'visit_date' => $v->visit_date?->toDateString(),
                'status' => $v->status,
            ]);

        return Inertia::render('marketplace/admin/bookings', [
            'visits' => $visits,
        ]);
    }

    /**
     * Admin: communities page.
     */
    public function adminCommunities(Request $request): Response
    {
        $tenantId = $request->user()?->tenant_id;

        $communities = Community::query()
            ->where('tenant_id', $tenantId)
            ->where('is_marketplace', true)
            ->with(['city'])
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Community $c): array => [
                'id' => $c->id,
                'name' => $c->name,
                'city' => $c->city?->name,
                'is_marketplace' => $c->is_marketplace,
            ]);

        return Inertia::render('marketplace/admin/communities', [
            'communities' => $communities,
        ]);
    }

    /**
     * Admin: community detail page.
     */
    public function adminCommunityShow(Community $community): Response
    {
        return Inertia::render('marketplace/admin/community-show', [
            'community' => [
                'id' => $community->id,
                'name' => $community->name,
                'description' => $community->description,
                'is_marketplace' => $community->is_marketplace,
            ],
        ]);
    }

    /**
     * Admin: units page.
     */
    public function adminUnits(Request $request): Response
    {
        $tenantId = $request->user()?->tenant_id;

        $units = Unit::query()
            ->where('tenant_id', $tenantId)
            ->where('is_marketplace', true)
            ->with(['community', 'building', 'type'])
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Unit $u): array => [
                'id' => $u->id,
                'name' => $u->name,
                'community' => $u->community?->name,
                'building' => $u->building?->name,
                'type' => $u->type?->name,
                'market_rent' => $u->market_rent,
            ]);

        return Inertia::render('marketplace/admin/units', [
            'units' => $units,
        ]);
    }

    /**
     * Admin: unit detail page.
     */
    public function adminUnitShow(Unit $unit): Response
    {
        $unit->load(['community', 'building', 'type', 'category', 'status']);

        return Inertia::render('marketplace/admin/unit-show', [
            'unit' => [
                'id' => $unit->id,
                'name' => $unit->name,
                'community' => $unit->community?->name,
                'building' => $unit->building?->name,
                'type' => $unit->type?->name,
                'category' => $unit->category?->name,
                'market_rent' => $unit->market_rent,
                'net_area' => $unit->net_area,
                'floor_no' => $unit->floor_no,
                'photos' => $unit->photos,
            ],
        ]);
    }

    /**
     * Admin: visits page.
     */
    public function adminVisits(Request $request): Response
    {
        $tenantId = $request->user()?->tenant_id;

        $visits = MarketplaceVisit::query()
            ->where('tenant_id', $tenantId)
            ->with(['marketplaceUnit', 'marketplaceUnit.unit'])
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (MarketplaceVisit $v): array => [
                'id' => $v->id,
                'unit' => $v->marketplaceUnit?->unit?->name,
                'visit_date' => $v->visit_date?->toDateString(),
                'status' => $v->status?->name,
            ]);

        return Inertia::render('marketplace/admin/visits', [
            'visits' => $visits,
        ]);
    }

    /**
     * Admin: visit detail page.
     */
    public function adminVisitShow(MarketplaceVisit $visit): Response
    {
        $visit->load(['marketplaceUnit', 'marketplaceUnit.unit', 'marketplaceUnit.unit.community', 'marketplaceUnit.unit.building', 'status']);

        return Inertia::render('marketplace/admin/visit-show', [
            'visit' => [
                'id' => $visit->id,
                'unit' => $visit->marketplaceUnit?->unit?->name,
                'community' => $visit->marketplaceUnit?->unit?->community?->name,
                'building' => $visit->marketplaceUnit?->unit?->building?->name,
                'visit_date' => $visit->visit_date?->toDateString(),
                'status' => $visit->status?->name,
            ],
        ]);
    }

    /**
     * Admin: settings page.
     */
    public function adminSettings(Request $request): Response
    {
        $tenantId = $request->user()?->tenant_id;

        $settings = MarketplaceSetting::query()
            ->where('tenant_id', $tenantId)
            ->first();

        return Inertia::render('marketplace/admin/settings', [
            'settings' => $settings ? [
                'id' => $settings->id,
                'is_marketplace_enabled' => $settings->is_marketplace_enabled ?? false,
            ] : null,
        ]);
    }
}
