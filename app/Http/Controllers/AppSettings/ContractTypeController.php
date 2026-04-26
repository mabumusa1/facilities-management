<?php

namespace App\Http\Controllers\AppSettings;

use App\Http\Controllers\Controller;
use App\Models\ContractType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContractTypeController extends Controller
{
    public function index(Request $request): JsonResponse|Response
    {
        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $types = ContractType::query()
                ->orderBy('sort_order')
                ->get()
                ->map(fn (ContractType $type): array => [
                    'id' => $type->id,
                    'name_en' => $type->name_en,
                    'name_ar' => $type->name_ar,
                    'default_payment_terms_days' => $type->default_payment_terms_days,
                    'default_escalation_type' => $type->default_escalation_type,
                    'is_active' => $type->is_active,
                    'sort_order' => $type->sort_order,
                ]);

            return response()->json(['data' => $types]);
        }

        return Inertia::render('app-settings/contract-types/Index', [
            'contractTypes' => ContractType::query()->orderBy('sort_order')->paginate(20),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'default_payment_terms_days' => ['nullable', 'integer', 'min:0'],
            'default_escalation_type' => ['nullable', 'string', 'max:50'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $contractType = ContractType::create($validated);

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            return response()->json([
                'data' => $contractType,
                'message' => __('Contract type created.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Contract type created.')]);

        return to_route('app-settings.contract-types.index');
    }

    public function update(Request $request, ContractType $contractType): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'default_payment_terms_days' => ['nullable', 'integer', 'min:0'],
            'default_escalation_type' => ['nullable', 'string', 'max:50'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $contractType->update($validated);

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            return response()->json([
                'data' => $contractType,
                'message' => __('Contract type updated.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Contract type updated.')]);

        return to_route('app-settings.contract-types.index');
    }

    public function destroy(Request $request, ContractType $contractType): JsonResponse|RedirectResponse
    {
        $contractType->delete();

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            return response()->json([
                'data' => ['id' => $contractType->id],
                'message' => __('Contract type deleted.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Contract type deleted.')]);

        return to_route('app-settings.contract-types.index');
    }
}
