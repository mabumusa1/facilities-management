<?php

namespace App\Http\Controllers\AppSettings;

use App\Http\Controllers\Controller;
use App\Models\InvoiceSetting;
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
}
