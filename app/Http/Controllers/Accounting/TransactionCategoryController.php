<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransactionCategoryController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Setting::class);

        $categories = Setting::query()
            ->where('type', 'transaction_category')
            ->orderBy('name_en')
            ->get();

        return Inertia::render('accounting/settings/categories/Index', [
            'categories' => $categories->map(fn (Setting $setting): array => $this->categoryPayload($setting)),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Setting::class);

        $validated = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'category_type' => ['required', 'string', 'in:income,expense'],
        ]);

        Setting::create([
            'name' => $validated['name_en'],
            'name_en' => $validated['name_en'],
            'name_ar' => $validated['name_ar'],
            'type' => 'transaction_category',
            'subtype' => $validated['category_type'],
            'is_active' => true,
            'is_default' => false,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Category saved.')]);

        return to_route('accounting.settings.categories.index');
    }

    public function update(Request $request, Setting $setting): RedirectResponse
    {
        $this->authorize('update', $setting);
        $this->abortIfNotTransactionCategory($setting);

        $validated = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
        ]);

        $setting->update([
            'name' => $validated['name_en'],
            'name_en' => $validated['name_en'],
            'name_ar' => $validated['name_ar'],
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Category saved.')]);

        return to_route('accounting.settings.categories.index');
    }

    public function toggleActive(Setting $setting): RedirectResponse
    {
        $this->authorize('update', $setting);
        $this->abortIfNotTransactionCategory($setting);

        $setting->update(['is_active' => ! $setting->is_active]);

        $message = $setting->is_active
            ? __('Category reactivated.')
            : __('Category deactivated.');

        Inertia::flash('toast', ['type' => 'success', 'message' => $message]);

        return to_route('accounting.settings.categories.index');
    }

    public function destroy(Setting $setting): RedirectResponse
    {
        $this->authorize('delete', $setting);
        $this->abortIfNotTransactionCategory($setting);

        if ($setting->is_default) {
            return back()->withErrors([
                'category' => __('Default categories cannot be deleted; you may deactivate them instead.'),
            ]);
        }

        $setting->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Category deleted.')]);

        return to_route('accounting.settings.categories.index');
    }

    private function abortIfNotTransactionCategory(Setting $setting): void
    {
        if ($setting->type !== 'transaction_category') {
            abort(404);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function categoryPayload(Setting $setting): array
    {
        return [
            'id' => $setting->id,
            'name_en' => $setting->name_en ?? $setting->name,
            'name_ar' => $setting->name_ar,
            'category_type' => $setting->subtype ?? 'income',
            'is_active' => $setting->is_active,
            'is_default' => $setting->is_default,
        ];
    }
}
