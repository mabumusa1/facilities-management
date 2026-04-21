<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\CommonList;
use App\Models\Country;
use App\Models\Lead;
use App\Models\Status;
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
