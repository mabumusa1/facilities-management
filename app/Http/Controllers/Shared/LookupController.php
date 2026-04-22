<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\CommonList;
use App\Models\Country;
use App\Models\InvoiceSetting;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Owner;
use App\Models\Professional;
use App\Models\Resident;
use App\Models\Status;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class LookupController extends Controller
{
    public function modules(Request $request): JsonResponse
    {
        $items = collect([
            ['id' => 1, 'title' => 'Dashboard', 'is_active' => '1'],
            ['id' => 2, 'title' => 'Marketplace', 'is_active' => '1'],
            ['id' => 3, 'title' => 'Visitor Access', 'is_active' => '1'],
            ['id' => 4, 'title' => 'Requests', 'is_active' => '1'],
            ['id' => 5, 'title' => 'Transactions', 'is_active' => '1'],
            ['id' => 6, 'title' => 'Settings', 'is_active' => '1'],
            ['id' => 7, 'title' => 'Reports', 'is_active' => '1'],
        ]);

        $paginator = $this->paginateCollection($items, $request);

        return response()->json([
            'data' => $paginator->items(),
            'meta' => $this->meta($paginator),
        ]);
    }

    public function statuses(Request $request): JsonResponse
    {
        $statuses = Status::query()
            ->select('id', 'name', 'name_ar', 'name_en', 'priority', 'type', 'created_at')
            ->orderBy('priority')
            ->orderBy('id')
            ->paginate($this->perPage($request));

        return response()->json([
            'data' => collect($statuses->items())->map(fn (Status $status): array => [
                'id' => $status->id,
                'name' => $status->name,
                'name_ar' => $status->name_ar,
                'name_en' => $status->name_en,
                'type' => $status->type,
                'priority' => $status->priority,
                'created_at' => $status->created_at?->toISOString(),
            ]),
            'meta' => $this->meta($statuses),
        ]);
    }

    public function commonLists(Request $request): JsonResponse
    {
        $lists = CommonList::query()
            ->select('id', 'name', 'name_ar', 'name_en', 'type', 'priority', 'created_at')
            ->orderBy('type')
            ->orderBy('priority')
            ->paginate($this->perPage($request));

        return response()->json([
            'data' => collect($lists->items())->map(fn (CommonList $item): array => [
                'id' => $item->id,
                'name' => $item->name,
                'name_ar' => $item->name_ar,
                'name_en' => $item->name_en,
                'type' => $item->type,
                'priority' => $item->priority,
                'created_at' => $item->created_at?->toISOString(),
            ]),
            'meta' => $this->meta($lists),
        ]);
    }

    public function leads(Request $request): JsonResponse
    {
        $leads = Lead::query()
            ->with(['status:id,name,name_ar,name_en', 'source:id,name,name_ar,name_en', 'leadOwner:id,first_name,last_name'])
            ->latest()
            ->paginate($this->perPage($request));

        return response()->json([
            'data' => collect($leads->items())->map(fn (Lead $lead): array => [
                'id' => $lead->id,
                'name' => $lead->name ?: trim(($lead->first_name ?? '').' '.($lead->last_name ?? '')),
                'phone_number' => $lead->phone_number,
                'email' => $lead->email,
                'created_at' => $lead->created_at?->toISOString(),
                'updated_at' => $lead->updated_at?->toISOString(),
                'lead_last_contact_at' => $lead->lead_last_contact_at?->toISOString(),
                'interested' => $lead->interested,
                'role' => 'Lead',
                'status' => [
                    'id' => $lead->status?->id,
                    'value' => $lead->status?->name_en ?? $lead->status?->name,
                ],
                'source' => [
                    'id' => $lead->source?->id,
                    'value' => $lead->source?->name_en ?? $lead->source?->name,
                ],
                'priority' => [
                    'id' => $lead->priority_id,
                    'value' => $lead->priority_id ? (string) $lead->priority_id : null,
                ],
                'lead_owner' => $lead->leadOwner
                    ? trim($lead->leadOwner->first_name.' '.$lead->leadOwner->last_name)
                    : null,
            ]),
            'meta' => $this->meta($leads),
        ]);
    }

    public function countries(Request $request): JsonResponse
    {
        $countries = Country::query()
            ->select('id', 'name', 'name_ar', 'name_en', 'iso2', 'dial', 'currency')
            ->orderByRaw('COALESCE(name_en, name) asc')
            ->paginate($this->perPage($request));

        return response()->json([
            'data' => collect($countries->items())->map(fn (Country $country): array => [
                'id' => $country->id,
                'name' => $country->name,
                'name_ar' => $country->name_ar,
                'name_en' => $country->name_en,
                'iso2' => $country->iso2,
                'dial' => $country->dial,
                'currency' => $country->currency,
            ]),
            'meta' => $this->meta($countries),
        ]);
    }

    public function companyProfile(): JsonResponse
    {
        $invoiceSetting = InvoiceSetting::query()
            ->select([
                'id',
                'company_name',
                'logo',
                'address',
                'vat',
                'instructions',
                'notes',
                'vat_number',
                'cr_number',
            ])
            ->first();

        /** @var array<string, mixed>|null $bankDetails */
        $bankDetails = SystemSetting::query()
            ->where('key', 'bank-details')
            ->value('payload');

        return response()->json([
            'data' => [
                'id' => $invoiceSetting?->id,
                'company_name' => $invoiceSetting?->company_name,
                'logo' => $invoiceSetting?->logo,
                'address' => $invoiceSetting?->address,
                'vat' => $invoiceSetting?->vat,
                'vat_number' => $invoiceSetting?->vat_number,
                'cr_number' => $invoiceSetting?->cr_number,
                'instructions' => $invoiceSetting?->instructions,
                'notes' => $invoiceSetting?->notes,
                'beneficiary_name' => $bankDetails['beneficiary_name'] ?? null,
                'bank_name' => $bankDetails['bank_name'] ?? null,
                'account_number' => $bankDetails['account_number'] ?? null,
                'iban' => $bankDetails['iban'] ?? null,
            ],
        ]);
    }

    public function contactsStatistics(): JsonResponse
    {
        $tenants = Resident::query()->count();
        $owners = Owner::query()->count();
        $professionals = Professional::query()->count();
        $admins = Admin::query()->count();
        $leads = Lead::query()->count();

        return response()->json([
            'data' => [
                'tenants' => $tenants,
                'owners' => $owners,
                'professionals' => $professionals,
                'admins' => $admins,
                'leads' => $leads,
                'total_contacts' => $tenants + $owners + $professionals + $admins + $leads,
            ],
        ]);
    }

    public function leadCreate(): JsonResponse
    {
        $statuses = Status::query()
            ->whereIn('type', ['lead', 'lead_status'])
            ->select('id', 'name', 'name_ar', 'name_en')
            ->orderBy('priority')
            ->orderBy('id')
            ->get();

        $statusData = $statuses->map(fn (Status $status): array => [
            'id' => $status->id,
            'name' => $status->name,
            'name_ar' => $status->name_ar,
            'name_en' => $status->name_en,
        ])->values();

        if ($statusData->isEmpty()) {
            $statusData = collect([
                ['id' => 1, 'name' => 'New', 'name_ar' => 'جديد', 'name_en' => 'New'],
                ['id' => 2, 'name' => 'Contacted', 'name_ar' => 'تم التواصل', 'name_en' => 'Contacted'],
                ['id' => 3, 'name' => 'No Response', 'name_ar' => 'لا يوجد رد', 'name_en' => 'No Response'],
                ['id' => 4, 'name' => 'Follow-up', 'name_ar' => 'متابعة', 'name_en' => 'Follow-up'],
                ['id' => 5, 'name' => 'Interested', 'name_ar' => 'مهتم', 'name_en' => 'Interested'],
                ['id' => 6, 'name' => 'Not Interested', 'name_ar' => 'غير مهتم', 'name_en' => 'Not Interested'],
                ['id' => 7, 'name' => 'Future Interest', 'name_ar' => 'اهتمام مستقبلي', 'name_en' => 'Future Interest'],
            ]);
        }

        $sources = LeadSource::query()
            ->select('id', 'name', 'name_ar', 'name_en')
            ->orderBy('id')
            ->get()
            ->map(fn (LeadSource $source): array => [
                'id' => $source->id,
                'name' => $source->name,
                'name_ar' => $source->name_ar,
                'name_en' => $source->name_en,
            ])
            ->values();

        return response()->json([
            'data' => [
                'statuses' => $statusData,
                'sources' => $sources,
                'priorities' => [
                    ['id' => 1, 'name_ar' => 'عالي', 'name_en' => 'High'],
                    ['id' => 2, 'name_ar' => 'متوسط', 'name_en' => 'Medium'],
                    ['id' => 3, 'name_ar' => 'منخفض', 'name_en' => 'Low'],
                ],
            ],
        ]);
    }

    public function storeLead(Request $request): JsonResponse
    {
        $validated = $request->validate([
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
            'lead_last_contact_at' => ['nullable', 'date'],
        ]);

        if (! array_key_exists('status_id', $validated) || $validated['status_id'] === null) {
            $validated['status_id'] = Status::query()
                ->whereIn('type', ['lead', 'lead_status'])
                ->orderBy('priority')
                ->orderBy('id')
                ->value('id');
        }

        $lead = Lead::create($validated);

        $lead->load([
            'status:id,name,name_ar,name_en',
            'source:id,name,name_ar,name_en',
            'leadOwner:id,first_name,last_name',
        ]);

        return response()->json([
            'data' => [
                'id' => $lead->id,
                'name' => $lead->name ?: trim(($lead->first_name ?? '').' '.($lead->last_name ?? '')),
                'phone_number' => $lead->phone_number,
                'email' => $lead->email,
                'interested' => $lead->interested,
                'status' => [
                    'id' => $lead->status?->id,
                    'value' => $lead->status?->name_en ?? $lead->status?->name,
                ],
                'source' => [
                    'id' => $lead->source?->id,
                    'value' => $lead->source?->name_en ?? $lead->source?->name,
                ],
                'lead_owner' => $lead->leadOwner
                    ? trim($lead->leadOwner->first_name.' '.$lead->leadOwner->last_name)
                    : null,
            ],
            'message' => __('Lead created.'),
        ]);
    }

    private function perPage(Request $request): int
    {
        return min(max((int) $request->integer('per_page', 10), 1), 50);
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $items
     */
    private function paginateCollection(Collection $items, Request $request): LengthAwarePaginator
    {
        $page = max((int) $request->integer('page', 1), 1);
        $perPage = $this->perPage($request);
        $total = $items->count();

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $total,
            $perPage,
            $page,
            ['path' => $request->url()],
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function meta(LengthAwarePaginator $paginator): array
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
