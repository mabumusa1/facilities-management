<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RolesEnum;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\FeatureFlagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TenantFeatureFlagController extends Controller
{
    public function show(Request $request, Tenant $tenant): Response|RedirectResponse
    {
        if (! $request->user()->hasRole(RolesEnum::ACCOUNT_ADMINS->value)) {
            return redirect()->route('admin.subscriptions.index');
        }

        return Inertia::render('admin/subscriptions/Show', [
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'domain' => $tenant->domain,
            ],
            'features' => Inertia::defer(function () use ($tenant): array {
                return app(FeatureFlagService::class)->effectiveFlags($tenant);
            }),
        ]);
    }

    public function index(Request $request, Tenant $tenant): JsonResponse
    {
        if (! $request->user()->hasRole(RolesEnum::ACCOUNT_ADMINS->value)) {
            abort(403);
        }

        return response()->json(
            app(FeatureFlagService::class)->effectiveFlags($tenant),
        );
    }

    public function toggle(Request $request, Tenant $tenant, string $flagKey): JsonResponse
    {
        if (! $request->user()->hasRole(RolesEnum::ACCOUNT_ADMINS->value)) {
            abort(403);
        }

        $validated = $request->validate([
            'enabled' => ['required', 'boolean'],
        ]);

        $result = app(FeatureFlagService::class)->toggle(
            $tenant,
            $request->user(),
            $flagKey,
            $validated['enabled'],
        );

        return response()->json($result);
    }
}
