<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardModuleController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\LeaseApplicationController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\LeasingModuleController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\SubLeaseController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\VisitorAccessController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::inertia('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

// Auth status pages
Route::middleware(['auth'])->group(function () {
    Route::inertia('no-access', 'auth/no-access')->name('no-access');
    Route::inertia('forbidden', 'auth/forbidden')->name('forbidden');
});

// Protected routes with verification check
Route::middleware(['auth', 'verified', 'verified.user'])->group(function () {
    // Dashboard routes
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
        Route::get('announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');
        Route::get('announcements/edit/{announcement}', [AnnouncementController::class, 'edit'])->name('announcements.edit');

        Route::get('issues', [ServiceRequestController::class, 'index'])->name('issues.index');
        Route::get('issues/create', [ServiceRequestController::class, 'create'])->name('issues.create');
        Route::get('issues/{serviceRequest}/view', [ServiceRequestController::class, 'show'])->name('issues.view');
        Route::get('issues/{serviceRequest}/assign', [ServiceRequestController::class, 'edit'])->name('issues.assign');

        Route::get('bookings', [DashboardModuleController::class, 'bookings'])->name('bookings.index');
        Route::get('bookings/{facilityBooking}', [DashboardModuleController::class, 'bookingDetails'])->name('bookings.show');
        Route::get('booking-contracts', [DashboardModuleController::class, 'bookingContracts'])->name('booking-contracts.index');
        Route::get('booking-contracts/{marketplaceOffer}', [DashboardModuleController::class, 'bookingContractDetails'])->name('booking-contracts.show');

        Route::get('visits', [DashboardModuleController::class, 'visits'])->name('visits.index');
        Route::get('complaints', [DashboardModuleController::class, 'complaints'])->name('complaints.index');
        Route::get('complaints/{serviceRequest}', [DashboardModuleController::class, 'complaintDetails'])->name('complaints.show');
        Route::get('suggestions', [DashboardModuleController::class, 'suggestions'])->name('suggestions.index');
        Route::get('suggestions/{serviceRequest}', [DashboardModuleController::class, 'suggestionDetails'])->name('suggestions.show');

        Route::get('reports', [DashboardModuleController::class, 'reports'])->name('reports.index');
        Route::get('payment', [DashboardModuleController::class, 'payment'])->name('payment.index');

        Route::get('offers', [DashboardModuleController::class, 'offers'])->name('offers.index');
        Route::get('offers/create', [DashboardModuleController::class, 'offerCreate'])->name('offers.create');
        Route::get('offers/{marketplaceOffer}/view', [DashboardModuleController::class, 'offerView'])->name('offers.view');

        Route::get('directory', [DashboardModuleController::class, 'directory'])->name('directory.index');
        Route::get('directory/create', [DashboardModuleController::class, 'directoryCreate'])->name('directory.create');
        Route::get('directory/update', [DashboardModuleController::class, 'directoryUpdate'])->name('directory.update');
        Route::get('directory/{contact}', [DashboardModuleController::class, 'directoryDetails'])->name('directory.show');

        Route::get('move-out-tenants', [DashboardModuleController::class, 'moveOutTenants'])->name('move-out-tenants.index');
        Route::get('move-out-tenants/{lease}', [DashboardModuleController::class, 'moveOutTenantDetails'])->name('move-out-tenants.show');

        Route::get('system-reports', [DashboardModuleController::class, 'systemReports'])->name('system-reports.index');
        Route::get('system-reports/Lease', [DashboardModuleController::class, 'systemReportsLease'])->name('system-reports.lease');
        Route::get('system-reports/maintenance', [DashboardModuleController::class, 'systemReportsMaintenance'])->name('system-reports.maintenance');
        Route::get('power-bi-reports', [DashboardModuleController::class, 'powerBiReports'])->name('power-bi-reports.index');
    });

    // Properties module routes
    Route::get('properties-list/communities', [CommunityController::class, 'index'])
        ->name('properties-list.communities.index');
    Route::get('properties-list/new/community', [CommunityController::class, 'create'])
        ->name('properties-list.communities.create');
    Route::get('properties-list/communities/new/community', [CommunityController::class, 'create'])
        ->name('properties-list.communities.create.alt');
    Route::get('properties-list/communities/bulk-upload', [CommunityController::class, 'bulkUpload'])
        ->name('properties-list.communities.bulk-upload');
    Route::get('properties-list/communities/building/details/{building}', [BuildingController::class, 'show'])
        ->name('properties-list.communities.building.show');
    Route::get('properties-list/communities/community/details/{community}', [CommunityController::class, 'show'])
        ->name('properties-list.communities.show');
    Route::get('properties-list/buildings', [BuildingController::class, 'index'])
        ->name('properties-list.buildings.index');
    Route::get('properties-list/new/building', [BuildingController::class, 'create'])
        ->name('properties-list.buildings.create');
    Route::get('properties-list/buildings/bulk-upload', [BuildingController::class, 'bulkUpload'])
        ->name('properties-list.buildings.bulk-upload');
    Route::get('properties-list/buildings/{building}', [BuildingController::class, 'show'])
        ->name('properties-list.buildings.numeric-show');
    Route::get('properties-list/buildings/building/details/{building}', [BuildingController::class, 'show'])
        ->name('properties-list.buildings.show');
    Route::get('properties-list/units', [UnitController::class, 'index'])
        ->name('properties-list.units.index');
    Route::get('properties-list/units/new-unit', [UnitController::class, 'create'])
        ->name('properties-list.units.create');
    Route::get('properties-list/units/edit-unit', [UnitController::class, 'create'])
        ->name('properties-list.units.edit-alias');
    Route::get('properties-list/units/marketplace-listing', [UnitController::class, 'marketplaceListing'])
        ->name('properties-list.units.marketplace-listing');
    Route::get('properties-list/units/{unit}/marketplace', [UnitController::class, 'marketplaceDetails'])
        ->name('properties-list.units.marketplace-details');
    Route::get('properties-list/units/unit/details/{unit}', [UnitController::class, 'show'])
        ->name('properties-list.units.show');
    Route::resource('communities', CommunityController::class);
    Route::resource('buildings', BuildingController::class);
    Route::resource('units', UnitController::class);

    // Docs parity aliases for contact listing pages
    Route::get('contacts/tenants', function (Request $request, ContactController $controller) {
        $request->query->set('type', 'tenant');

        return $controller->index($request);
    })->name('contacts.tenants.index');
    Route::get('contacts/owners', function (Request $request, ContactController $controller) {
        $request->query->set('type', 'owner');

        return $controller->index($request);
    })->name('contacts.owners.index');
    Route::get('contacts/admins', function (Request $request, ContactController $controller) {
        $request->query->set('type', 'admin');

        return $controller->index($request);
    })->name('contacts.admins.index');
    Route::get('contacts/managers', function (Request $request, ContactController $controller) {
        $request->query->set('type', 'professional');

        return $controller->index($request);
    })->name('contacts.managers.index');
    Route::get('contacts/service-professional', function (Request $request, ContactController $controller) {
        $request->query->set('type', 'professional');

        return $controller->index($request);
    })->name('contacts.professionals.index');
    Route::get('contacts/ServiceProfessional', function (Request $request, ContactController $controller) {
        $request->query->set('type', 'professional');

        return $controller->index($request);
    })->name('contacts.professionals.legacy.index');
    Route::get('contacts/tenants/{contact}', [ContactController::class, 'show'])
        ->whereNumber('contact')
        ->name('contacts.tenants.show');
    Route::get('contacts/owners/{contact}', [ContactController::class, 'show'])
        ->whereNumber('contact')
        ->name('contacts.owners.show');
    Route::get('contacts/managers/{contact}', [ContactController::class, 'show'])
        ->whereNumber('contact')
        ->name('contacts.managers.show');
    Route::get('contacts/family-members/{contact}', [ContactController::class, 'show'])
        ->whereNumber('contact')
        ->name('contacts.family-members.show');

    // Docs parity aliases for contact forms
    Route::get('contacts/{legacyType}/form', function (Request $request, ContactController $controller, string $legacyType) {
        $contactType = match (strtolower($legacyType)) {
            'tenant' => 'tenant',
            'owner' => 'owner',
            'admin' => 'admin',
            'professional', 'manager' => 'professional',
            default => null,
        };

        abort_if($contactType === null, 404);

        $request->query->set('type', $contactType);

        return $controller->create($request);
    })->whereIn('legacyType', ['Tenant', 'Owner', 'Admin', 'Professional', 'Manager'])->name('contacts.legacy.form');
    Route::get('contacts/{contact}/form', [ContactController::class, 'edit'])
        ->whereNumber('contact')
        ->name('contacts.legacy.edit');

    // Contacts module routes
    Route::get('contacts/statistics', [ContactController::class, 'statistics'])->name('contacts.statistics');
    Route::resource('contacts', ContactController::class);

    // Service Requests module routes
    Route::resource('service-requests', ServiceRequestController::class);
    Route::get('requests', [ServiceRequestController::class, 'index'])->name('requests.index');
    Route::get('requests/create', [ServiceRequestController::class, 'create'])->name('requests.create');
    Route::get('requests/history', [ServiceRequestController::class, 'history'])->name('requests.history');
    Route::get('requests/{serviceRequest}', [ServiceRequestController::class, 'show'])
        ->whereNumber('serviceRequest')
        ->name('requests.show');

    // Visitor Access module routes
    Route::get('visitor-access', [VisitorAccessController::class, 'index'])->name('visitor-access.index');
    Route::get('visitor-access/history', [VisitorAccessController::class, 'history'])->name('visitor-access.history');
    Route::get('visitor-access/visitor-details/{visitorAccess}', [VisitorAccessController::class, 'show'])
        ->name('visitor-access.show');

    // Leases module routes
    Route::resource('leases', LeaseController::class);
    Route::post('leases/wizard/save-step', [LeaseController::class, 'saveStep'])->name('leases.wizard.save-step');
    Route::post('leases/{lease}/activate', [LeaseController::class, 'activate'])->name('leases.activate');
    Route::get('leases/{lease}/terminate', [LeaseController::class, 'terminateForm'])->name('leases.terminate.form');
    Route::post('leases/{lease}/terminate', [LeaseController::class, 'terminate'])->name('leases.terminate');
    Route::get('leases/{lease}/move-out', [LeaseController::class, 'moveOutForm'])->name('leases.move-out.form');
    Route::post('leases/{lease}/move-out', [LeaseController::class, 'moveOut'])->name('leases.move-out');
    Route::get('leases/{lease}/renew', [LeaseController::class, 'renewForm'])->name('leases.renew');
    Route::post('leases/{lease}/renew', [LeaseController::class, 'renew'])->name('leases.renew.store');

    // Transactions module routes
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/list', [TransactionController::class, 'list'])->name('transactions.list');
    Route::get('transactions/chart-of-accounts', [TransactionController::class, 'chartOfAccounts'])->name('transactions.chart-of-accounts');
    Route::get('transactions/journal-entries', [TransactionController::class, 'journalEntries'])->name('transactions.journal-entries');
    Route::get('transactions/overdues', [TransactionController::class, 'overdues'])->name('transactions.overdues');
    Route::get('transactions/record-transaction', [TransactionController::class, 'recordTransaction'])->name('transactions.record-transaction');
    Route::get('transactions/tenant/{contact}', [TransactionController::class, 'contactTransactions'])->name('transactions.tenant');
    Route::get('transactions/money-in', function (Request $request, TransactionController $controller) {
        $request->query->set('filter_type', 'money_in');

        return $controller->index($request);
    })->name('transactions.money-in');
    Route::get('transactions/money-out', function (Request $request, TransactionController $controller) {
        $request->query->set('filter_type', 'money_out');

        return $controller->index($request);
    })->name('transactions.money-out');
    Route::get('transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::get('accounting', [TransactionController::class, 'index'])->name('accounting.index');
    Route::get('accounting/main', [TransactionController::class, 'index'])->name('accounting.main');

    // Lease Applications routes
    Route::resource('lease-applications', LeaseApplicationController::class);
    Route::post('lease-applications/{leaseApplication}/transition', [LeaseApplicationController::class, 'transition'])->name('lease-applications.transition');
    Route::post('lease-applications/{leaseApplication}/submit-for-review', [LeaseApplicationController::class, 'submitForReview'])->name('lease-applications.submit-for-review');
    Route::post('lease-applications/{leaseApplication}/approve', [LeaseApplicationController::class, 'approve'])->name('lease-applications.approve');
    Route::post('lease-applications/{leaseApplication}/reject', [LeaseApplicationController::class, 'reject'])->name('lease-applications.reject');
    Route::post('lease-applications/{leaseApplication}/cancel', [LeaseApplicationController::class, 'cancel'])->name('lease-applications.cancel');
    Route::post('lease-applications/{leaseApplication}/hold', [LeaseApplicationController::class, 'hold'])->name('lease-applications.hold');
    Route::post('lease-applications/{leaseApplication}/resume', [LeaseApplicationController::class, 'resume'])->name('lease-applications.resume');
    Route::post('lease-applications/{leaseApplication}/send-quote', [LeaseApplicationController::class, 'sendQuote'])->name('lease-applications.send-quote');
    Route::post('lease-applications/{leaseApplication}/convert-to-lease', [LeaseApplicationController::class, 'convertToLease'])->name('lease-applications.convert-to-lease');
    Route::get('lease-applications/{leaseApplication}/history', [LeaseApplicationController::class, 'history'])->name('lease-applications.history');

    // Sub-Leases module routes
    Route::resource('sub-leases', SubLeaseController::class);

    // Leasing Module UI (overview dashboard)
    Route::get('leasing', [LeasingModuleController::class, 'index'])->name('leasing.index');

    // Docs parity aliases for leasing pages
    Route::get('leasing/leases', [LeaseController::class, 'index'])->name('leasing.leases.index');
    Route::get('leasing/leases/create', [LeaseController::class, 'create'])->name('leasing.leases.create');
    Route::get('leasing/leases/expiring-leases', [LeaseController::class, 'expiringLeases'])->name('leasing.leases.expiring');
    Route::get('leasing/leases/expiring-leases/{lease}', [LeaseController::class, 'expiringLeaseDetails'])->name('leasing.leases.expiring.show');
    Route::get('leasing/leases/overdues', [LeaseController::class, 'overdues'])->name('leasing.leases.overdues');
    Route::get('leasing/leases/{lease}', [LeaseController::class, 'show'])->name('leasing.leases.show');
    Route::get('leasing/leases/{lease}/renew', [LeaseController::class, 'renewForm'])->name('leasing.leases.renew');
    Route::get('leasing/leases/renew/{lease}', [LeaseController::class, 'renewForm'])->name('leasing.leases.renew.legacy');
    Route::get('leasing/details/{lease}', [LeaseController::class, 'show'])->name('leasing.details.show');
    Route::get('leasing/apps', [LeaseApplicationController::class, 'index'])->name('leasing.apps.index');
    Route::get('leasing/statistics', [LeasingModuleController::class, 'statistics'])->name('leasing.statistics');
    Route::get('leasing/visits', [LeasingModuleController::class, 'visits'])->name('leasing.visits');
    Route::get('leasing/quotes', [LeaseApplicationController::class, 'quotes'])->name('leasing.quotes');
    Route::get('leasing/sub-leases', [SubLeaseController::class, 'index'])->name('leasing.sub-leases.index');
    Route::get('leasing/sub-leases/{subLease}', [SubLeaseController::class, 'show'])->name('leasing.sub-leases.show');

    // Marketplace module routes
    Route::get('marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');
    Route::get('marketplace/listing', [MarketplaceController::class, 'listing'])->name('marketplace.listing');
    Route::get('marketplace/listing/off-plan-sale-form', [MarketplaceController::class, 'offPlanForm'])->name('marketplace.off-plan-sale-form');
    Route::get('marketplace/customers', [MarketplaceController::class, 'customers'])->name('marketplace.customers');
    Route::get('marketplace/customers/upload-leads', [MarketplaceController::class, 'uploadLeads'])->name('marketplace.upload-leads');
    Route::get('marketplace/customers/upload-leads/errors', [MarketplaceController::class, 'uploadLeadsErrors'])->name('marketplace.upload-leads.errors');
    Route::get('marketplace/favorites', [MarketplaceController::class, 'favorites'])->name('marketplace.favorites');
    Route::get('marketplace/off-plan-form', [MarketplaceController::class, 'offPlanForm'])->name('marketplace.off-plan-form');
    Route::prefix('marketplace/admin')->name('marketplace.admin.')->group(function () {
        Route::get('bookings', [MarketplaceController::class, 'adminBookings'])->name('bookings');
        Route::get('communities', [MarketplaceController::class, 'adminCommunities'])->name('communities');
        Route::get('communities/{community}', [MarketplaceController::class, 'adminCommunityShow'])->name('communities.show');
        Route::get('units', [MarketplaceController::class, 'adminUnits'])->name('units');
        Route::get('units/{unit}', [MarketplaceController::class, 'adminUnitShow'])->name('units.show');
        Route::get('visits', [MarketplaceController::class, 'adminVisits'])->name('visits');
        Route::get('visits/{visit}', [MarketplaceController::class, 'adminVisitShow'])->name('visits.show');
        Route::get('settings', [MarketplaceController::class, 'adminSettings'])->name('settings');
    });

    // Directory module routes (extended)
    Route::get('directory', [DirectoryController::class, 'index'])->name('directory.index');
    Route::get('directory/facilities', [DirectoryController::class, 'facilities'])->name('directory.facilities');
    Route::get('directory/addNewFacility', [DirectoryController::class, 'addFacility'])->name('directory.add-facility');
    Route::get('directory/facility/{facility}', [DirectoryController::class, 'facilityShow'])->name('directory.facility.show');
    Route::get('directory/building/{building}', [DirectoryController::class, 'buildingShow'])->name('directory.building.show');
    Route::get('directory/community/{community}', [DirectoryController::class, 'communityShow'])->name('directory.community.show');
    Route::get('directory/owner', [DirectoryController::class, 'owner'])->name('directory.owner');
    Route::get('directory/documents', [DirectoryController::class, 'documents'])->name('directory.documents');

    // Settings extended module routes
    Route::get('settings/forms', [SettingsController::class, 'forms'])->name('settings.forms');
    Route::get('settings/forms/create', [SettingsController::class, 'formCreate'])->name('settings.forms.create');
    Route::get('settings/forms/{id}/preview', [SettingsController::class, 'formPreview'])->name('settings.forms.preview');
    Route::get('settings/forms/{id}/select-building', [SettingsController::class, 'formSelectBuilding'])->name('settings.forms.select-building');
    Route::get('settings/forms/{id}/select-community', [SettingsController::class, 'formSelectCommunity'])->name('settings.forms.select-community');
    Route::get('settings/bank-details', [SettingsController::class, 'bankDetails'])->name('settings.bank-details');
    Route::get('settings/visits-details', [SettingsController::class, 'visitsDetails'])->name('settings.visits-details');
    Route::get('settings/facilities', [SettingsController::class, 'facilities'])->name('settings.facilities');
    Route::get('settings/facilities/list', [SettingsController::class, 'facilitiesList'])->name('settings.facilities.list');
    Route::get('settings/add-facility', [SettingsController::class, 'addFacility'])->name('settings.add-facility');
    Route::get('settings/add-new-facility', [SettingsController::class, 'addNewFacility'])->name('settings.add-new-facility');
    Route::get('settings/facility/{id}', [SettingsController::class, 'facilityDetails'])->name('settings.facility-details');
    Route::get('settings/home-service-settings/{id}', [SettingsController::class, 'homeService'])->name('settings.home-service');
    Route::get('settings/home-service-settings/{id}/category', [SettingsController::class, 'homeServiceCategory'])->name('settings.home-service.category');
    Route::get('settings/home-service-settings/{id}/details', [SettingsController::class, 'homeServiceDetails'])->name('settings.home-service.details');
    Route::get('settings/home-service-settings/{id}/new-type', [SettingsController::class, 'homeServiceNewType'])->name('settings.home-service.new-type');
    Route::get('settings/home-service-settings/{id}/add-subcategory', [SettingsController::class, 'homeServiceAddSubcategory'])->name('settings.home-service.add-subcategory');
    Route::get('settings/home-service-settings/{id}/select-community', [SettingsController::class, 'homeServiceSelectCommunity'])->name('settings.home-service.select-community');
    Route::get('settings/neighbourhood-service', [SettingsController::class, 'neighbourhoodService'])->name('settings.neighbourhood-service');
    Route::get('settings/service-request', [SettingsController::class, 'serviceRequest'])->name('settings.service-request');
    Route::get('settings/service-request/category', [SettingsController::class, 'serviceRequestCategory'])->name('settings.service-request.category');
    Route::get('settings/visitor-request', [SettingsController::class, 'visitorRequest'])->name('settings.visitor-request');
    Route::get('settings/invoice', [SettingsController::class, 'invoice'])->name('settings.invoice');
    Route::get('settings/sales-details', [SettingsController::class, 'salesDetails'])->name('settings.sales-details');

    // Reports module routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/leases', [ReportController::class, 'leases'])->name('leases');
        Route::get('/maintenance', [ReportController::class, 'maintenance'])->name('maintenance');
    });
    Route::get('reporting', [ReportController::class, 'index'])->name('reporting.index');
    Route::get('reporting/leases', [ReportController::class, 'leases'])->name('reporting.leases');
    Route::get('reporting/maintenance', [ReportController::class, 'maintenance'])->name('reporting.maintenance');

    // Notifications module routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
    });

    // Announcements module routes
    Route::resource('announcements', AnnouncementController::class);
    Route::post('announcements/{announcement}/publish', [AnnouncementController::class, 'publish'])->name('announcements.publish');
    Route::post('announcements/{announcement}/cancel', [AnnouncementController::class, 'cancel'])->name('announcements.cancel');

    // Admins routes (contacts with type=admin)
    Route::get('admins', function (Request $request, ContactController $controller) {
        $request->query->set('type', 'admin');

        return $controller->index($request);
    })->name('admins.index');
    Route::get('admins/{contact}', [ContactController::class, 'show'])->name('admins.show');

    // Maintenance mode page
    Route::get('maintenance', fn () => Inertia::render('maintenance'))->name('maintenance');

    // Misc docs alias routes
    Route::get('edit-profile', [ProfileController::class, 'edit'])->name('edit-profile');
    Route::get('more', fn () => Inertia::render('more-page'))->name('more-page');
    Route::get('pricing', fn () => Inertia::render('pricing-page'))->name('pricing-page');
    Route::get('privacy_policy', fn () => Inertia::render('legal/privacy-policy'))->name('legal.privacy-policy');
    Route::get('terms_and_conditions', fn () => Inertia::render('legal/terms-and-conditions'))->name('legal.terms-and-conditions');
});

// Test routes for RBAC middleware (only in testing environment)
if (app()->environment('testing')) {
    Route::middleware(['auth', 'permission:view-communities'])
        ->get('/test-permission-communities', fn () => response('OK'));

    Route::middleware(['auth', 'capability:manage-properties'])
        ->get('/test-capability-properties', fn () => response('OK'));

    Route::middleware(['auth', 'contact.type:admin,owner'])
        ->get('/test-contact-type-admin-owner', fn () => response('OK'));

    Route::middleware(['auth', 'verified.user'])
        ->get('/test-verified-user', fn () => response('OK'));
}

require __DIR__.'/settings.php';
