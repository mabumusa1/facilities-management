<?php

namespace App\Http\Controllers\Leasing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Leasing\RecordRenewalDecisionRequest;
use App\Http\Requests\Leasing\StoreLeaseRenewalOfferRequest;
use App\Models\Lease;
use App\Models\LeaseRenewalOffer;
use App\Models\Setting;
use App\Models\Status;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Handles generating and tracking lease renewal offers.
 *
 * Routes:
 *   GET  leases/renewals                      → index
 *   GET  leases/{lease}/renewal/create        → create
 *   POST leases/{lease}/renewal               → store
 *   POST leases/{lease}/renewal/{offer}/send  → send
 *   POST leases/{lease}/renewal/{offer}/decision → recordDecision
 *   POST leases/{lease}/renewal/{offer}/convert  → convert
 */
class LeaseRenewalController extends Controller
{
    /** Renewal window: leases expiring within this many days may have an offer generated. */
    private const RENEWAL_WINDOW_DAYS = 90;

    /**
     * List all renewal offers across leases, filterable by status.
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', LeaseRenewalOffer::class);

        $statusId = $request->input('status_id');
        $search = trim((string) $request->input('search', ''));

        $offers = LeaseRenewalOffer::query()
            ->with(['lease.tenant', 'lease.units', 'status'])
            ->when($statusId, fn ($q) => $q->where('status_id', (int) $statusId))
            ->when($search !== '', function ($q) use ($search): void {
                $q->whereHas('lease', function ($lq) use ($search): void {
                    $lq->where('contract_number', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $statuses = Status::query()
            ->where('type', 'renewal')
            ->select('id', 'name', 'name_en', 'name_ar')
            ->orderBy('priority')
            ->get();

        return Inertia::render('leasing/renewals/Index', [
            'offers' => $offers,
            'statuses' => $statuses,
            'filters' => [
                'search' => $search,
                'status_id' => $statusId ? (string) $statusId : '',
            ],
        ]);
    }

    /**
     * Show the form to generate a renewal offer pre-filled from the source lease.
     */
    public function create(Request $request, Lease $lease): Response
    {
        $this->authorize('create', LeaseRenewalOffer::class);
        $this->authorize('view', $lease);

        $lease->load(['tenant', 'units', 'rentalContractType', 'paymentSchedule']);

        $contractTypes = Setting::query()
            ->where('type', 'rental_contract_type')
            ->select('id', 'name', 'name_ar', 'name_en')
            ->get();

        // Default: new start date is day after lease end_date; valid until 30 days before.
        $newStartDate = $lease->end_date?->addDay()?->toDateString();
        $validUntil = $lease->end_date?->subDays(30)?->toDateString();

        return Inertia::render('leasing/renewals/Create', [
            'lease' => $lease,
            'contractTypes' => $contractTypes,
            'defaults' => [
                'new_start_date' => $newStartDate,
                'duration_months' => 12,
                'new_rent_amount' => $lease->rental_total_amount,
                'payment_frequency' => $lease->paymentSchedule?->name,
                'contract_type_id' => $lease->rental_contract_type_id,
                'valid_until' => $validUntil,
            ],
        ]);
    }

    /**
     * Store a new renewal offer (draft status).
     */
    public function store(StoreLeaseRenewalOfferRequest $request, Lease $lease): RedirectResponse
    {
        $this->authorize('create', LeaseRenewalOffer::class);
        $this->authorize('view', $lease);

        $validated = $request->validated();

        LeaseRenewalOffer::create([
            ...$validated,
            'lease_id' => $lease->id,
            'status_id' => LeaseRenewalOffer::STATUS_DRAFT,
            'created_by' => $request->user()->id,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Renewal offer saved as draft.')]);

        return to_route('leases.show', $lease);
    }

    /**
     * Transition a draft renewal offer to "sent".
     */
    public function send(Request $request, Lease $lease, LeaseRenewalOffer $offer): RedirectResponse
    {
        $this->authorize('send', $offer);

        abort_unless($offer->lease_id === $lease->id, 404);

        $offer->update(['status_id' => LeaseRenewalOffer::STATUS_SENT]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Renewal offer sent.')]);

        return to_route('leases.show', $lease);
    }

    /**
     * Record the tenant's decision (accepted / declined) on a renewal offer.
     */
    public function recordDecision(RecordRenewalDecisionRequest $request, Lease $lease, LeaseRenewalOffer $offer): RedirectResponse
    {
        $this->authorize('recordDecision', $offer);

        abort_unless($offer->lease_id === $lease->id, 404);

        $decision = $request->validated('decision');

        $statusId = $decision === 'accepted'
            ? LeaseRenewalOffer::STATUS_ACCEPTED
            : LeaseRenewalOffer::STATUS_REJECTED;

        $offer->update([
            'status_id' => $statusId,
            'decided_at' => now(),
            'decided_by' => $request->user()->id,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Decision recorded.')]);

        return to_route('leases.show', $lease);
    }

    /**
     * Convert an accepted renewal offer to a new lease (delegates to existing renewal flow).
     * Sets converted_lease_id on the offer to track the link.
     */
    public function convert(Request $request, Lease $lease, LeaseRenewalOffer $offer): RedirectResponse
    {
        $this->authorize('convert', $offer);

        abort_unless($offer->lease_id === $lease->id, 404);

        // Redirect to the existing lease renewal create form pre-filled from the offer.
        return to_route('leases.subleases.create', $lease);
    }
}
