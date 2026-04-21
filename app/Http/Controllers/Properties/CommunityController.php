<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Community;
use App\Models\Country;
use App\Models\Currency;
use App\Models\District;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CommunityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $communities = Community::query()
            ->withCount(['buildings', 'units', 'requests'])
            ->with(['country', 'city', 'district', 'currency'])
            ->latest()
            ->paginate(15);

        return Inertia::render('properties/communities/Index', [
            'communities' => $communities,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('properties/communities/Create', [
            'countries' => Country::query()->select('id', 'name', 'name_en', 'currency')->orderBy('id')->get(),
            'currencies' => Currency::query()->select('id', 'name', 'code', 'symbol')->orderBy('name')->get(),
            'cities' => City::query()->select('id', 'name', 'name_en', 'country_id')->orderBy('name')->get(),
            'districts' => District::query()->select('id', 'name', 'name_en', 'city_id')->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'sales_commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'rental_commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $community = Community::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Community created.')]);

        return to_route('communities.show', $community);
    }

    /**
     * Display the specified resource.
     */
    public function show(Community $community): Response
    {
        $community->loadCount(['buildings', 'units', 'requests'])
            ->load(['country', 'city', 'district', 'currency', 'buildings', 'facilities']);

        return Inertia::render('properties/communities/Show', [
            'community' => $community,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Community $community): Response
    {
        return Inertia::render('properties/communities/Edit', [
            'community' => $community,
            'countries' => Country::query()->select('id', 'name', 'name_en', 'currency')->orderBy('id')->get(),
            'currencies' => Currency::query()->select('id', 'name', 'code', 'symbol')->orderBy('name')->get(),
            'cities' => City::query()->select('id', 'name', 'name_en', 'country_id')->orderBy('name')->get(),
            'districts' => District::query()->select('id', 'name', 'name_en', 'city_id')->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Community $community): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'sales_commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'rental_commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $community->update($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Community updated.')]);

        return to_route('communities.show', $community);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Community $community): RedirectResponse
    {
        $community->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Community deleted.')]);

        return to_route('communities.index');
    }
}
