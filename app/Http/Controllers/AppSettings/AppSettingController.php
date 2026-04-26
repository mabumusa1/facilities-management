<?php

namespace App\Http\Controllers\AppSettings;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AppSettingController extends Controller
{
    public function edit(): Response
    {
        $setting = AppSetting::first();

        return Inertia::render('app-settings/appearance/Edit', [
            'appSetting' => $setting
                ? [
                    'id' => $setting->id,
                    'sidebar_label_overrides' => $setting->sidebar_label_overrides,
                    'favicon_path' => $setting->favicon_path,
                    'login_bg_path' => $setting->login_bg_path,
                ]
                : null,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sidebar_label_overrides' => ['nullable', 'array'],
            'sidebar_label_overrides.residents' => ['nullable', 'string', 'max:50'],
            'sidebar_label_overrides.tenants' => ['nullable', 'string', 'max:50'],
            'sidebar_label_overrides.facilities' => ['nullable', 'string', 'max:50'],
            'sidebar_label_overrides.amenities' => ['nullable', 'string', 'max:50'],
            'favicon_path' => ['nullable', 'string', 'max:500'],
            'login_bg_path' => ['nullable', 'string', 'max:500'],
            'primary_color' => ['nullable', 'string', 'max:7'],
        ]);

        AppSetting::updateOrCreate(
            ['id' => AppSetting::first()?->id ?? 0],
            $validated,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('App appearance updated.')]);

        return to_route('app-settings.appearance.edit');
    }
}
