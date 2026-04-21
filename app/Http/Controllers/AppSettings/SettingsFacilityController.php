<?php

namespace App\Http\Controllers\AppSettings;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\Facility;
use App\Models\FacilityCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsFacilityController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('app-settings/settings/FacilitiesIndex', [
            'facilities' => Facility::query()
                ->with(['category:id,name,name_ar,name_en', 'community:id,name'])
                ->withCount('bookings')
                ->latest()
                ->paginate(15),
        ]);
    }

    public function show(Facility $facility): Response
    {
        $facility->load([
            'category:id,name,name_ar,name_en',
            'community:id,name',
            'bookings' => fn ($query) => $query->latest()->take(10),
        ]);

        return Inertia::render('app-settings/settings/FacilityShow', [
            'facility' => $facility,
        ]);
    }

    public function form(?Facility $facility = null): Response
    {
        return Inertia::render('app-settings/settings/FacilityForm', [
            'facility' => $facility,
            'categories' => FacilityCategory::query()
                ->select('id', 'name', 'name_ar', 'name_en')
                ->orderByRaw('COALESCE(name_en, name) asc')
                ->get(),
            'communities' => Community::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $facility = Facility::create($this->validatedPayload($request));

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Facility created.')]);

        return to_route('settings.facilities.show', $facility);
    }

    public function update(Request $request, Facility $facility): RedirectResponse
    {
        $facility->update($this->validatedPayload($request));

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Facility updated.')]);

        return to_route('settings.facilities.show', $facility);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedPayload(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:rf_facility_categories,id'],
            'community_id' => ['nullable', 'integer', 'exists:rf_communities,id'],
            'description' => ['nullable', 'string'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'open_time' => ['nullable', 'date_format:H:i'],
            'close_time' => ['nullable', 'date_format:H:i'],
            'booking_fee' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'requires_approval' => ['sometimes', 'boolean'],
        ]);
    }
}
