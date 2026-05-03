<?php

namespace App\Http\Controllers\Leasing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Leasing\UpdateLeaseAlertSettingsRequest;
use App\Models\AppSetting;
use App\Models\Lease;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Manage per-tenant lease expiry alert threshold configuration.
 *
 * Routes:
 *   GET  leasing/settings/alerts → leasing.settings.alerts
 *   POST leasing/settings/alerts → leasing.settings.alerts.update
 */
class LeaseAlertSettingsController extends Controller
{
    /**
     * Default threshold configuration applied when none has been saved.
     *
     * @var array<int, array{days: int, in_app: bool, email: bool}>
     */
    private const DEFAULT_THRESHOLDS = [
        ['days' => 90, 'in_app' => true, 'email' => true],
        ['days' => 60, 'in_app' => true, 'email' => true],
        ['days' => 30, 'in_app' => true, 'email' => true],
    ];

    public function show(): Response
    {
        $this->authorize('viewAny', Lease::class);

        $setting = AppSetting::first();
        $thresholds = $setting?->lease_alert_thresholds ?? self::DEFAULT_THRESHOLDS;

        return Inertia::render('leasing/settings/Alerts', [
            'thresholds' => $thresholds,
            'defaultThresholds' => self::DEFAULT_THRESHOLDS,
        ]);
    }

    public function update(UpdateLeaseAlertSettingsRequest $request): RedirectResponse
    {
        $this->authorize('viewAny', Lease::class);

        $validated = $request->validated();

        AppSetting::updateOrCreate(
            ['id' => AppSetting::first()?->id ?? 0],
            ['lease_alert_thresholds' => $validated['thresholds']],
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Alert settings saved.')]);

        return to_route('leasing.settings.alerts');
    }
}
