<?php

use App\Http\Controllers\Accounting\TransactionController;
use App\Http\Controllers\AppSettings\FacilityCategoryController as AppFacilityCategoryController;
use App\Http\Controllers\AppSettings\FormTemplateController;
use App\Http\Controllers\AppSettings\GeneralSettingController;
use App\Http\Controllers\AppSettings\InvoiceSettingController;
use App\Http\Controllers\AppSettings\RequestCategoryController;
use App\Http\Controllers\AppSettings\RequestSubcategoryController;
use App\Http\Controllers\AppSettings\ServiceSettingController;
use App\Http\Controllers\AppSettings\SettingsFacilityController;
use App\Http\Controllers\AppSettings\SettingsShellController;
use App\Http\Controllers\Communication\AnnouncementController;
use App\Http\Controllers\Contacts\AdminController;
use App\Http\Controllers\Contacts\OwnerController;
use App\Http\Controllers\Contacts\ProfessionalController;
use App\Http\Controllers\Contacts\ResidentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Documents\DocumentCenterController;
use App\Http\Controllers\Documents\ExcelSheetController;
use App\Http\Controllers\Documents\FileController;
use App\Http\Controllers\Facilities\FacilityBookingController;
use App\Http\Controllers\Facilities\FacilityController;
use App\Http\Controllers\Leasing\LeaseController;
use App\Http\Controllers\Marketplace\MarketplaceController;
use App\Http\Controllers\Properties\BuildingController;
use App\Http\Controllers\Properties\CommunityController;
use App\Http\Controllers\Properties\UnitController;
use App\Http\Controllers\Reports\ReportsController;
use App\Http\Controllers\Requests\ServiceRequestController;
use App\Http\Controllers\Shared\LookupController;
use App\Http\Controllers\Shared\NotificationController;
use App\Http\Controllers\VisitorAccess\VisitorAccessController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified', 'tenant'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    // Properties
    Route::resource('communities', CommunityController::class);
    Route::resource('buildings', BuildingController::class);
    Route::resource('units', UnitController::class);

    // Leasing
    Route::resource('leases', LeaseController::class);
    Route::get('leases/{lease}/subleases/create', [LeaseController::class, 'createSublease'])->name('leases.subleases.create');
    Route::post('leases/{lease}/subleases', [LeaseController::class, 'storeSublease'])->name('leases.subleases.store');

    // Requests
    Route::resource('requests', ServiceRequestController::class)->parameters([
        'requests' => 'serviceRequest',
    ]);

    // Facilities
    Route::resource('facilities', FacilityController::class);
    Route::resource('facility-bookings', FacilityBookingController::class);

    // Accounting
    Route::resource('transactions', TransactionController::class);

    // Communication
    Route::resource('announcements', AnnouncementController::class);

    // Contacts
    Route::resource('owners', OwnerController::class);
    Route::resource('residents', ResidentController::class);
    Route::resource('admins', AdminController::class);
    Route::resource('professionals', ProfessionalController::class);

    // App Settings
    Route::prefix('app-settings')->name('app-settings.')->group(function () {
        Route::resource('request-categories', RequestCategoryController::class)->except(['show']);
        Route::resource('request-categories.subcategories', RequestSubcategoryController::class)->only(['store', 'update', 'destroy']);
        Route::post('service-settings', [ServiceSettingController::class, 'updateOrCreate'])->name('service-settings.update-or-create');
        Route::resource('facility-categories', AppFacilityCategoryController::class)->except(['show']);
        Route::get('invoice', [InvoiceSettingController::class, 'edit'])->name('invoice.edit');
        Route::put('invoice', [InvoiceSettingController::class, 'update'])->name('invoice.update');
        Route::get('general', [GeneralSettingController::class, 'index'])->name('general.index');
        Route::post('general', [GeneralSettingController::class, 'store'])->name('general.store');
        Route::put('general/{setting}', [GeneralSettingController::class, 'update'])->name('general.update');
        Route::delete('general/{setting}', [GeneralSettingController::class, 'destroy'])->name('general.destroy');
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('invoice', [SettingsShellController::class, 'invoice'])->name('invoice');
        Route::post('invoice', [SettingsShellController::class, 'storeInvoice'])->name('invoice.store');
        Route::get('service-request', [SettingsShellController::class, 'serviceRequest'])->name('service-request');
        Route::get('service-request/{type}/{catCode}/{catId}', [SettingsShellController::class, 'serviceRequestDetails'])->name('service-request.details');
        Route::get('visitor-request', [SettingsShellController::class, 'visitorRequest'])->name('visitor-request');
        Route::post('visitor-request', [SettingsShellController::class, 'storeVisitorRequest'])->name('visitor-request.store');
        Route::get('bank-details', [SettingsShellController::class, 'bankDetails'])->name('bank-details');
        Route::post('bank-details', [SettingsShellController::class, 'storeBankDetails'])->name('bank-details.store');
        Route::get('visits-details', [SettingsShellController::class, 'visitsDetails'])->name('visits-details');
        Route::post('visits-details', [SettingsShellController::class, 'storeVisitsDetails'])->name('visits-details.store');
        Route::get('sales-details', [SettingsShellController::class, 'salesDetails'])->name('sales-details');
        Route::post('sales-details', [SettingsShellController::class, 'storeSalesDetails'])->name('sales-details.store');

        Route::get('facilities', [SettingsFacilityController::class, 'index'])->name('facilities.index');
        Route::get('facility/{facility}', [SettingsFacilityController::class, 'show'])->name('facilities.show');
        Route::get('addNewFacility/{facility?}', [SettingsFacilityController::class, 'form'])->name('facilities.form');
        Route::post('facilities', [SettingsFacilityController::class, 'store'])->name('facilities.store');
        Route::put('facilities/{facility}', [SettingsFacilityController::class, 'update'])->name('facilities.update');

        Route::get('forms', [FormTemplateController::class, 'index'])->name('forms.index');
        Route::get('forms/create', [FormTemplateController::class, 'create'])->name('forms.create');
        Route::post('forms', [FormTemplateController::class, 'store'])->name('forms.store');
        Route::get('forms/select-community', [FormTemplateController::class, 'selectCommunity'])->name('forms.select-community');
        Route::get('forms/select-building', [FormTemplateController::class, 'selectBuilding'])->name('forms.select-building');
        Route::get('forms/preview/{formTemplate}', [FormTemplateController::class, 'preview'])->name('forms.preview');
        Route::get('forms/{formTemplate}/edit', [FormTemplateController::class, 'edit'])->name('forms.edit');
        Route::put('forms/{formTemplate}', [FormTemplateController::class, 'update'])->name('forms.update');
        Route::delete('forms/{formTemplate}', [FormTemplateController::class, 'destroy'])->name('forms.destroy');
    });

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');

    Route::get('documents', [DocumentCenterController::class, 'index'])->name('documents.index');

    Route::prefix('rf')->name('rf.')->group(function () {
        Route::get('modules', [LookupController::class, 'modules'])->name('modules');
        Route::get('statuses', [LookupController::class, 'statuses'])->name('statuses');
        Route::get('common-lists', [LookupController::class, 'commonLists'])->name('common-lists');
        Route::get('leads', [LookupController::class, 'leads'])->name('leads');
        Route::get('countries', [LookupController::class, 'countries'])->name('countries');

        Route::post('files', [FileController::class, 'store'])->name('files.store');
        Route::delete('files/{media}', [FileController::class, 'destroy'])->name('files.destroy');

        Route::post('excel-sheets', [ExcelSheetController::class, 'store'])->name('excel-sheets.store');
        Route::post('excel-sheets/land', [ExcelSheetController::class, 'storeLand'])->name('excel-sheets.land');
        Route::post('excel-sheets/leads', [ExcelSheetController::class, 'storeLeads'])->name('excel-sheets.leads');
        Route::get('excel-sheets/leads/errors', [ExcelSheetController::class, 'leadsErrors'])->name('excel-sheets.leads.errors');
    });

    Route::prefix('marketplace')->name('marketplace.')->group(function () {
        Route::get('/', [MarketplaceController::class, 'overview'])->name('overview');
        Route::get('customers', [MarketplaceController::class, 'customers'])->name('customers');
        Route::post('customers/sales-lead', [MarketplaceController::class, 'createSalesLead'])->name('customers.sales-lead');
        Route::post('customers/property-lead', [MarketplaceController::class, 'createPropertyLead'])->name('customers.property-lead');

        Route::get('listing', [MarketplaceController::class, 'listing'])->name('listing');
        Route::post('listing', [MarketplaceController::class, 'storeListing'])->name('listing.store');
        Route::put('listing/{marketplaceUnit}', [MarketplaceController::class, 'updateListing'])->name('listing.update');
        Route::delete('listing/{marketplaceUnit}', [MarketplaceController::class, 'destroyListing'])->name('listing.destroy');

        Route::get('visits', [MarketplaceController::class, 'visits'])->name('visits.index');
        Route::get('visits/{marketplaceVisit}', [MarketplaceController::class, 'showVisit'])->name('visits.show');
        Route::post('visits/schedule', [MarketplaceController::class, 'scheduleViewing'])->name('visits.schedule');
        Route::post('visits/{marketplaceVisit}/cancel', [MarketplaceController::class, 'cancelVisit'])->name('visits.cancel');
        Route::post('visits/{marketplaceVisit}/send-contract', [MarketplaceController::class, 'sendContract'])->name('visits.send-contract');

        Route::post('requests/{request}/assign', [MarketplaceController::class, 'assignRequest'])->name('requests.assign');
    });

    Route::prefix('marketplace/admin')->name('marketplace-admin.')->group(function () {
        Route::get('settings/banks', [SettingsShellController::class, 'marketplaceBankSettings'])->name('settings.banks');
        Route::post('settings/banks/store', [SettingsShellController::class, 'storeBankDetails'])->name('settings.banks.store');
        Route::get('settings/sales', [SettingsShellController::class, 'marketplaceSalesSettings'])->name('settings.sales');
        Route::post('settings/sales/store', [SettingsShellController::class, 'storeSalesDetails'])->name('settings.sales.store');
        Route::get('settings/visits', [SettingsShellController::class, 'marketplaceVisitsSettings'])->name('settings.visits');
        Route::post('settings/visits/store', [SettingsShellController::class, 'storeVisitsDetails'])->name('settings.visits.store');

        Route::get('units', [MarketplaceController::class, 'unitsApi'])->name('units');
        Route::post('units/{marketplaceUnit}/prices-visibility', [MarketplaceController::class, 'updateUnitPricesVisibility'])->name('units.prices-visibility');
        Route::get('visits', [MarketplaceController::class, 'visitsApi'])->name('visits');

        Route::post('offers', [MarketplaceController::class, 'storeOffer'])->name('offers.store');
        Route::put('offers/{marketplaceOffer}', [MarketplaceController::class, 'updateOffer'])->name('offers.update');
        Route::delete('offers/{marketplaceOffer}', [MarketplaceController::class, 'destroyOffer'])->name('offers.destroy');

        Route::post('communities/list/{community}', [MarketplaceController::class, 'listCommunity'])->name('communities.list');
        Route::post('communities/unlist/{community}', [MarketplaceController::class, 'unlistCommunity'])->name('communities.unlist');
        Route::put('communities/{community}/sales-information', [MarketplaceController::class, 'updateCommunitySalesInformation'])->name('communities.sales-information.update');
        Route::post('communities/{community}/sales-information/resend', [MarketplaceController::class, 'resendCommunitySalesInformation'])->name('communities.sales-information.resend');
    });

    Route::prefix('visitor-access')->name('visitor-access.')->group(function () {
        Route::get('/', [VisitorAccessController::class, 'history'])->name('index');
        Route::get('history', [VisitorAccessController::class, 'history'])->name('history');
        Route::get('visitor-details/{marketplaceVisit}', [VisitorAccessController::class, 'details'])->name('details');
        Route::post('{marketplaceVisit}/approve', [VisitorAccessController::class, 'approve'])->name('approve');
        Route::post('{marketplaceVisit}/reject', [VisitorAccessController::class, 'reject'])->name('reject');
    });

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('requires-attention', [DashboardController::class, 'requiresAttention'])->name('requires-attention');
        Route::get('power-bi-reports', [ReportsController::class, 'powerBiReports'])->name('power-bi-reports');
        Route::get('reports', [ReportsController::class, 'reports'])->name('reports');
        Route::get('system-reports', [ReportsController::class, 'systemReports'])->name('system-reports');
        Route::get('system-reports/lease', [ReportsController::class, 'leaseReports'])->name('system-reports.lease');
        Route::get('system-reports/maintenance', [ReportsController::class, 'maintenanceReports'])->name('system-reports.maintenance');
    });

    Route::prefix('report')->name('report.')->group(function () {
        Route::post('load', [ReportsController::class, 'load'])->name('load');
        Route::post('prepare', [ReportsController::class, 'prepare'])->name('prepare');
        Route::post('render', [ReportsController::class, 'renderReport'])->name('render');
        Route::get('pages', [ReportsController::class, 'pages'])->name('pages');
        Route::get('pages/active', [ReportsController::class, 'activePage'])->name('pages.active');
        Route::get('filters', [ReportsController::class, 'filters'])->name('filters');
        Route::get('bookmarks', [ReportsController::class, 'bookmarks'])->name('bookmarks');
        Route::get('settings', [ReportsController::class, 'settings'])->name('settings');
        Route::post('print', [ReportsController::class, 'print'])->name('print');
        Route::post('refresh', [ReportsController::class, 'refresh'])->name('refresh');
        Route::post('save', [ReportsController::class, 'save'])->name('save');
        Route::post('saveAs', [ReportsController::class, 'saveAs'])->name('save-as');
        Route::post('theme', [ReportsController::class, 'theme'])->name('theme');
        Route::post('zoom', [ReportsController::class, 'zoom'])->name('zoom');
    });
});

require __DIR__.'/settings.php';
