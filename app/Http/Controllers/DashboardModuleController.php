<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\FacilityBooking;
use App\Models\Lease;
use App\Models\MarketplaceOffer;
use App\Models\ServiceRequest;
use App\Models\Transaction;
use App\Models\VisitorAccess;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardModuleController extends Controller
{
    public function bookings(Request $request): Response
    {
        $tenantId = (int) ($request->user()?->tenant_id ?? 0);

        return $this->renderListPage(
            title: 'Bookings',
            endpoint: '/dashboard/bookings',
            items: FacilityBooking::query()
                ->where('tenant_id', $tenantId)
                ->latest()
                ->take(10)
                ->get()
                ->map(fn (FacilityBooking $booking) => [
                    'id' => $booking->id,
                    'label' => 'Booking #'.$booking->id,
                    'status_id' => $booking->status_id,
                ])
                ->values()
                ->all(),
        );
    }

    public function bookingDetails(FacilityBooking $facilityBooking): Response
    {
        return $this->renderDetailPage(
            title: 'Booking Details',
            endpoint: '/dashboard/bookings/'.$facilityBooking->id,
            item: [
                'id' => $facilityBooking->id,
                'status_id' => $facilityBooking->status_id,
                'booking_date' => $facilityBooking->booking_date?->toDateString(),
                'start_time' => $facilityBooking->start_time,
                'end_time' => $facilityBooking->end_time,
            ],
        );
    }

    public function bookingContracts(Request $request): Response
    {
        $tenantId = (int) ($request->user()?->tenant_id ?? 0);

        return $this->renderListPage(
            title: 'Booking Contracts',
            endpoint: '/dashboard/booking-contracts',
            items: MarketplaceOffer::query()
                ->where('tenant_id', $tenantId)
                ->latest()
                ->take(10)
                ->get()
                ->map(fn (MarketplaceOffer $offer) => [
                    'id' => $offer->id,
                    'label' => $offer->offer_reference ?: 'Offer #'.$offer->id,
                    'status_id' => $offer->status_id,
                ])
                ->values()
                ->all(),
        );
    }

    public function bookingContractDetails(MarketplaceOffer $marketplaceOffer): Response
    {
        return $this->renderDetailPage(
            title: 'Booking Contract Details',
            endpoint: '/dashboard/booking-contracts/'.$marketplaceOffer->id,
            item: [
                'id' => $marketplaceOffer->id,
                'offer_reference' => $marketplaceOffer->offer_reference,
                'status_id' => $marketplaceOffer->status_id,
                'offer_amount' => $marketplaceOffer->offer_amount,
            ],
        );
    }

    public function visits(Request $request): Response
    {
        $tenantId = (int) ($request->user()?->tenant_id ?? 0);

        return $this->renderListPage(
            title: 'Visits',
            endpoint: '/dashboard/visits',
            items: VisitorAccess::query()
                ->where('tenant_id', $tenantId)
                ->latest()
                ->take(10)
                ->get()
                ->map(fn (VisitorAccess $visit) => [
                    'id' => $visit->id,
                    'label' => $visit->visitor_name,
                    'status_id' => $visit->status_id,
                ])
                ->values()
                ->all(),
        );
    }

    public function complaints(): Response
    {
        return $this->renderListPage(
            title: 'Complaints',
            endpoint: '/dashboard/complaints',
            items: ServiceRequest::query()
                ->latest()
                ->take(10)
                ->get()
                ->map(fn (ServiceRequest $request) => [
                    'id' => $request->id,
                    'label' => $request->title,
                    'status_id' => $request->status_id,
                ])
                ->values()
                ->all(),
        );
    }

    public function complaintDetails(ServiceRequest $serviceRequest): Response
    {
        return $this->renderDetailPage(
            title: 'Complaint Details',
            endpoint: '/dashboard/complaints/'.$serviceRequest->id,
            item: [
                'id' => $serviceRequest->id,
                'title' => $serviceRequest->title,
                'description' => $serviceRequest->description,
                'status_id' => $serviceRequest->status_id,
            ],
        );
    }

    public function suggestions(): Response
    {
        return $this->renderListPage(
            title: 'Suggestions',
            endpoint: '/dashboard/suggestions',
            items: ServiceRequest::query()
                ->latest()
                ->take(10)
                ->get()
                ->map(fn (ServiceRequest $request) => [
                    'id' => $request->id,
                    'label' => $request->title,
                    'status_id' => $request->status_id,
                ])
                ->values()
                ->all(),
        );
    }

    public function suggestionDetails(ServiceRequest $serviceRequest): Response
    {
        return $this->renderDetailPage(
            title: 'Suggestion Details',
            endpoint: '/dashboard/suggestions/'.$serviceRequest->id,
            item: [
                'id' => $serviceRequest->id,
                'title' => $serviceRequest->title,
                'description' => $serviceRequest->description,
                'status_id' => $serviceRequest->status_id,
            ],
        );
    }

    public function reports(): Response
    {
        return $this->renderListPage(
            title: 'Reports',
            endpoint: '/dashboard/reports',
            items: [
                ['id' => 'system', 'label' => 'System Reports'],
                ['id' => 'power-bi', 'label' => 'Power BI Reports'],
            ],
        );
    }

    public function payment(Request $request): Response
    {
        $tenantId = (int) ($request->user()?->tenant_id ?? 0);

        return $this->renderListPage(
            title: 'Payment',
            endpoint: '/dashboard/payment',
            items: Transaction::query()
                ->where('tenant_id', $tenantId)
                ->latest()
                ->take(10)
                ->get()
                ->map(fn (Transaction $transaction) => [
                    'id' => $transaction->id,
                    'label' => $transaction->lease_number,
                    'amount' => $transaction->amount,
                ])
                ->values()
                ->all(),
        );
    }

    public function offers(Request $request): Response
    {
        $tenantId = (int) ($request->user()?->tenant_id ?? 0);

        return $this->renderListPage(
            title: 'Offers',
            endpoint: '/dashboard/offers',
            items: MarketplaceOffer::query()
                ->where('tenant_id', $tenantId)
                ->latest()
                ->take(10)
                ->get()
                ->map(fn (MarketplaceOffer $offer) => [
                    'id' => $offer->id,
                    'label' => $offer->offer_reference ?: 'Offer #'.$offer->id,
                    'status_id' => $offer->status_id,
                ])
                ->values()
                ->all(),
        );
    }

    public function offerCreate(): Response
    {
        return Inertia::render('dashboard/form', [
            'title' => 'Create Offer',
            'endpoint' => '/dashboard/offers/create',
        ]);
    }

    public function offerView(MarketplaceOffer $marketplaceOffer): Response
    {
        return $this->renderDetailPage(
            title: 'Offer Details',
            endpoint: '/dashboard/offers/'.$marketplaceOffer->id.'/view',
            item: [
                'id' => $marketplaceOffer->id,
                'offer_reference' => $marketplaceOffer->offer_reference,
                'status_id' => $marketplaceOffer->status_id,
                'offer_amount' => $marketplaceOffer->offer_amount,
            ],
        );
    }

    public function directory(Request $request): Response
    {
        $tenantId = (int) ($request->user()?->tenant_id ?? 0);

        return $this->renderListPage(
            title: 'Directory',
            endpoint: '/dashboard/directory',
            items: Contact::query()
                ->where('tenant_id', $tenantId)
                ->latest()
                ->take(10)
                ->get()
                ->map(fn (Contact $contact) => [
                    'id' => $contact->id,
                    'label' => trim($contact->first_name.' '.$contact->last_name),
                    'type' => $contact->contact_type,
                ])
                ->values()
                ->all(),
        );
    }

    public function directoryCreate(): Response
    {
        return Inertia::render('dashboard/form', [
            'title' => 'Create Directory Entry',
            'endpoint' => '/dashboard/directory/create',
        ]);
    }

    public function directoryDetails(Contact $contact): Response
    {
        return $this->renderDetailPage(
            title: 'Directory Details',
            endpoint: '/dashboard/directory/'.$contact->id,
            item: [
                'id' => $contact->id,
                'first_name' => $contact->first_name,
                'last_name' => $contact->last_name,
                'contact_type' => $contact->contact_type,
                'email' => $contact->email,
            ],
        );
    }

    public function directoryUpdate(): Response
    {
        return Inertia::render('dashboard/form', [
            'title' => 'Update Directory Entry',
            'endpoint' => '/dashboard/directory/update',
        ]);
    }

    public function moveOutTenants(Request $request): Response
    {
        $tenantId = (int) ($request->user()?->tenant_id ?? 0);

        return $this->renderListPage(
            title: 'Move-out Tenants',
            endpoint: '/dashboard/move-out-tenants',
            items: Lease::query()
                ->where('tenant_id', $tenantId)
                ->latest()
                ->take(10)
                ->get()
                ->map(fn (Lease $lease) => [
                    'id' => $lease->id,
                    'label' => $lease->contract_number,
                    'status_id' => $lease->status_id,
                ])
                ->values()
                ->all(),
        );
    }

    public function moveOutTenantDetails(Lease $lease): Response
    {
        return $this->renderDetailPage(
            title: 'Move-out Tenant Details',
            endpoint: '/dashboard/move-out-tenants/'.$lease->id,
            item: [
                'id' => $lease->id,
                'lease_number' => $lease->contract_number,
                'status_id' => $lease->status_id,
                'end_date' => $lease->end_date?->toDateString(),
            ],
        );
    }

    public function systemReports(): Response
    {
        return $this->renderListPage(
            title: 'System Reports',
            endpoint: '/dashboard/system-reports',
            items: [
                ['id' => 'lease', 'label' => 'Lease'],
                ['id' => 'maintenance', 'label' => 'maintenance'],
            ],
        );
    }

    public function systemReportsLease(): Response
    {
        return $this->renderListPage(
            title: 'System Reports - Lease',
            endpoint: '/dashboard/system-reports/Lease',
            items: [],
        );
    }

    public function systemReportsMaintenance(): Response
    {
        return $this->renderListPage(
            title: 'System Reports - maintenance',
            endpoint: '/dashboard/system-reports/maintenance',
            items: [],
        );
    }

    public function powerBiReports(): Response
    {
        return $this->renderListPage(
            title: 'Power BI Reports',
            endpoint: '/dashboard/power-bi-reports',
            items: [],
        );
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function renderListPage(string $title, string $endpoint, array $items): Response
    {
        return Inertia::render('dashboard/list', [
            'title' => $title,
            'endpoint' => $endpoint,
            'items' => $items,
        ]);
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function renderDetailPage(string $title, string $endpoint, array $item): Response
    {
        return Inertia::render('dashboard/detail', [
            'title' => $title,
            'endpoint' => $endpoint,
            'item' => $item,
        ]);
    }
}
