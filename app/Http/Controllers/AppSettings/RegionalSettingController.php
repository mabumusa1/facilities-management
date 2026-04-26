<?php

namespace App\Http\Controllers\AppSettings;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RegionalSettingController extends Controller
{
    public function edit(): Response
    {
        $settings = SystemSetting::all()->pluck('payload', 'key');

        return Inertia::render('app-settings/regional/Edit', [
            'settings' => $settings,
            'locales' => [
                ['value' => 'en', 'label' => 'English'],
                ['value' => 'ar', 'label' => 'العربية'],
            ],
        ]);
    }

    public function update(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'default_currency_id' => ['nullable', 'integer'],
            'default_locale' => ['nullable', 'string', 'in:en,ar'],
            'date_format' => ['nullable', 'string', 'in:Y-m-d,d/m/Y,m/d/Y'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'working_days' => ['nullable', 'array'],
            'working_days.*' => ['string', 'in:sat,sun,mon,tue,wed,thu,fri'],
            'working_hours_start' => ['nullable', 'date_format:H:i'],
            'working_hours_end' => ['nullable', 'date_format:H:i'],
        ]);

        foreach ($validated as $key => $value) {
            $encoded = is_array($value) ? json_encode($value) : (string) $value;

            SystemSetting::updateOrCreate(
                ['key' => $key, 'account_tenant_id' => Tenant::current()?->id],
                ['payload' => json_decode($encoded, true)],
            );
        }

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            return response()->json([
                'data' => $validated,
                'message' => __('Regional settings updated.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Regional settings updated.')]);

        return to_route('app-settings.regional.edit');
    }
}
