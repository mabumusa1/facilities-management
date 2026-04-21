<?php

namespace App\Http\Controllers\AppSettings;

use App\Http\Controllers\Controller;
use App\Models\ServiceSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceSettingController extends Controller
{
    public function updateOrCreate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rf_category_id' => ['required', 'integer', 'exists:rf_request_categories,id'],
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

        $categoryId = (int) $validated['rf_category_id'];

        ServiceSetting::updateOrCreate(
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

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Service settings updated.')]);

        return to_route('app-settings.request-categories.edit', $categoryId);
    }
}
