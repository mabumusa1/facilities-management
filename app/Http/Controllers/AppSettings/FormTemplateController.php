<?php

namespace App\Http\Controllers\AppSettings;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Community;
use App\Models\FormTemplate;
use App\Models\RequestCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FormTemplateController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('app-settings/settings/forms/Index', [
            'templates' => FormTemplate::query()
                ->with([
                    'requestCategory:id,name,name_ar,name_en',
                    'community:id,name',
                    'building:id,name',
                ])
                ->latest()
                ->paginate(15),
        ]);
    }

    public function create(Request $request): Response
    {
        $communityId = $request->integer('community_id') ?: null;

        return Inertia::render('app-settings/settings/forms/Create', [
            'template' => null,
            'categories' => $this->requestCategories(),
            'communities' => $this->communities(),
            'buildings' => $this->buildings(),
            'selectedCommunityId' => $communityId,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedPayload($request);
        $validated['schema'] ??= ['fields' => []];

        $template = FormTemplate::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Form template created.')]);

        return to_route('settings.forms.preview', $template);
    }

    public function selectCommunity(): Response
    {
        return Inertia::render('app-settings/settings/forms/SelectCommunity', [
            'communities' => $this->communities(),
        ]);
    }

    public function selectBuilding(Request $request): Response
    {
        $communityId = $request->integer('community_id') ?: null;

        return Inertia::render('app-settings/settings/forms/SelectBuilding', [
            'communities' => $this->communities(),
            'selectedCommunityId' => $communityId,
            'buildings' => $communityId
                ? $this->buildingsByCommunity($communityId)
                : collect(),
        ]);
    }

    public function preview(FormTemplate $formTemplate): Response
    {
        $formTemplate->load([
            'requestCategory:id,name,name_ar,name_en',
            'community:id,name',
            'building:id,name',
        ]);

        return Inertia::render('app-settings/settings/forms/Preview', [
            'template' => $formTemplate,
            'requiredFields' => collect($formTemplate->schema['fields'] ?? [])
                ->filter(fn ($field): bool => (bool) data_get($field, 'required', false))
                ->values(),
        ]);
    }

    public function edit(FormTemplate $formTemplate): Response
    {
        return Inertia::render('app-settings/settings/forms/Create', [
            'template' => $formTemplate,
            'categories' => $this->requestCategories(),
            'communities' => $this->communities(),
            'buildings' => $this->buildings(),
            'selectedCommunityId' => $formTemplate->community_id,
        ]);
    }

    public function update(Request $request, FormTemplate $formTemplate): RedirectResponse
    {
        $validated = $this->validatedPayload($request);
        $validated['schema'] ??= ['fields' => []];

        $formTemplate->update($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Form template updated.')]);

        return to_route('settings.forms.preview', $formTemplate);
    }

    public function destroy(FormTemplate $formTemplate): RedirectResponse
    {
        $formTemplate->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Form template deleted.')]);

        return to_route('settings.forms.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedPayload(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'request_category_id' => ['nullable', 'integer', 'exists:rf_request_categories,id'],
            'community_id' => ['nullable', 'integer', 'exists:rf_communities,id'],
            'building_id' => ['nullable', 'integer', 'exists:rf_buildings,id'],
            'schema' => ['nullable', 'array'],
            'schema.fields' => ['nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }

    private function requestCategories()
    {
        return RequestCategory::query()
            ->select('id', 'name', 'name_ar', 'name_en')
            ->orderByRaw('COALESCE(name_en, name) asc')
            ->get();
    }

    private function communities()
    {
        return Community::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    private function buildings()
    {
        return Building::query()
            ->select('id', 'name', 'rf_community_id')
            ->orderBy('name')
            ->get();
    }

    private function buildingsByCommunity(int $communityId)
    {
        return Building::query()
            ->where('rf_community_id', $communityId)
            ->select('id', 'name', 'rf_community_id')
            ->orderBy('name')
            ->get();
    }
}
