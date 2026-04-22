<?php

namespace App\Http\Controllers\AppSettings;

use App\Http\Controllers\Controller;
use App\Models\InvoiceSetting;
use App\Models\RequestCategory;
use App\Models\ServiceSetting;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class SettingsShellController extends Controller
{
    /**
     * @var array<string, string>
     */
    private const TABS = [
        'invoice' => 'Invoice',
        'service-request' => 'Service Request',
        'visitor-request' => 'Visitor Request',
        'bank-details' => 'Bank Details',
        'visits-details' => 'Visits Details',
        'sales-details' => 'Sales Details',
    ];

    /**
     * @var array<int, array{key: string, label: string, description: string}>
     */
    private const SERVICE_REQUEST_TYPES = [
        [
            'key' => 'home-service',
            'label' => 'Home Service',
            'description' => 'Requests related to home maintenance, cleaning, and unit-level support.',
        ],
        [
            'key' => 'neighbourhood-service',
            'label' => 'Neighbourhood Service',
            'description' => 'Requests related to shared-area and neighbourhood-level services.',
        ],
    ];

    public function invoice(): Response
    {
        return $this->renderTab('invoice', [
            'invoiceSetting' => InvoiceSetting::query()
                ->select([
                    'id',
                    'company_name',
                    'logo',
                    'address',
                    'vat',
                    'vat_number',
                    'cr_number',
                    'instructions',
                    'notes',
                ])
                ->first(),
        ]);
    }

    public function serviceRequest(): Response
    {
        $categories = RequestCategory::query()
            ->select([
                'id',
                'name',
                'name_ar',
                'name_en',
                'status',
                'has_sub_categories',
            ])
            ->with([
                'subcategories:id,category_id,name,name_ar,name_en,status',
            ])
            ->orderByRaw('COALESCE(name_en, name) asc')
            ->get()
            ->map(fn (RequestCategory $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'name_ar' => $category->name_ar,
                'name_en' => $category->name_en,
                'status' => (bool) $category->status,
                'has_sub_categories' => (bool) $category->has_sub_categories,
                'subcategories' => $category->subcategories->map(fn ($subcategory): array => [
                    'id' => $subcategory->id,
                    'name' => $subcategory->name,
                    'name_ar' => $subcategory->name_ar,
                    'name_en' => $subcategory->name_en,
                    'status' => (bool) $subcategory->status,
                ])->values()->all(),
            ])
            ->values()
            ->all();

        return $this->renderTab('service-request', [
            'serviceRequestSettings' => [
                'types' => self::SERVICE_REQUEST_TYPES,
                'categories' => $categories,
            ],
        ]);
    }

    public function serviceRequestDetails(string $type, string $catCode, RequestCategory $catId): Response
    {
        abort_unless($this->serviceRequestTypes()->pluck('key')->contains($type), 404);

        $catId->load([
            'subcategories:id,category_id,name,name_ar,name_en,status',
            'serviceSettings:id,category_id,visibilities,permissions,submit_request_before_type,submit_request_before_value,capacity_type,capacity_value',
        ]);

        return Inertia::render('app-settings/settings/ServiceRequestDetails', [
            'tabs' => $this->tabsPayload(),
            'requestType' => $type,
            'categoryCode' => $catCode,
            'category' => [
                'id' => $catId->id,
                'name' => $catId->name,
                'name_ar' => $catId->name_ar,
                'name_en' => $catId->name_en,
                'status' => (bool) $catId->status,
                'has_sub_categories' => (bool) $catId->has_sub_categories,
                'subcategories' => $catId->subcategories->map(fn ($subcategory): array => [
                    'id' => $subcategory->id,
                    'name' => $subcategory->name,
                    'name_ar' => $subcategory->name_ar,
                    'name_en' => $subcategory->name_en,
                    'status' => (bool) $subcategory->status,
                ])->values()->all(),
            ],
            'serviceSetting' => $catId->serviceSettings->first(fn (ServiceSetting $setting): bool => (int) $setting->category_id === (int) $catId->id),
        ]);
    }

    public function storeInvoice(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'vat' => ['required', 'numeric', 'min:0', 'max:100'],
            'vat_number' => ['nullable', 'string', 'max:50'],
            'cr_number' => ['nullable', 'string', 'max:50'],
            'instructions' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $invoiceSetting = InvoiceSetting::query()->firstOrNew();
        $invoiceSetting->fill($validated);
        $invoiceSetting->save();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Invoice settings updated.'),
        ]);

        return to_route('settings.invoice');
    }

    public function visitorRequest(): Response
    {
        return $this->renderTab('visitor-request', [
            'visitorRequestSetting' => $this->systemSettingPayload('visitor-request'),
        ]);
    }

    public function storeVisitorRequest(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'enabled' => ['required', 'boolean'],
            'require_pre_approval' => ['required', 'boolean'],
            'max_visitors_per_request' => ['required', 'integer', 'min:1', 'max:50'],
            'allowed_visit_duration_minutes' => ['nullable', 'integer', 'min:15', 'max:1440'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->upsertSystemSetting('visitor-request', $validated);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Visitor request settings updated.'),
        ]);

        return to_route('settings.visitor-request');
    }

    public function bankDetails(): Response
    {
        return $this->renderTab('bank-details', [
            'bankDetailsSetting' => $this->systemSettingPayload('bank-details'),
        ]);
    }

    public function storeBankDetails(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'beneficiary_name' => ['required', 'string', 'max:255'],
            'bank_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'regex:/^[0-9]+$/', 'min:14', 'max:34'],
            'iban' => ['required', 'string', 'max:34'],
        ]);

        $this->upsertSystemSetting('bank-details', $validated);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $validated,
                'message' => __('Bank settings saved successfully.'),
            ]);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Bank details updated.'),
        ]);

        return to_route('settings.bank-details');
    }

    public function visitsDetails(): Response
    {
        return $this->renderTab('visits-details', [
            'visitsDetailsSetting' => $this->systemSettingPayload('visits-details'),
        ]);
    }

    public function storeVisitsDetails(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'is_all_day' => ['required', 'boolean'],
            'days' => ['required', 'array', 'min:1'],
            'days.*' => ['string', 'max:20'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'max_daily_visits' => ['nullable', 'integer', 'min:1', 'max:1000'],
        ]);

        $this->upsertSystemSetting('visits-details', $validated);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $validated,
                'message' => __('Visits settings saved successfully.'),
            ]);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Visits details updated.'),
        ]);

        return to_route('settings.visits-details');
    }

    public function salesDetails(): Response
    {
        return $this->renderTab('sales-details', [
            'salesDetailsSetting' => $this->systemSettingPayload('sales-details'),
        ]);
    }

    public function storeSalesDetails(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'deposit_time_limit_days' => ['required', 'integer', 'min:1', 'max:365'],
            'cash_contract_signing_days' => ['required', 'integer', 'min:1', 'max:365'],
            'bank_contract_signing_days' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        $this->upsertSystemSetting('sales-details', $validated);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $validated,
                'message' => __('Sales settings saved successfully.'),
            ]);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Sales details updated.'),
        ]);

        return to_route('settings.sales-details');
    }

    public function marketplaceBankSettings(): JsonResponse
    {
        return response()->json([
            'data' => $this->systemSettingPayload('bank-details'),
            'success' => true,
            'message' => __('Bank settings loaded successfully.'),
        ]);
    }

    public function destroyMarketplaceBankSetting(SystemSetting $systemSetting): JsonResponse
    {
        if ($systemSetting->key !== 'bank-details') {
            return response()->json([
                'success' => false,
                'message' => __('Bank settings not found.'),
            ], 404);
        }

        $settingId = $systemSetting->id;
        $systemSetting->delete();

        return response()->json([
            'data' => [
                'id' => $settingId,
            ],
            'success' => true,
            'message' => __('Bank settings deleted successfully.'),
        ]);
    }

    public function updateMarketplaceBankSetting(Request $request, SystemSetting $systemSetting): JsonResponse
    {
        if ($systemSetting->key !== 'bank-details') {
            return response()->json([
                'success' => false,
                'message' => __('Bank settings not found.'),
            ], 404);
        }

        $validated = $request->validate([
            'beneficiary_name' => ['required', 'string', 'max:255'],
            'bank_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'regex:/^[0-9]+$/', 'min:14', 'max:34'],
            'iban' => ['required', 'string', 'max:34'],
        ]);

        $systemSetting->payload = $validated;
        $systemSetting->save();

        return response()->json([
            'data' => [
                'id' => $systemSetting->id,
                ...$validated,
            ],
            'success' => true,
            'message' => __('Bank settings updated successfully.'),
        ]);
    }

    public function marketplaceSalesSettings(): JsonResponse
    {
        return response()->json([
            'data' => $this->systemSettingPayload('sales-details'),
            'success' => true,
            'message' => __('Sales settings loaded successfully.'),
        ]);
    }

    public function marketplaceVisitsSettings(): JsonResponse
    {
        return response()->json([
            'data' => $this->systemSettingPayload('visits-details'),
            'success' => true,
            'message' => __('Visits settings loaded successfully.'),
        ]);
    }

    /**
     * @param  array<string, mixed>  $extraProps
     */
    private function renderTab(string $activeTab, array $extraProps = []): Response
    {
        return Inertia::render('app-settings/settings/Index', [
            'activeTab' => $activeTab,
            'pageTitle' => self::TABS[$activeTab],
            'tabs' => $this->tabsPayload(),
            ...$extraProps,
        ]);
    }

    /**
     * @return Collection<int, array{key: string, label: string, description: string}>
     */
    private function serviceRequestTypes(): Collection
    {
        return collect(self::SERVICE_REQUEST_TYPES);
    }

    /**
     * @return array<int, array{key: string, label: string, href: string}>
     */
    private function tabsPayload(): array
    {
        return collect(self::TABS)
            ->map(fn (string $label, string $key): array => [
                'key' => $key,
                'label' => $label,
                'href' => route('settings.'.$key),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>|null
     */
    private function systemSettingPayload(string $key): ?array
    {
        /** @var array<string, mixed>|null $payload */
        $payload = SystemSetting::query()
            ->where('key', $key)
            ->value('payload');

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function upsertSystemSetting(string $key, array $payload): void
    {
        $setting = SystemSetting::query()->firstOrNew(['key' => $key]);
        $setting->payload = $payload;
        $setting->save();
    }
}
