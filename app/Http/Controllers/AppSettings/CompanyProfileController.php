<?php

namespace App\Http\Controllers\AppSettings;

use App\Http\Controllers\Controller;
use App\Models\InvoiceSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CompanyProfileController extends Controller
{
    public function edit(): Response
    {
        Gate::authorize('companyProfile.VIEW');

        return Inertia::render('app-settings/company-profile/Edit', [
            'companyProfile' => Inertia::defer(function () {
                $settings = InvoiceSetting::query()
                    ->select([
                        'id', 'name_en', 'name_ar', 'logo_path', 'logo_ar_path',
                        'vat_number', 'cr_number', 'timezone', 'primary_color',
                    ])
                    ->first();

                return [
                    'name_en' => $settings?->name_en,
                    'name_ar' => $settings?->name_ar,
                    'vat_number' => $settings?->vat_number,
                    'cr_number' => $settings?->cr_number,
                    'logo_url' => $settings?->logo_url,
                    'logo_ar_url' => $settings?->logo_ar_url,
                    'timezone' => $settings?->timezone ?? 'UTC',
                    'primary_color' => $settings?->primary_color,
                ];
            }),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        Gate::authorize('companyProfile.UPDATE');

        $validated = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'vat_number' => ['nullable', 'string', 'regex:/^[0-9]{15}$/'],
            'cr_number' => ['nullable', 'string', 'max:50'],
            'timezone' => ['required', 'string', 'max:50', 'timezone'],
            'primary_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'logo' => ['nullable', 'image', 'mimes:png,svg', 'mimetypes:image/png,image/svg+xml', 'max:2048'],
            'logo_ar' => ['nullable', 'image', 'mimes:png,svg', 'mimetypes:image/png,image/svg+xml', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_logo_ar' => ['nullable', 'boolean'],
        ]);

        $settings = InvoiceSetting::query()->firstOrNew();
        $settings->fill($request->except(['logo', 'logo_ar', 'remove_logo', 'remove_logo_ar']));

        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }

            $settings->logo_path = $request->file('logo')->store(
                'uploads/logos/'.$settings->accountTenant?->id,
                'public',
            );
        }

        if ($request->hasFile('logo_ar')) {
            if ($settings->logo_ar_path) {
                Storage::disk('public')->delete($settings->logo_ar_path);
            }

            $settings->logo_ar_path = $request->file('logo_ar')->store(
                'uploads/logos/'.$settings->accountTenant?->id,
                'public',
            );
        }

        if ($request->boolean('remove_logo') && $settings->logo_path) {
            Storage::disk('public')->delete($settings->logo_path);
            $settings->logo_path = null;
        }

        if ($request->boolean('remove_logo_ar') && $settings->logo_ar_path) {
            Storage::disk('public')->delete($settings->logo_ar_path);
            $settings->logo_ar_path = null;
        }

        $settings->save();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Company profile saved successfully.'),
        ]);

        return to_route('app-settings.company-profile.edit');
    }
}
