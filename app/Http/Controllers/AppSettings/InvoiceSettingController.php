<?php

namespace App\Http\Controllers\AppSettings;

use App\Http\Controllers\Controller;
use App\Models\InvoiceSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceSettingController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('app-settings/invoice/Edit', [
            'invoiceSetting' => InvoiceSetting::first(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'vat' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'vat_number' => ['nullable', 'string', 'max:50'],
            'cr_number' => ['nullable', 'string', 'max:50'],
            'instructions' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        InvoiceSetting::updateOrCreate(
            ['id' => InvoiceSetting::first()?->id ?? 0],
            $validated,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Invoice settings updated.')]);

        return to_route('app-settings.invoice.edit');
    }

    public function showApi(): JsonResponse
    {
        $invoiceSetting = InvoiceSetting::query()
            ->select([
                'id',
                'company_name',
                'logo',
                'address',
                'vat',
                'instructions',
                'notes',
                'vat_number',
                'cr_number',
            ])
            ->first();

        return response()->json([
            'data' => $this->invoiceSettingPayload($invoiceSetting),
        ]);
    }

    public function storeApi(Request $request): JsonResponse
    {
        return $this->upsertApi($request, false);
    }

    public function updateApi(Request $request): JsonResponse
    {
        return $this->upsertApi($request, true);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateApiPayload(Request $request, bool $partial): array
    {
        $required = $partial ? ['sometimes', 'required'] : ['required'];

        return $request->validate([
            'company_name' => [...$required, 'string', 'max:255'],
            'address' => [...$required, 'string', 'max:500'],
            'vat' => [...$required, 'numeric', 'min:0', 'max:100'],
            'logo' => ['nullable', 'string', 'max:2048'],
            'instructions' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'vat_number' => ['nullable', 'string', 'max:50'],
            'cr_number' => ['nullable', 'string', 'max:50'],
        ]);
    }

    private function upsertApi(Request $request, bool $partial): JsonResponse
    {
        $validated = $this->validateApiPayload($request, $partial);

        $invoiceSetting = InvoiceSetting::query()->firstOrNew();
        $invoiceSetting->fill($validated);
        $invoiceSetting->save();
        $invoiceSetting->refresh();

        return response()->json([
            'data' => $this->invoiceSettingPayload($invoiceSetting),
            'message' => __('Invoice settings updated.'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function invoiceSettingPayload(?InvoiceSetting $invoiceSetting): array
    {
        return [
            'id' => $invoiceSetting?->id,
            'company_name' => $invoiceSetting?->company_name,
            'logo' => $invoiceSetting?->logo,
            'address' => $invoiceSetting?->address,
            'vat' => $invoiceSetting?->vat,
            'instructions' => $invoiceSetting?->instructions,
            'notes' => $invoiceSetting?->notes,
            'vat_number' => $invoiceSetting?->vat_number,
            'cr_number' => $invoiceSetting?->cr_number,
        ];
    }
}
