<?php

namespace App\Http\Controllers\AppSettings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GeneralSettingController extends Controller
{
    public function index(): Response
    {
        $settings = Setting::orderBy('type')->orderBy('name')->get()->groupBy('type');

        return Inertia::render('app-settings/general/Index', [
            'settingGroups' => $settings,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:rental_contract_type,payment_schedule,transaction_category,transaction_type'],
            'parent_id' => ['nullable', 'integer', 'exists:rf_settings,id'],
        ]);

        Setting::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Setting created.')]);

        return to_route('app-settings.general.index');
    }

    public function update(Request $request, Setting $setting): RedirectResponse
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
        ]);

        $setting->update($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Setting updated.')]);

        return to_route('app-settings.general.index');
    }

    public function destroy(Setting $setting): RedirectResponse
    {
        $setting->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Setting deleted.')]);

        return to_route('app-settings.general.index');
    }
}
