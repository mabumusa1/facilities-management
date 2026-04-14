<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    /**
     * Settings: Forms listing page.
     */
    public function forms(): Response
    {
        return Inertia::render('settings/forms/index');
    }

    /**
     * Settings: Create form page.
     */
    public function formCreate(): Response
    {
        return Inertia::render('settings/forms/create');
    }

    /**
     * Settings: Form preview page.
     */
    public function formPreview(int|string $id): Response
    {
        return Inertia::render('settings/forms/preview', ['id' => $id]);
    }

    /**
     * Settings: Form select building page.
     */
    public function formSelectBuilding(int|string $id): Response
    {
        return Inertia::render('settings/forms/select-building', ['id' => $id]);
    }

    /**
     * Settings: Form select community page.
     */
    public function formSelectCommunity(int|string $id): Response
    {
        return Inertia::render('settings/forms/select-community', ['id' => $id]);
    }

    /**
     * Settings: Bank details page.
     */
    public function bankDetails(): Response
    {
        return Inertia::render('settings/bank-details');
    }

    /**
     * Settings: Visits details page.
     */
    public function visitsDetails(): Response
    {
        return Inertia::render('settings/visits-details');
    }

    /**
     * Settings: Facilities page.
     */
    public function facilities(): Response
    {
        return Inertia::render('settings/facilities/index');
    }

    /**
     * Settings: Facilities list page.
     */
    public function facilitiesList(): Response
    {
        return Inertia::render('settings/facilities/list');
    }

    /**
     * Settings: Add facility page.
     */
    public function addFacility(): Response
    {
        return Inertia::render('settings/facilities/add');
    }

    /**
     * Settings: Add new facility page.
     */
    public function addNewFacility(): Response
    {
        return Inertia::render('settings/facilities/add-new');
    }

    /**
     * Settings: Facility details page.
     */
    public function facilityDetails(int|string $id): Response
    {
        return Inertia::render('settings/facilities/show', ['id' => $id]);
    }

    /**
     * Settings: Home service page.
     */
    public function homeService(int|string $id): Response
    {
        return Inertia::render('settings/home-service/index', ['id' => $id]);
    }

    /**
     * Settings: Home service category page.
     */
    public function homeServiceCategory(int|string $id): Response
    {
        return Inertia::render('settings/home-service/category', ['id' => $id]);
    }

    /**
     * Settings: Home service details page.
     */
    public function homeServiceDetails(int|string $id): Response
    {
        return Inertia::render('settings/home-service/details', ['id' => $id]);
    }

    /**
     * Settings: Home service new type page.
     */
    public function homeServiceNewType(int|string $id): Response
    {
        return Inertia::render('settings/home-service/new-type', ['id' => $id]);
    }

    /**
     * Settings: Home service add subcategory page.
     */
    public function homeServiceAddSubcategory(int|string $id): Response
    {
        return Inertia::render('settings/home-service/add-subcategory', ['id' => $id]);
    }

    /**
     * Settings: Home service select community page.
     */
    public function homeServiceSelectCommunity(int|string $id): Response
    {
        return Inertia::render('settings/home-service/select-community', ['id' => $id]);
    }

    /**
     * Settings: Neighbourhood service page.
     */
    public function neighbourhoodService(): Response
    {
        return Inertia::render('settings/neighbourhood-service');
    }

    /**
     * Settings: Service request categories page.
     */
    public function serviceRequest(): Response
    {
        return Inertia::render('settings/service-request/index');
    }

    /**
     * Settings: Service request category detail page.
     */
    public function serviceRequestCategory(): Response
    {
        return Inertia::render('settings/service-request/category');
    }

    /**
     * Settings: Visitor request page.
     */
    public function visitorRequest(): Response
    {
        return Inertia::render('settings/visitor-request');
    }

    /**
     * Settings: Invoice settings page.
     */
    public function invoice(): Response
    {
        return Inertia::render('settings/invoice');
    }

    /**
     * Settings: Sales details page.
     */
    public function salesDetails(): Response
    {
        return Inertia::render('settings/sales-details');
    }
}
