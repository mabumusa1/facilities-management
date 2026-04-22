<?php

namespace App\Http\Controllers\AppSettings;

use App\Http\Controllers\Controller;
use App\Models\ServiceSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;

class ServiceSettingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->integer('per_page', 10), 1), 50);

        $serviceSettings = ServiceSetting::query()
            ->with('category:id,name,name_ar,name_en')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'data' => collect($serviceSettings->items())->map(
                fn (ServiceSetting $serviceSetting): array => $this->serviceSettingPayload($serviceSetting)
            ),
            'meta' => $this->meta($serviceSettings),
        ]);
    }

    public function show(ServiceSetting $serviceSetting): JsonResponse
    {
        $serviceSetting->load('category:id,name,name_ar,name_en');

        return response()->json([
            'data' => $this->serviceSettingPayload($serviceSetting),
            'message' => __('Service settings retrieved.'),
        ]);
    }

    public function updateOrCreate(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'rf_category_id' => ['nullable', 'integer', 'exists:rf_request_categories,id', 'required_without:category_id'],
            'category_id' => ['nullable', 'integer', 'exists:rf_request_categories,id', 'required_without:rf_category_id'],
            'permissions' => ['required', 'array'],
            'permissions.manager_close_Request' => ['nullable', 'boolean'],
            'permissions.not_require_professional_enter_request_code' => ['nullable', 'boolean'],
            'permissions.not_require_professional_upload_request_photo' => ['nullable', 'boolean'],
            'permissions.attachments_required' => ['nullable', 'boolean'],
            'permissions.allow_professional_reschedule' => ['nullable', 'boolean'],
            'visibilities' => ['nullable', 'array'],
            'visibilities.hide_resident_number' => ['nullable', 'boolean'],
            'visibilities.hide_resident_name' => ['nullable', 'boolean'],
            'visibilities.hide_professional_number_and_name' => ['nullable', 'boolean'],
            'visibilities.show_unified_number_only' => ['nullable', 'boolean'],
            'submit_request_before_type' => ['nullable', 'string', 'max:50'],
            'submit_request_before_value' => ['nullable', 'integer', 'min:1'],
            'capacity_type' => ['nullable', 'string', 'max:50'],
            'capacity_value' => ['nullable', 'integer', 'min:1'],
        ]);

        $categoryId = (int) ($validated['rf_category_id'] ?? $validated['category_id']);

        $serviceSetting = ServiceSetting::updateOrCreate(
            ['category_id' => $categoryId],
            [
                'permissions' => $validated['permissions'],
                'visibilities' => $validated['visibilities'] ?? null,
                'submit_request_before_type' => $validated['submit_request_before_type'] ?? null,
                'submit_request_before_value' => $validated['submit_request_before_value'] ?? null,
                'capacity_type' => $validated['capacity_type'] ?? null,
                'capacity_value' => $validated['capacity_value'] ?? null,
            ],
        );

        $serviceSetting->load('category:id,name,name_ar,name_en');

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            return response()->json([
                'data' => $this->serviceSettingPayload($serviceSetting),
                'message' => __('Service settings updated.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Service settings updated.')]);

        return to_route('app-settings.request-categories.edit', $categoryId);
    }

    public function destroy(Request $request, ServiceSetting $serviceSetting): JsonResponse|RedirectResponse
    {
        $serviceSettingId = $serviceSetting->id;
        $categoryId = $serviceSetting->category_id;

        $serviceSetting->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $serviceSettingId,
                ],
                'message' => __('Service settings deleted.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Service settings deleted.')]);

        return to_route('app-settings.request-categories.edit', $categoryId);
    }

    /**
     * @return array<string, mixed>
     */
    private function serviceSettingPayload(ServiceSetting $serviceSetting): array
    {
        return [
            'id' => $serviceSetting->id,
            'category_id' => $serviceSetting->category_id,
            'category' => $serviceSetting->category
                ? [
                    'id' => $serviceSetting->category->id,
                    'name' => $serviceSetting->category->name,
                    'name_ar' => $serviceSetting->category->name_ar,
                    'name_en' => $serviceSetting->category->name_en,
                ]
                : null,
            'visibilities' => $serviceSetting->visibilities ?? [],
            'permissions' => $serviceSetting->permissions ?? [],
            'submit_request_before_type' => $serviceSetting->submit_request_before_type,
            'submit_request_before_value' => $serviceSetting->submit_request_before_value,
            'capacity_type' => $serviceSetting->capacity_type,
            'capacity_value' => $serviceSetting->capacity_value,
            'created_at' => $serviceSetting->created_at?->toJSON(),
            'updated_at' => $serviceSetting->updated_at?->toJSON(),
        ];
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
