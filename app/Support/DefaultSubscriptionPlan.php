<?php

namespace App\Support;

use Laravelcm\Subscriptions\Interval;
use Laravelcm\Subscriptions\Models\Plan;

final class DefaultSubscriptionPlan
{
    public const SLUG = 'basic-monthly-sar';

    public static function ensure(): Plan
    {
        /** @var Plan $plan */
        $plan = Plan::query()->firstOrCreate(
            ['slug' => self::SLUG],
            [
                'name' => [
                    'en' => 'Basic Monthly',
                    'ar' => 'الخطة الشهرية الأساسية',
                ],
                'description' => [
                    'en' => 'Single plan at 300 SAR per month with a 14-day trial period.',
                    'ar' => 'خطة واحدة بسعر 300 ريال سعودي شهرياً مع فترة تجريبية 14 يوماً.',
                ],
                'is_active' => true,
                'price' => 300.00,
                'signup_fee' => 0.00,
                'currency' => 'SAR',
                'trial_period' => 14,
                'trial_interval' => Interval::DAY->value,
                'invoice_period' => 1,
                'invoice_interval' => Interval::MONTH->value,
                'grace_period' => 0,
                'grace_interval' => Interval::DAY->value,
                'sort_order' => 1,
            ],
        );

        return $plan;
    }
}
