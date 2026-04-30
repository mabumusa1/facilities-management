<?php

namespace App\Http\Controllers\Leasing;

use App\Actions\ConvertLeadToContact;
use App\Http\Controllers\Controller;
use App\Http\Requests\Leasing\StoreLeadNoteRequest;
use App\Http\Requests\Leasing\StoreLeadRequest;
use App\Http\Requests\Leasing\UpdateLeadRequest;
use App\Models\AccountMembership;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadSource;
use App\Models\Owner;
use App\Models\Resident;
use App\Models\Status;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

    public function show(Lead $lead): Response
    {
        $this->authorize('view', $lead);

        $statuses = Status::query()
            ->where('type', 'lead')
            ->select('id', 'name', 'name_en', 'name_ar')
            ->orderBy('priority')
            ->orderBy('id')
            ->get();

        $tenant = Tenant::current();

        $teamMembers = AccountMembership::query()
            ->where('account_tenant_id', $tenant?->id)
            ->with('user:id,name,email')
            ->get()
            ->map(fn (AccountMembership $m): array => [
                'id' => $m->user_id,
                'name' => $m->user?->name ?? '',
                'email' => $m->user?->email ?? '',
            ])
            ->filter(fn (array $m): bool => $m['id'] !== null)
            ->values();

        $lead->load(['source', 'status', 'assignedTo', 'convertedContact']);

        $convertedContactData = null;

        if ($lead->isConverted() && $lead->convertedContact !== null) {
            $contact = $lead->convertedContact;
            $contactType = $lead->converted_contact_type === Owner::class ? 'owner' : 'resident';
            $contactRoute = $contactType === 'owner'
                ? route('owners.show', $contact->id)
                : route('residents.show', $contact->id);

            $convertedContactData = [
                'id' => $contact->id,
                'type' => $contactType,
                'name' => trim(($contact->first_name ?? '').' '.($contact->last_name ?? '')),
                'email' => $contact->email,
                'converted_at' => $lead->converted_at?->toJSON(),
                'url' => $contactRoute,
            ];
        }

        $authUser = request()->user();
        $canConvert = ! $lead->isConverted()
            && $lead->status?->name_en === 'Qualified'
            && $authUser?->can('convert', $lead);

        return Inertia::render('leasing/leads/Show', [
            'lead' => [
                'id' => $lead->id,
                'name' => $lead->name,
                'name_en' => $lead->name_en,
                'name_ar' => $lead->name_ar,
                'phone_number' => $lead->phone_number,
                'phone_country_code' => $lead->phone_country_code,
                'email' => $lead->email,
                'notes' => $lead->notes,
                'lost_reason' => $lead->lost_reason,
                'created_at' => $lead->created_at?->toJSON(),
                'is_converted' => $lead->isConverted(),
                'converted_contact' => $convertedContactData,
                'status' => $lead->status ? [
                    'id' => $lead->status->id,
                    'name' => $lead->status->name,
                    'name_en' => $lead->status->name_en,
                    'name_ar' => $lead->status->name_ar,
                ] : null,
                'source' => $lead->source ? [
                    'id' => $lead->source->id,
                    'name' => $lead->source->name,
                    'name_en' => $lead->source->name_en,
                    'name_ar' => $lead->source->name_ar,
                ] : null,
                'assigned_to' => $lead->assignedTo ? [
                    'id' => $lead->assignedTo->id,
                    'name' => $lead->assignedTo->name,
                    'email' => $lead->assignedTo->email,
                ] : null,
            ],
            'canConvert' => ! $lead->isConverted() && $lead->status?->name_en === 'Qualified',
            'statuses' => $statuses,
            'teamMembers' => $teamMembers,
            'activities' => Inertia::defer(function () use ($lead): array {
                return LeadActivity::query()
                    ->where('lead_id', $lead->id)
                    ->with('user:id,name')
                    ->latest('created_at')
                    ->limit(50)
                    ->get()
                    ->map(fn (LeadActivity $a): array => [
                        'id' => $a->id,
                        'type' => $a->type,
                        'data' => $a->data,
                        'created_at' => $a->created_at?->toJSON(),
                        'actor' => $a->user ? ['id' => $a->user->id, 'name' => $a->user->name] : null,
                    ])
                    ->all();
            }),
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

    public function update(UpdateLeadRequest $request, Lead $lead): RedirectResponse
    {
        $this->authorize('update', $lead);

        $oldStatus = $lead->status;
        $validated = $request->validated();

        $lead->update([
            'status_id' => $validated['status_id'],
            'lost_reason' => $validated['lost_reason'] ?? null,
        ]);

        // Record status change activity if status actually changed
        if ($oldStatus?->id !== (int) $validated['status_id']) {
            $newStatus = Status::find($validated['status_id']);
            LeadActivity::create([
                'lead_id' => $lead->id,
                'user_id' => $request->user()?->id,
                'type' => LeadActivity::TYPE_STATUS_CHANGE,
                'data' => [
                    'from' => $oldStatus?->name_en ?? $oldStatus?->name,
                    'to' => $newStatus?->name_en ?? $newStatus?->name,
                    'from_ar' => $oldStatus?->name_ar,
                    'to_ar' => $newStatus?->name_ar,
                ],
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Status updated successfully.')]);

        return redirect()->route('leads.show', $lead);
    }

    public function assign(Request $request, Lead $lead): RedirectResponse
    {
        $this->authorize('assign', $lead);

        $tenant = Tenant::current();

        $validated = $request->validate([
            'user_id' => [
                'required',
                'integer',
                Rule::exists('account_memberships', 'user_id')
                    ->where('account_tenant_id', $tenant?->id),
            ],
        ]);

        $previousAssignee = $lead->assignedTo;

        $lead->update(['assigned_to_user_id' => $validated['user_id']]);
        $lead->load('assignedTo');

        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => $request->user()?->id,
            'type' => LeadActivity::TYPE_ASSIGNED,
            'data' => [
                'to' => $lead->assignedTo?->name,
                'from' => $previousAssignee?->name,
            ],
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Lead assigned successfully.')]);

        return redirect()->route('leads.show', $lead);
    }

    public function unassign(Request $request, Lead $lead): RedirectResponse
    {
        $this->authorize('assign', $lead);

        $previousAssignee = $lead->assignedTo;

        $lead->update(['assigned_to_user_id' => null]);

        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => $request->user()?->id,
            'type' => LeadActivity::TYPE_UNASSIGNED,
            'data' => [
                'from' => $previousAssignee?->name,
            ],
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Lead unassigned successfully.')]);

        return redirect()->route('leads.show', $lead);
    }

    public function addNote(StoreLeadNoteRequest $request, Lead $lead): RedirectResponse
    {
        $this->authorize('addNote', $lead);

        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => $request->user()?->id,
            'type' => LeadActivity::TYPE_NOTE,
            'data' => ['note' => $request->validated()['note']],
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Note added successfully.')]);

        return redirect()->route('leads.show', $lead);
    }

    /**
     * Check if the lead's email or phone matches an existing Owner or Resident.
     * Returns the matching contact info for the dedup warning dialog.
     */
    public function checkDuplicate(Lead $lead, ConvertLeadToContact $action): JsonResponse
    {
        $this->authorize('convert', $lead);

        $match = $action->findDuplicate($lead);

        if ($match === null) {
            return response()->json(['duplicate' => false]);
        }

        $isOwner = $match instanceof Owner;

        return response()->json([
            'duplicate' => true,
            'match' => [
                'id' => $match->id,
                'type' => $isOwner ? 'owner' : 'resident',
                'name' => trim(($match->first_name ?? '').' '.($match->last_name ?? '')),
                'email' => $match->email,
                'phone_number' => $match->phone_number,
            ],
        ]);
    }

    public function convert(Request $request, Lead $lead, ConvertLeadToContact $action): JsonResponse|RedirectResponse
    {
        $this->authorize('convert', $lead);

        if ($lead->isConverted()) {
            return response()->json([
                'message' => 'Lead is already converted.',
            ], 422);
        }

        $validated = $request->validate([
            'contact_type' => ['required', 'in:owner,resident'],
            'link_to_existing' => ['boolean'],
            'existing_contact_id' => ['nullable', 'integer'],
        ]);

        $linkToExisting = (bool) ($validated['link_to_existing'] ?? false);
        $existingContactId = $validated['existing_contact_id'] ?? null;

        // If linking, validate the contact exists in current tenant
        if ($linkToExisting && $existingContactId !== null) {
            $contactType = $validated['contact_type'];
            $tenantId = Tenant::current()?->id;

            if ($contactType === ConvertLeadToContact::CONTACT_TYPE_OWNER) {
                $exists = Owner::withoutGlobalScopes()
                    ->where('id', $existingContactId)
                    ->where('account_tenant_id', $tenantId)
                    ->exists();
            } else {
                $exists = Resident::withoutGlobalScopes()
                    ->where('id', $existingContactId)
                    ->where('account_tenant_id', $tenantId)
                    ->exists();
            }

            if (! $exists) {
                return response()->json([
                    'message' => 'Contact not found in current tenant.',
                    'errors' => ['existing_contact_id' => ['Contact not found.']],
                ], 422);
            }
        }

        $contact = $action->execute(
            lead: $lead,
            contactType: $validated['contact_type'],
            linkToExisting: $linkToExisting,
            existingContactId: $existingContactId,
            actorUserId: $request->user()?->id,
        );

        if ($request->expectsJson()) {
            $contactType = $validated['contact_type'];
            $contactUrl = $contactType === ConvertLeadToContact::CONTACT_TYPE_OWNER
                ? route('owners.show', $contact->id)
                : route('residents.show', $contact->id);

            return response()->json([
                'message' => 'Lead converted successfully.',
                'contact_url' => $contactUrl,
                'contact_id' => $contact->id,
                'contact_type' => $contactType,
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Lead converted successfully.')]);

        return redirect()->route('leads.show', $lead);
    }

    public function destroy(Request $request, Lead $lead): RedirectResponse
    {
        $this->authorize('delete', $lead);

        $lead->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Lead deleted successfully.')]);

        return redirect()->route('leads.index');
    }
}
