<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RolesEnum;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Support\DefaultSubscriptionPlan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravelcm\Subscriptions\Models\Subscription;

class AccountSubscriptionController extends Controller
{
    public function index(Request $request): Response
    {
        $plan = DefaultSubscriptionPlan::ensure();
        $tenants = $this->manageableTenantQuery($request->user())
            ->with([
                'planSubscriptions' => fn ($query) => $query
                    ->where('slug', 'main')
                    ->latest('id')
                    ->with('plan'),
            ])
            ->orderBy('id')
            ->get()
            ->map(function (Tenant $tenant): array {
                /** @var Subscription|null $subscription */
                $subscription = $tenant->planSubscriptions->first();

                return [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'domain' => $tenant->domain,
                    'subscription' => $subscription
                        ? [
                            'id' => $subscription->id,
                            'active' => $subscription->active(),
                            'canceled' => $subscription->canceled(),
                            'ended' => $subscription->ended(),
                            'on_trial' => $subscription->onTrial(),
                            'starts_at' => $subscription->starts_at?->toDateTimeString(),
                            'trial_ends_at' => $subscription->trial_ends_at?->toDateTimeString(),
                            'ends_at' => $subscription->ends_at?->toDateTimeString(),
                            'canceled_at' => $subscription->canceled_at?->toDateTimeString(),
                        ]
                        : null,
                ];
            })
            ->values();

        return Inertia::render('admin/subscriptions/Index', [
            'plan' => [
                'slug' => $plan->slug,
                'name' => $plan->name,
                'price' => $plan->price,
                'currency' => $plan->currency,
                'invoice_period' => $plan->invoice_period,
                'invoice_interval' => $plan->invoice_interval,
                'trial_period' => $plan->trial_period,
                'trial_interval' => $plan->trial_interval,
            ],
            'accounts' => $tenants,
        ]);
    }

    public function activate(Request $request, Tenant $tenant): RedirectResponse
    {
        $this->ensureCanManageTenant($request->user(), $tenant);

        $plan = DefaultSubscriptionPlan::ensure();
        $subscription = $this->latestMainSubscription($tenant);

        if ($subscription !== null && $subscription->active()) {
            Inertia::flash('toast', [
                'type' => 'success',
                'message' => __('Subscription is already active.'),
            ]);

            return back();
        }

        $tenant->newPlanSubscription('main', $plan);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Subscription activated.'),
        ]);

        return back();
    }

    public function cancel(Request $request, Tenant $tenant): RedirectResponse
    {
        $this->ensureCanManageTenant($request->user(), $tenant);

        $subscription = $this->latestMainSubscription($tenant);

        if ($subscription === null) {
            Inertia::flash('toast', [
                'type' => 'warning',
                'message' => __('No active subscription was found for this account.'),
            ]);

            return back();
        }

        $subscription->cancel();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Subscription canceled at period end.'),
        ]);

        return back();
    }

    public function cancelNow(Request $request, Tenant $tenant): RedirectResponse
    {
        $this->ensureCanManageTenant($request->user(), $tenant);

        $subscription = $this->latestMainSubscription($tenant);

        if ($subscription === null) {
            Inertia::flash('toast', [
                'type' => 'warning',
                'message' => __('No active subscription was found for this account.'),
            ]);

            return back();
        }

        $subscription->cancel(true);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Subscription canceled immediately.'),
        ]);

        return back();
    }

    private function manageableTenantQuery(User $user): Builder
    {
        if ($user->hasRole(RolesEnum::ACCOUNT_ADMINS->value)) {
            return Tenant::query();
        }

        $currentTenantId = Tenant::current()?->id;

        return Tenant::query()->whereKey($currentTenantId);
    }

    private function latestMainSubscription(Tenant $tenant): ?Subscription
    {
        /** @var Subscription|null $subscription */
        $subscription = $tenant->planSubscriptions()
            ->where('slug', 'main')
            ->latest('id')
            ->first();

        return $subscription;
    }

    private function ensureCanManageTenant(User $user, Tenant $tenant): void
    {
        if ($user->hasRole(RolesEnum::ACCOUNT_ADMINS->value)) {
            return;
        }

        abort_unless((int) Tenant::current()?->id === (int) $tenant->id, 403);
    }

    public function billingHistory(): Response
    {
        $tenant = Tenant::current();
        abort_unless($tenant !== null, 404);

        $subscriptions = $tenant->planSubscriptions()
            ->where('slug', 'main')
            ->with('plan')
            ->latest('id')
            ->get()
            ->map(fn ($sub): array => [
                'id' => $sub->id,
                'plan_name' => $sub->plan?->name,
                'starts_at' => $sub->starts_at?->toJSON(),
                'ends_at' => $sub->ends_at?->toJSON(),
                'canceled_at' => $sub->canceled_at?->toJSON(),
                'active' => $sub->active(),
            ]);

        return Inertia::render('admin/subscriptions/Billing', [
            'subscriptions' => $subscriptions,
        ]);
    }
}

    public function billingHistory(): Response
    {
        $tenant = Tenant::current();
        abort_unless($tenant !== null, 404);

        $subscriptions = $tenant->planSubscriptions()
            ->where('slug', 'main')
            ->with('plan')
            ->latest('id')
            ->get()
            ->map(fn ($sub): array => [
                'id' => $sub->id,
                'plan_name' => $sub->plan?->name,
                'starts_at' => $sub->starts_at?->toJSON(),
                'ends_at' => $sub->ends_at?->toJSON(),
                'canceled_at' => $sub->canceled_at?->toJSON(),
                'active' => $sub->active(),
            ]);

        return Inertia::render('admin/subscriptions/Billing', [
            'subscriptions' => $subscriptions,
        ]);
    }
