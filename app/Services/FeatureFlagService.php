<?php

namespace App\Services;

use App\Enums\FeatureFlag;
use App\Models\FeatureFlagAuditLog;
use App\Models\FeatureFlagOverride;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class FeatureFlagService
{
    public function effectiveFlags(Tenant $tenant): array
    {
        $overrides = FeatureFlagOverride::query()
            ->where('account_tenant_id', $tenant->id)
            ->get()
            ->keyBy('flag_key');

        $planName = $this->resolvePlanName($tenant);

        return collect(FeatureFlag::cases())
            ->map(function (FeatureFlag $flag) use ($overrides, $planName): array {
                $includedInTiers = $flag->includedInTiers();
                $included = in_array($planName, $includedInTiers, true);
                $override = $overrides->get($flag->value);

                return [
                    'key' => $flag->value,
                    'label_en' => $flag->labelEn(),
                    'label_ar' => $flag->labelAr(),
                    'enabled' => $override ? $override->enabled : $included,
                    'in_tier' => $included,
                    'plan_name' => $planName,
                ];
            })
            ->values()
            ->all();
    }

    public function toggle(Tenant $tenant, User $actor, string $flagKey, bool $enabled): array
    {
        $flag = FeatureFlag::tryFrom($flagKey);
        if ($flag === null) {
            abort(422, 'Invalid feature flag key.');
        }

        FeatureFlagOverride::updateOrCreate(
            [
                'account_tenant_id' => $tenant->id,
                'flag_key' => $flagKey,
            ],
            ['enabled' => $enabled],
        );

        FeatureFlagAuditLog::create([
            'account_tenant_id' => $tenant->id,
            'user_id' => $actor->id,
            'flag_key' => $flagKey,
            'action' => $enabled ? 'enabled' : 'disabled',
            'created_at' => now(),
        ]);

        Cache::forget("feature-flags:tenant:{$tenant->id}");

        return [
            'key' => $flag->value,
            'label_en' => $flag->labelEn(),
            'label_ar' => $flag->labelAr(),
            'enabled' => $enabled,
            'in_tier' => in_array($this->resolvePlanName($tenant), $flag->includedInTiers(), true),
            'plan_name' => $this->resolvePlanName($tenant),
        ];
    }

    private function resolvePlanName(Tenant $tenant): string
    {
        $subscription = $tenant->planSubscriptions()
            ->where('slug', 'main')
            ->latest('id')
            ->first();

        if ($subscription && $subscription->plan) {
            return $subscription->plan->name ?? 'Starter';
        }

        return 'Starter';
    }
}
