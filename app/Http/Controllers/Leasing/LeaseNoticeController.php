<?php

namespace App\Http\Controllers\Leasing;

use App\Enums\LeaseNoticeType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Leasing\StoreLeaseNoticeRequest;
use App\Models\Lease;
use App\Models\LeaseNotice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LeaseNoticeController extends Controller
{
    /**
     * Display the notice history and send-notice form for a lease.
     */
    public function index(Lease $lease): Response
    {
        $this->authorize('viewAny', [LeaseNotice::class, $lease]);

        $lease->load(['tenant', 'status']);

        $notices = LeaseNotice::query()
            ->where('lease_id', $lease->id)
            ->with('sentBy')
            ->orderByDesc('sent_at')
            ->paginate(15);

        return Inertia::render('leasing/leases/Notices', [
            'lease' => $lease,
            'tenant' => $lease->tenant,
            'notices' => $notices,
            'noticeTypes' => array_map(
                fn (LeaseNoticeType $type) => ['value' => $type->value, 'label' => $type->value],
                LeaseNoticeType::cases()
            ),
        ]);
    }

    /**
     * Store a new notice and record it as sent.
     */
    public function store(StoreLeaseNoticeRequest $request, Lease $lease): RedirectResponse
    {
        $this->authorize('create', [LeaseNotice::class, $lease]);

        $tenant = $lease->tenant;

        if (empty($tenant?->email)) {
            return back()->withErrors([
                'tenant_email' => __('Tenant email address is missing. Update the Resident contact before sending notices.'),
            ]);
        }

        LeaseNotice::create([
            'lease_id' => $lease->id,
            'tenant_id' => $lease->tenant_id,
            'sent_by' => auth()->id(),
            'type' => $request->validated('type'),
            'subject_en' => $request->validated('subject_en'),
            'body_en' => $request->validated('body_en'),
            'subject_ar' => $request->validated('subject_ar'),
            'body_ar' => $request->validated('body_ar'),
            'sent_at' => now(),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Notice sent to :email', ['email' => $tenant->email])]);

        return to_route('leases.notices.index', $lease);
    }

    /**
     * Return a single notice body as JSON for accordion expansion.
     */
    public function show(Lease $lease, LeaseNotice $notice): JsonResponse
    {
        $this->authorize('view', $notice);

        return response()->json([
            'id' => $notice->id,
            'type' => $notice->type,
            'subject_en' => $notice->subject_en,
            'body_en' => $notice->body_en,
            'subject_ar' => $notice->subject_ar,
            'body_ar' => $notice->body_ar,
            'sent_at' => $notice->sent_at,
        ]);
    }
}
