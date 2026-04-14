<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Community;
use App\Models\Contact;
use App\Models\Facility;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DirectoryController extends Controller
{
    /**
     * Main directory page.
     */
    public function index(Request $request): Response
    {
        $tenantId = $request->user()?->tenant_id;

        return Inertia::render('directory/index', [
            'communities_count' => Community::query()->where('tenant_id', $tenantId)->count(),
            'buildings_count' => Building::query()->where('tenant_id', $tenantId)->count(),
            'facilities_count' => Facility::query()->where('tenant_id', $tenantId)->count(),
            'contacts_count' => Contact::query()->where('tenant_id', $tenantId)->count(),
        ]);
    }

    /**
     * Directory: facilities listing.
     */
    public function facilities(Request $request): Response
    {
        $tenantId = $request->user()?->tenant_id;

        $facilities = Facility::query()
            ->where('tenant_id', $tenantId)
            ->with(['community', 'category'])
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Facility $f): array => [
                'id' => $f->id,
                'name' => $f->name_en,
                'community' => $f->community?->name,
                'category' => $f->category?->name,
                'is_active' => $f->is_active,
            ]);

        return Inertia::render('directory/facilities', [
            'facilities' => $facilities,
        ]);
    }

    /**
     * Directory: add new facility page.
     */
    public function addFacility(Request $request): Response
    {
        $tenantId = $request->user()?->tenant_id;

        return Inertia::render('directory/add-facility', [
            'communities' => Community::query()
                ->where('tenant_id', $tenantId)
                ->select('id', 'name')
                ->get(),
        ]);
    }

    /**
     * Directory: facility detail page.
     */
    public function facilityShow(Facility $facility): Response
    {
        $facility->load(['community', 'category']);

        return Inertia::render('directory/facility-show', [
            'facility' => [
                'id' => $facility->id,
                'name' => $facility->name_en,
                'description' => $facility->description,
                'community' => $facility->community?->name,
                'category' => $facility->category?->name,
                'is_active' => $facility->is_active,
            ],
        ]);
    }

    /**
     * Directory: building detail page.
     */
    public function buildingShow(Building $building): Response
    {
        $building->load(['community', 'city', 'district']);

        return Inertia::render('directory/building-show', [
            'building' => [
                'id' => $building->id,
                'name' => $building->name,
                'community' => $building->community?->name,
                'city' => $building->city?->name,
                'district' => $building->district?->name,
                'no_floors' => $building->no_floors,
                'year_built' => $building->year_built,
            ],
        ]);
    }

    /**
     * Directory: community detail page.
     */
    public function communityShow(Community $community): Response
    {
        $community->load(['city', 'district']);

        return Inertia::render('directory/community-show', [
            'community' => [
                'id' => $community->id,
                'name' => $community->name,
                'description' => $community->description,
                'city' => $community->city?->name,
                'district' => $community->district?->name,
                'is_marketplace' => $community->is_marketplace,
            ],
        ]);
    }

    /**
     * Directory: owner page.
     */
    public function owner(Request $request): Response
    {
        $tenantId = $request->user()?->tenant_id;

        $owners = Contact::query()
            ->where('tenant_id', $tenantId)
            ->where('contact_type', 'owner')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Contact $c): array => [
                'id' => $c->id,
                'name' => trim($c->first_name.' '.$c->last_name),
                'email' => $c->email,
                'phone_number' => $c->phone_number,
            ]);

        return Inertia::render('directory/owner', [
            'owners' => $owners,
        ]);
    }

    /**
     * Directory: documents page.
     */
    public function documents(): Response
    {
        return Inertia::render('directory/documents');
    }
}
