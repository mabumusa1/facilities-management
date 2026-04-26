<?php

namespace App\Http\Controllers\Leasing;

use App\Console\Commands\ExpireLeaseQuotes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Leasing\QuoteStoreRequest;
use App\Models\Admin;
use App\Models\ContractType;
use App\Models\Lease;
use App\Models\LeaseQuote;
use App\Models\Resident;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Unit;
use App\Models\UnitCategory;
use App\Support\StatusWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class QuoteController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', LeaseQuote::class);

        $search = trim((string) $request->input('search', ''));
        $statusId = $request->input('status_id');
        $perPage = min(max((int) $request->integer('per_page', 15), 5), 50);

        $quotes = LeaseQuote::query()
            ->with(['unit', 'contact', 'status'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested->where('quote_number', 'like', "%{$search}%")
                        ->orWhereHas('contact', function ($contactQuery) use ($search): void {
                            $contactQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($statusId, fn ($query) => $query->where('status_id', (int) $statusId))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('leasing/quotes/Index', [
            'quotes' => $quotes,
            'statuses' => Status::query()
                ->where('type', 'lease_quote')
                ->select('id', 'name', 'name_en')
                ->orderBy('priority')
                ->orderBy('id')
                ->get(),
            'filters' => [
                'search' => $search,
                'status_id' => $statusId ? (string) $statusId : '',
                'per_page' => (string) $perPage,
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', LeaseQuote::class);

        return Inertia::render('leasing/quotes/Create', [
            'units' => Unit::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get(),
            'contacts' => Resident::query()
                ->select('id', 'first_name', 'last_name')
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(),
            'contractTypes' => ContractType::query()
                ->select('id', 'name_en', 'name_ar')
                ->orderByRaw('COALESCE(name_en, name_ar) asc')
                ->get(),
            'paymentFrequencies' => Setting::query()
                ->where('type', 'payment_frequency')
                ->select('id', 'name', 'name_en', 'name_ar')
                ->orderByRaw('COALESCE(name_en, name) asc')
                ->get(),
        ]);
    }

    public function store(QuoteStoreRequest $request, StatusWorkflow $statusWorkflow): RedirectResponse
    {
        $this->authorize('create', LeaseQuote::class);

        $validated = $request->validated();
        $action = $validated['action'];
        unset($validated['action']);

        $validated['status_id'] = ExpireLeaseQuotes::STATUS_DRAFT;
        $validated['created_by_id'] = $request->user()->id;
        $validated['security_deposit'] = $validated['security_deposit'] ?? 0;

        /** @var LeaseQuote $quote */
        $quote = LeaseQuote::create($validated);

        if ($action === 'send') {
            $this->sendQuote($quote, $statusWorkflow);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $action === 'send'
                ? __('Quote sent to prospect.')
                : __('Quote saved as draft.'),
        ]);

        return to_route('quotes.show', $quote);
    }

    public function show(LeaseQuote $quote): Response
    {
        $this->authorize('view', $quote);

        $quote->load([
            'unit',
            'contact',
            'contractType',
            'status',
            'paymentFrequency',
            'createdBy',
            'parentQuote',
            'revisions.status',
        ]);

        return Inertia::render('leasing/quotes/Show', [
            'quote' => $quote,
        ]);
    }

    public function send(Request $request, LeaseQuote $quote, StatusWorkflow $statusWorkflow): RedirectResponse
    {
        $this->authorize('send', $quote);

        $statusWorkflow->ensureTransition('lease_quote', (int) $quote->status_id, ExpireLeaseQuotes::STATUS_SENT);

        if (empty($quote->public_token)) {
            $quote->public_token = (string) Str::uuid();
        }

        $quote->update([
            'status_id' => ExpireLeaseQuotes::STATUS_SENT,
            'public_token' => $quote->public_token,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Quote sent to prospect.')]);

        return to_route('quotes.show', $quote);
    }

    public function preview(string $token): Response
    {
        $quote = LeaseQuote::query()
            ->where('public_token', $token)
            ->with(['unit', 'contact', 'contractType', 'status', 'paymentFrequency'])
            ->firstOrFail();

        // Atomically transition from sent → viewed on first open.
        if ((int) $quote->status_id === ExpireLeaseQuotes::STATUS_SENT) {
            $quote->update(['status_id' => ExpireLeaseQuotes::STATUS_VIEWED]);
            $quote->status_id = ExpireLeaseQuotes::STATUS_VIEWED;
        }

        return Inertia::render('leasing/quotes/Preview', [
            'quote' => $quote,
        ]);
    }

    /**
     * Show the pre-filled lease creation form for an accepted quote.
     * If the quote already has a lease, redirect to the KYC page.
     */
    public function convert(LeaseQuote $quote): Response|RedirectResponse
    {
        $this->authorize('convert', $quote);

        $quote->load(['unit', 'contact', 'contractType', 'status', 'paymentFrequency', 'lease']);

        // Duplicate-convert guard: redirect to existing lease KYC if already converted.
        if ($quote->lease instanceof Lease) {
            return to_route('leases.kyc', $quote->lease);
        }

        return Inertia::render('leasing/quotes/Convert', [
            'quote' => $quote,
            'unitCategories' => UnitCategory::query()->select('id', 'name', 'name_en', 'name_ar', 'icon')->get(),
            'rentalContractTypes' => Setting::query()->where('type', 'rental_contract_type')->select('id', 'name', 'name_en', 'name_ar')->get(),
            'paymentSchedules' => Setting::query()->where('type', 'payment_schedule')->select('id', 'name', 'name_en', 'name_ar', 'parent_id')->get(),
            'units' => Unit::query()->select('id', 'name')->orderBy('name')->get(),
            'residents' => Resident::query()->select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
            'admins' => Admin::query()->select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
        ]);
    }

    /**
     * Store a new Lease created from an accepted quote, then redirect to KYC.
     *
     * Business rules enforced here:
     * - Quote must be in Accepted status (ID 73).
     * - Operation is wrapped in a DB transaction (Lease + lease_units + quote_id linkage).
     * - Idempotent: if a lease already exists for this quote, redirect rather than duplicate.
     */
    public function storeConversion(Request $request, LeaseQuote $quote): RedirectResponse
    {
        $this->authorize('convert', $quote);

        // Guard: only Accepted quotes can be converted.
        if ((int) $quote->status_id !== ExpireLeaseQuotes::STATUS_ACCEPTED) {
            return back()->withErrors(['quote' => __('Only accepted quotes can be converted to a lease application.')]);
        }

        // Idempotent: redirect if already converted.
        $existingLease = Lease::query()->where('quote_id', $quote->id)->first();

        if ($existingLease instanceof Lease) {
            return to_route('leases.kyc', $existingLease);
        }

        $validated = $request->validate([
            'contract_number' => ['nullable', 'string', 'unique:rf_leases,contract_number'],
            'autoGenerateLeaseNumber' => ['nullable'],
            'tenant_id' => ['required', 'integer', 'exists:rf_tenants,id'],
            'lease_unit_type_id' => ['required', 'integer', 'exists:rf_unit_categories,id'],
            'rental_contract_type_id' => ['required', 'integer', 'exists:rf_settings,id'],
            'payment_schedule_id' => ['required', 'integer', 'exists:rf_settings,id'],
            'deal_owner_id' => ['nullable', 'integer', 'exists:rf_admins,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'handover_date' => ['required', 'date'],
            'tenant_type' => ['required', 'in:individual,company'],
            'rental_type' => ['required', 'in:total,detailed'],
            'rental_total_amount' => ['required', 'numeric', 'min:0'],
            'security_deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'terms_conditions' => ['nullable', 'string'],
            'number_of_months' => ['nullable', 'integer', 'min:0'],
            'number_of_years' => ['nullable', 'integer', 'min:0'],
            'number_of_days' => ['nullable', 'integer', 'min:0'],
            'unit_id' => ['required', 'integer', 'exists:rf_units,id'],
        ]);

        $autoGenerateLeaseNumber = filter_var($validated['autoGenerateLeaseNumber'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
        $contractNumber = $validated['contract_number'] ?? null;

        if ($contractNumber === null && $autoGenerateLeaseNumber) {
            $contractNumber = $this->generateConvertedLeaseNumber();
        }

        if ($contractNumber === null) {
            return back()->withErrors(['contract_number' => __('Lease contract number is required.')]);
        }

        $lease = DB::transaction(function () use ($validated, $contractNumber, $quote, $request): Lease {
            $lease = Lease::create([
                'contract_number' => $contractNumber,
                'tenant_id' => $validated['tenant_id'],
                'status_id' => KycController::STATUS_PENDING_APPLICATION,
                'lease_unit_type_id' => $validated['lease_unit_type_id'],
                'rental_contract_type_id' => $validated['rental_contract_type_id'],
                'payment_schedule_id' => $validated['payment_schedule_id'],
                'deal_owner_id' => $validated['deal_owner_id'] ?? null,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'handover_date' => $validated['handover_date'],
                'tenant_type' => $validated['tenant_type'],
                'rental_type' => $validated['rental_type'],
                'rental_total_amount' => $validated['rental_total_amount'],
                'security_deposit_amount' => $validated['security_deposit_amount'] ?? null,
                'terms_conditions' => $validated['terms_conditions'] ?? null,
                'number_of_months' => $validated['number_of_months'] ?? null,
                'number_of_years' => $validated['number_of_years'] ?? null,
                'number_of_days' => $validated['number_of_days'] ?? null,
                'created_by_id' => $request->user()->id,
                'account_tenant_id' => $quote->account_tenant_id,
                'quote_id' => $quote->id,
            ]);

            // Attach the unit from the quote (or the submitted unit_id if the user changed it).
            $lease->units()->sync([
                (int) $validated['unit_id'] => [
                    'rental_annual_type' => null,
                    'annual_rental_amount' => $validated['rental_total_amount'],
                    'net_area' => null,
                    'meter_cost' => null,
                ],
            ]);

            return $lease;
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Lease application created. Complete KYC documents to proceed.')]);

        return to_route('leases.kyc', $lease);
    }

    /**
     * Generate a unique lease contract number for conversions.
     */
    private function generateConvertedLeaseNumber(): string
    {
        do {
            $candidate = 'LEASE-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4));
        } while (Lease::query()->where('contract_number', $candidate)->exists());

        return $candidate;
    }

    /**
     * Transition a quote to "sent" status and ensure it has a public token.
     *
     * @throws \RuntimeException When the sent status is not found.
     */
    private function sendQuote(LeaseQuote $quote, StatusWorkflow $statusWorkflow): void
    {
        $statusWorkflow->ensureTransition('lease_quote', (int) $quote->status_id, ExpireLeaseQuotes::STATUS_SENT);

        $quote->update([
            'status_id' => ExpireLeaseQuotes::STATUS_SENT,
            'public_token' => $quote->public_token ?? (string) Str::uuid(),
        ]);
    }
}
