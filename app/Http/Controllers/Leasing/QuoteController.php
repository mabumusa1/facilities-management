<?php

namespace App\Http\Controllers\Leasing;

use App\Console\Commands\ExpireLeaseQuotes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Leasing\QuoteReviseRequest;
use App\Http\Requests\Leasing\QuoteStoreRequest;
use App\Models\ContractType;
use App\Models\LeaseQuote;
use App\Models\Resident;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Unit;
use App\Support\StatusWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            'can' => [
                'send' => $this->can('send', $quote),
                'revise' => $this->can('revise', $quote),
                'reject' => $this->can('reject', $quote),
                'expire' => $this->can('expire', $quote),
            ],
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

    public function revise(LeaseQuote $quote): Response
    {
        $this->authorize('revise', $quote);

        $quote->load([
            'unit',
            'contact',
            'contractType',
            'status',
            'paymentFrequency',
            'parentQuote',
        ]);

        /** @var LeaseQuote $previous */
        $previous = $quote->parentQuote ?? $quote;

        $diff = [
            'rent_amount' => ['old' => $previous->rent_amount, 'new' => $quote->rent_amount],
            'security_deposit' => ['old' => $previous->security_deposit, 'new' => $quote->security_deposit],
            'duration_months' => ['old' => $previous->duration_months, 'new' => $quote->duration_months],
            'valid_until' => ['old' => $previous->valid_until, 'new' => $quote->valid_until],
            'special_conditions' => ['old' => $previous->special_conditions, 'new' => $quote->special_conditions],
        ];

        return Inertia::render('leasing/quotes/Revise', [
            'quote' => $quote,
            'diff' => $diff,
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

    public function storeRevision(QuoteReviseRequest $request, LeaseQuote $quote, StatusWorkflow $statusWorkflow): RedirectResponse
    {
        $this->authorize('revise', $quote);

        $validated = $request->validated();

        $revision = LeaseQuote::create([
            ...$validated,
            'parent_quote_id' => $quote->id,
            'version' => $quote->version + 1,
            'status_id' => ExpireLeaseQuotes::STATUS_DRAFT,
            'created_by_id' => $request->user()->id,
            'security_deposit' => $validated['security_deposit'] ?? 0,
        ]);

        $this->sendQuote($revision, $statusWorkflow);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Revision sent to prospect.'),
        ]);

        return to_route('quotes.show', $revision);
    }

    public function reject(Request $request, LeaseQuote $quote, StatusWorkflow $statusWorkflow): RedirectResponse
    {
        $this->authorize('reject', $quote);

        $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $statusWorkflow->ensureTransition('lease_quote', (int) $quote->status_id, ExpireLeaseQuotes::STATUS_REJECTED);

        $quote->update([
            'status_id' => ExpireLeaseQuotes::STATUS_REJECTED,
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Quote marked as rejected.')]);

        return to_route('quotes.show', $quote);
    }

    public function expire(LeaseQuote $quote, StatusWorkflow $statusWorkflow): RedirectResponse
    {
        $this->authorize('expire', $quote);

        $statusWorkflow->ensureTransition('lease_quote', (int) $quote->status_id, ExpireLeaseQuotes::STATUS_EXPIRED);

        $quote->update(['status_id' => ExpireLeaseQuotes::STATUS_EXPIRED]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Quote marked as expired.')]);

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
