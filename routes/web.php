<?php

use App\Http\Controllers\Accounting\TransactionCategoryController;
use App\Http\Controllers\Accounting\TransactionController;
use App\Http\Controllers\Admin\AccountSubscriptionController;
use App\Http\Controllers\Admin\AccountUserController;
use App\Http\Controllers\Admin\DocumentTemplateController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserRoleAssignmentController;
use App\Http\Controllers\AppSettings\CompanyProfileController;
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
use App\Http\Controllers\Leasing\QuoteController;
use App\Http\Controllers\Marketplace\MarketplaceController;
use App\Http\Controllers\Properties\BuildingController;
use App\Http\Controllers\Properties\CommunityController;
use App\Http\Controllers\Properties\UnitController;
use App\Http\Controllers\Reports\ReportsController;
use App\Http\Controllers\Requests\ServiceRequestController;
use App\Http\Controllers\Shared\LegacyCompatibilityController;
use App\Http\Controllers\Shared\LookupController;
use App\Http\Controllers\Shared\NotificationController;
use App\Http\Controllers\VisitorAccess\VisitorAccessController;
use App\Http\Controllers\VisitorAccess\VisitorInvitationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
})->name('home');

Route::controller(LegacyCompatibilityController::class)
    ->name('legacy.')
    ->group(function () {
        Route::get('api/general/static-files/download_land_excel', 'downloadLandExcel')->name('download-land-excel');
        Route::get('api/general/static-files/download_lead_excel', 'downloadLeadExcel')->name('download-lead-excel');

        Route::get('cities/all', 'citiesAll')->name('cities.all');
        Route::get('cities/{country_code}', 'citiesByCountryCode')->name('cities.by-country')->whereAlpha('country_code');

        Route::get('countries', 'countries')->name('countries');

        Route::get('districts/all', 'districtsAll')->name('districts.all');
        Route::get('districts/{city_id}', 'districtsByCityId')->name('districts.by-city')->whereNumber('city_id');

        Route::get('integrations/powerbi/types', 'powerBiTypes')->name('integrations.powerbi.types');
        Route::get('me', 'me')->name('me');
        Route::get('plans', 'plans')->name('plans');
        Route::get('request-category', 'requestCategory')->name('request-category');

        Route::post('images/multiple', 'imagesMultiple')->name('images.multiple');
        Route::post('signup/create-tenant', 'signupCreateTenant')->name('signup.create-tenant');
        Route::post('signup/send-verification', 'signupSendVerification')->name('signup.send-verification');
        Route::post('signup/verify', 'signupVerify')->name('signup.verify');

        Route::post('tenancy/login', 'tenancyLogin')->name('tenancy.login');
        Route::post('tenancy/logout', 'tenancyLogout')->name('tenancy.logout');
        Route::post('tenancy/send-verification', 'tenancySendVerification')->name('tenancy.send-verification');
    });

Route::middleware(['auth', 'verified', 'tenant'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    // Properties
    Route::resource('communities', CommunityController::class);
    Route::resource('buildings', BuildingController::class);
    Route::resource('units', UnitController::class);

    // Leasing — Lease Quotes (registered before leases resource to avoid {lease} catch-all conflict)
    Route::resource('leases/quotes', QuoteController::class)->only(['index', 'create', 'store', 'show'])->names('quotes');
    Route::post('leases/quotes/{quote}/send', [QuoteController::class, 'send'])->name('quotes.send');

    // Leasing — Leases
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

    Route::prefix('accounting')->name('accounting.')->group(function () {
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('categories', [TransactionCategoryController::class, 'index'])->name('categories.index');
            Route::post('categories', [TransactionCategoryController::class, 'store'])->name('categories.store');
            Route::put('categories/{setting}', [TransactionCategoryController::class, 'update'])->name('categories.update');
            Route::post('categories/{setting}/toggle', [TransactionCategoryController::class, 'toggleActive'])->name('categories.toggle');
            Route::delete('categories/{setting}', [TransactionCategoryController::class, 'destroy'])->name('categories.destroy');
        });
    });

    // Communication
    Route::resource('announcements', AnnouncementController::class);

    // Contacts
    Route::resource('owners', OwnerController::class);
    Route::get('residents/duplicate-check', [ResidentController::class, 'duplicateCheck'])->name('residents.duplicate-check');
    Route::resource('residents', ResidentController::class);
    Route::resource('admins', AdminController::class);
    Route::resource('professionals', ProfessionalController::class);

    // Account Management
    Route::prefix('admin')->name('admin.')->middleware('admin.manage')->group(function () {
        Route::get('users', [AccountUserController::class, 'index'])->name('users.index');
        Route::post('users', [AccountUserController::class, 'store'])->name('users.store');
        Route::get('users/{user}', [AccountUserController::class, 'show'])->name('users.show');
        Route::put('users/{membership}', [AccountUserController::class, 'update'])->name('users.update');
        Route::delete('users/{membership}', [AccountUserController::class, 'destroy'])->name('users.destroy');

        Route::post('users/{user}/role-assignments', [UserRoleAssignmentController::class, 'store'])->name('users.role-assignments.store');
        Route::delete('users/{user}/role-assignments/{assignment}', [UserRoleAssignmentController::class, 'destroy'])->name('users.role-assignments.destroy');

        Route::get('subscriptions', [AccountSubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::post('subscriptions/{tenant}/activate', [AccountSubscriptionController::class, 'activate'])->name('subscriptions.activate');
        Route::post('subscriptions/{tenant}/cancel', [AccountSubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
        Route::post('subscriptions/{tenant}/cancel-now', [AccountSubscriptionController::class, 'cancelNow'])->name('subscriptions.cancel-now');

        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
        Route::put('roles/{role}/permissions', [RoleController::class, 'syncPermissions'])->name('roles.permissions.sync');

        Route::get('documents', [DocumentTemplateController::class, 'index'])->name('documents.index');
        Route::post('documents', [DocumentTemplateController::class, 'store'])->name('documents.store');
        Route::get('documents/{documentTemplate}', [DocumentTemplateController::class, 'show'])->name('documents.show');
        Route::put('documents/{documentTemplate}', [DocumentTemplateController::class, 'update'])->name('documents.update');
        Route::delete('documents/{documentTemplate}', [DocumentTemplateController::class, 'destroy'])->name('documents.destroy');
        Route::post('documents/{documentTemplate}/activate', [DocumentTemplateController::class, 'activate'])->name('documents.activate');
        Route::post('documents/{documentTemplate}/archive', [DocumentTemplateController::class, 'archive'])->name('documents.archive');
        Route::post('documents/{documentTemplate}/preview', [DocumentTemplateController::class, 'preview'])->name('documents.preview');
    });

    // App Settings
    Route::prefix('app-settings')->name('app-settings.')->group(function () {
        Route::resource('request-categories', RequestCategoryController::class)->except(['show']);
        Route::resource('request-categories.subcategories', RequestSubcategoryController::class)->only(['store', 'update', 'destroy']);
        Route::post('service-settings', [ServiceSettingController::class, 'updateOrCreate'])->name('service-settings.update-or-create');
        Route::resource('facility-categories', AppFacilityCategoryController::class)->except(['show']);
        Route::get('invoice', [InvoiceSettingController::class, 'edit'])->name('invoice.edit');
        Route::put('invoice', [InvoiceSettingController::class, 'update'])->name('invoice.update');
        Route::get('company-profile', [CompanyProfileController::class, 'edit'])->name('company-profile.edit');
        Route::put('company-profile', [CompanyProfileController::class, 'update'])->name('company-profile.update');
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

    Route::get('invoice-settings', [InvoiceSettingController::class, 'showApi'])->name('invoice-settings.show');
    Route::post('invoice-settings', [InvoiceSettingController::class, 'storeApi'])->name('invoice-settings.store');
    Route::put('invoice-settings', [InvoiceSettingController::class, 'updateApi'])->name('invoice-settings.update');

    Route::get('reports/expenses', [ReportsController::class, 'expenses'])->name('reports.expenses');
    Route::get('reports/income', [ReportsController::class, 'income'])->name('reports.income');
    Route::get('reports/performance/units', [ReportsController::class, 'performanceUnits'])->name('reports.performance.units');

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
        Route::get('leads/create', [LookupController::class, 'leadCreate'])->name('leads.create');
        Route::get('countries', [LookupController::class, 'countries'])->name('countries');
        Route::get('company_profile', [LookupController::class, 'companyProfile'])->name('company-profile');
        Route::get('contacts/statistics', [LookupController::class, 'contactsStatistics'])->name('contacts.statistics');
        Route::get('admins', [AdminController::class, 'index'])->name('admins.index');
        Route::post('admins', [AdminController::class, 'store'])->name('admins.store');
        Route::put('admins/{admin}', [AdminController::class, 'update'])->name('admins.update');
        Route::post('admins/check-validate', [AdminController::class, 'checkValidate'])->name('admins.check-validate');
        Route::get('admins/manager-roles', [AdminController::class, 'managerRoles'])->name('admins.manager-roles');
        Route::get('admins/{admin}', [AdminController::class, 'show'])->name('admins.show');
        Route::get('announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::put('announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
        Route::get('buildings', [BuildingController::class, 'index'])->name('buildings.index');
        Route::post('buildings', [BuildingController::class, 'store'])->name('buildings.store');
        Route::put('buildings/{building}', [BuildingController::class, 'update'])->name('buildings.update');
        Route::get('buildings/{building}', [BuildingController::class, 'show'])->name('buildings.show');
        Route::get('communities/edaat-product-codes', [CommunityController::class, 'edaatProductCodes'])->name('communities.edaat-product-codes');
        Route::get('communities/edaat/product-codes', [CommunityController::class, 'edaatProductCodes'])->name('communities.edaat.product-codes');
        Route::get('communities/off-plan-sale', [CommunityController::class, 'offPlanSale'])->name('communities.off-plan-sale');
        Route::get('communities', [CommunityController::class, 'index'])->name('communities.index');
        Route::post('communities', [CommunityController::class, 'store'])->name('communities.store');
        Route::put('communities/{community}', [CommunityController::class, 'update'])->name('communities.update');
        Route::get('communities/{community}', [CommunityController::class, 'show'])->name('communities.show');
        Route::get('facilities', [FacilityController::class, 'index'])->name('facilities.index');
        Route::post('facilities', [FacilityController::class, 'store'])->name('facilities.store');
        Route::put('facilities/{facility}', [FacilityController::class, 'update'])->name('facilities.update');
        Route::get('leases', [LeaseController::class, 'index'])->name('leases.index');
        Route::get('leases/create', [LeaseController::class, 'create'])->name('leases.create');
        Route::post('leases', [LeaseController::class, 'store'])->name('leases.store');
        Route::put('leases/{lease}', [LeaseController::class, 'update'])->name('leases.update');
        Route::post('leases/create', [LeaseController::class, 'storeFromCreateAlias'])->name('leases.create.store');
        Route::post('leases/step-four', [LeaseController::class, 'stepFour'])->name('leases.step-four');
        Route::post('leases/renew/store', [LeaseController::class, 'renewStore'])->name('leases.renew.store');
        Route::post('leases/change-status/move-out', [LeaseController::class, 'changeStatusMoveOut'])->name('leases.change-status.move-out');
        Route::post('leases/change-status/reactivate', [LeaseController::class, 'changeStatusReactivate'])->name('leases.change-status.reactivate');
        Route::post('leases/change-status/suspend', [LeaseController::class, 'changeStatusSuspend'])->name('leases.change-status.suspend');
        Route::post('leases/change-status/terminate', [LeaseController::class, 'changeStatusTerminate'])->name('leases.change-status.terminate');
        Route::post('leases/{lease}/addendum', [LeaseController::class, 'addendum'])->name('leases.addendum');
        Route::get('leases/expiring', [LeaseController::class, 'expiring'])->name('leases.expiring');
        Route::get('leases/statistics', [LeaseController::class, 'statistics'])->name('leases.statistics');
        Route::get('leases/{lease}', [LeaseController::class, 'show'])->name('leases.show');
        Route::get('sub-leases', [LeaseController::class, 'subLeases'])->name('sub-leases.index');
        Route::post('sub-leases', [LeaseController::class, 'storeSubleaseAlias'])->name('sub-leases.store');
        Route::get('tenants', [ResidentController::class, 'index'])->name('tenants.index');
        Route::post('tenants', [ResidentController::class, 'store'])->name('tenants.store');
        Route::post('tenants/{resident}/family-members', [ResidentController::class, 'storeFamilyMember'])->name('tenants.family-members.store');
        Route::put('tenants/{resident}', [ResidentController::class, 'update'])->name('tenants.update');
        Route::get('tenants/{resident}', [ResidentController::class, 'rfShow'])->name('tenants.show');
        Route::get('owners', [OwnerController::class, 'index'])->name('owners.index');
        Route::post('owners', [OwnerController::class, 'store'])->name('owners.store');
        Route::put('owners/{owner}', [OwnerController::class, 'update'])->name('owners.update');
        Route::get('owners/{owner}', [OwnerController::class, 'show'])->name('owners.show');
        Route::post('leads', [LookupController::class, 'storeLead'])->name('leads.store');
        Route::get('payment-schedules', [LeaseController::class, 'paymentSchedules'])->name('payment-schedules');
        Route::get('professionals', [ProfessionalController::class, 'index'])->name('professionals.index');
        Route::post('professionals', [ProfessionalController::class, 'store'])->name('professionals.store');
        Route::get('rental-contract-types', [LeaseController::class, 'rentalContractTypes'])->name('rental-contract-types');
        Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::post('transactions', [TransactionController::class, 'store'])->name('transactions.store');
        Route::put('transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
        Route::get('transactions/{transaction}', [TransactionController::class, 'rfShow'])->name('transactions.show');
        Route::get('units', [UnitController::class, 'rfIndex'])->name('units.index');
        Route::post('units', [UnitController::class, 'store'])->name('units.store');
        Route::put('units/{unit}', [UnitController::class, 'update'])->name('units.update');
        Route::post('units/bulk-delete', [UnitController::class, 'bulkDelete'])->name('units.bulk-delete');
        Route::post('units/bulk-update', [UnitController::class, 'bulkUpdate'])->name('units.bulk-update');
        Route::get('units/create', [UnitController::class, 'rfCreate'])->name('units.create');
        Route::get('units/{unit}', [UnitController::class, 'rfShow'])->name('units.show');
        Route::get('requests', [ServiceRequestController::class, 'index'])->name('requests.index');
        Route::post('requests', [ServiceRequestController::class, 'store'])->name('requests.store');
        Route::put('requests/{serviceRequest}', [ServiceRequestController::class, 'update'])->name('requests.update');
        Route::post('requests/change-status/approved', [ServiceRequestController::class, 'changeStatusApproved'])->name('requests.change-status.approved');
        Route::post('requests/change-status/canceled', [ServiceRequestController::class, 'changeStatusCanceled'])->name('requests.change-status.canceled');
        Route::post('requests/change-status/completed', [ServiceRequestController::class, 'changeStatusCompleted'])->name('requests.change-status.completed');
        Route::post('requests/change-status/in-progress', [ServiceRequestController::class, 'changeStatusInProgress'])->name('requests.change-status.in-progress');
        Route::post('requests/change-status/pending', [ServiceRequestController::class, 'changeStatusPending'])->name('requests.change-status.pending');
        Route::post('requests/change-status/rejected', [ServiceRequestController::class, 'changeStatusRejected'])->name('requests.change-status.rejected');
        Route::post('requests/{serviceRequest}/assign', [ServiceRequestController::class, 'assign'])->name('requests.assign');
        Route::post('requests/{serviceRequest}/reassign', [ServiceRequestController::class, 'reassign'])->name('requests.reassign');
        Route::get('users/requests', [ServiceRequestController::class, 'index'])->name('users.requests.index');
        Route::get('users/requests/categories', [RequestCategoryController::class, 'index'])->name('users.requests.categories');
        Route::get('users/requests/types', [RequestSubcategoryController::class, 'typesIndex'])->name('users.requests.types');
        Route::get('users/visitor-access', [VisitorAccessController::class, 'rfIndex'])->name('users.visitor-access');
        Route::get('requests/categories', [RequestCategoryController::class, 'index'])->name('requests.categories.index');
        Route::post('requests/categories', [RequestCategoryController::class, 'store'])->name('requests.categories.store');
        Route::put('requests/categories/{requestCategory}', [RequestCategoryController::class, 'update'])->name('requests.categories.update');
        Route::get('requests/categories/{requestCategory}', [RequestCategoryController::class, 'show'])->name('requests.categories.show');
        Route::get('requests/sub-categories', [RequestSubcategoryController::class, 'index'])->name('requests.sub-categories.index');
        Route::post('requests/sub-categories', [RequestSubcategoryController::class, 'storeRf'])->name('requests.sub-categories.store');
        Route::put('requests/sub-categories/{requestSubcategory}', [RequestSubcategoryController::class, 'updateRf'])->name('requests.sub-categories.update');
        Route::get('requests/sub-categories/{requestSubcategory}', [RequestSubcategoryController::class, 'show'])->name('requests.sub-categories.show');
        Route::get('requests/types', [RequestSubcategoryController::class, 'typesIndex'])->name('requests.types.index');
        Route::get('requests/types/create', [RequestSubcategoryController::class, 'typesCreate'])->name('requests.types.create');
        Route::post('requests/types/create', [RequestSubcategoryController::class, 'storeType'])->name('requests.types.create.store');
        Route::get('requests/types/list/{requestSubcategory}', [RequestSubcategoryController::class, 'typesList'])->name('requests.types.list');
        Route::put('requests/types/{requestSubcategory}', [RequestSubcategoryController::class, 'updateTypeRf'])->name('requests.types.update');
        Route::get('requests/types/{requestSubcategory}', [RequestSubcategoryController::class, 'typesShow'])->name('requests.types.show');
        Route::get('requests/service-settings', [ServiceSettingController::class, 'index'])->name('requests.service-settings.index');
        Route::post('requests/service-settings/updateOrCreate', [ServiceSettingController::class, 'updateOrCreate'])->name('requests.service-settings.update-or-create');
        Route::get('requests/service-settings/{serviceSetting}', [ServiceSettingController::class, 'show'])->name('requests.service-settings.show');
        Route::get('invoices', [TransactionController::class, 'index'])->name('invoices.index');

        Route::delete('requests/categories/{requestCategory}', [RequestCategoryController::class, 'destroy'])->name('requests.categories.destroy');
        Route::delete('requests/service-settings/{serviceSetting}', [ServiceSettingController::class, 'destroy'])->name('requests.service-settings.destroy');
        Route::delete('requests/types/{requestSubcategory}', [RequestSubcategoryController::class, 'destroyType'])->name('requests.types.destroy');
        Route::delete('sub-leases/{lease}', [LeaseController::class, 'destroySublease'])->name('sub-leases.destroy');
        Route::delete('tenants/{resident}/family-members/{dependent}', [ResidentController::class, 'destroyFamilyMember'])->name('tenants.family-members.destroy');
        Route::delete('tenants/{resident}', [ResidentController::class, 'destroy'])->name('tenants.destroy');
        Route::delete('transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
        Route::delete('units/{unit}', [UnitController::class, 'destroy'])->name('units.destroy');

        Route::delete('admins/{admin}', [AdminController::class, 'destroy'])->name('admins.destroy');
        Route::delete('announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
        Route::delete('buildings/{building}', [BuildingController::class, 'destroy'])->name('buildings.destroy');
        Route::delete('communities/{community}', [CommunityController::class, 'destroy'])->name('communities.destroy');
        Route::delete('facilities/{facility}', [FacilityController::class, 'destroy'])->name('facilities.destroy');
        Route::delete('leases/{lease}', [LeaseController::class, 'destroy'])->name('leases.destroy');
        Route::delete('owners/{owner}', [OwnerController::class, 'destroy'])->name('owners.destroy');
        Route::delete('professionals/{professional}', [ProfessionalController::class, 'destroy'])->name('professionals.destroy');
        Route::delete('requests/{serviceRequest}', [ServiceRequestController::class, 'destroy'])->name('requests.destroy');

        Route::post('files', [FileController::class, 'store'])->name('files.store');
        Route::delete('files/{media}', [FileController::class, 'destroy'])->name('files.destroy');

        Route::get('excel-sheets', [ExcelSheetController::class, 'index'])->name('excel-sheets.index');
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
        Route::put('settings/banks/{systemSetting}', [SettingsShellController::class, 'updateMarketplaceBankSetting'])->name('settings.banks.update');
        Route::delete('settings/banks/{systemSetting}', [SettingsShellController::class, 'destroyMarketplaceBankSetting'])->name('settings.banks.destroy');
        Route::get('settings/sales', [SettingsShellController::class, 'marketplaceSalesSettings'])->name('settings.sales');
        Route::post('settings/sales/store', [SettingsShellController::class, 'storeSalesDetails'])->name('settings.sales.store');
        Route::get('settings/visits', [SettingsShellController::class, 'marketplaceVisitsSettings'])->name('settings.visits');
        Route::post('settings/visits/store', [SettingsShellController::class, 'storeVisitsDetails'])->name('settings.visits.store');

        Route::get('units', [MarketplaceController::class, 'unitsApi'])->name('units');
        Route::post('units/{marketplaceUnit}/prices-visibility', [MarketplaceController::class, 'updateUnitPricesVisibility'])->name('units.prices-visibility');
        Route::post('units/prices-visibility/{marketplaceUnit}', [MarketplaceController::class, 'updateUnitPricesVisibility'])->name('units.prices-visibility.legacy');
        Route::post('listings', [MarketplaceController::class, 'storeListing'])->name('listings.store');
        Route::put('listings/{marketplaceUnit}', [MarketplaceController::class, 'updateListing'])->name('listings.update');
        Route::delete('listings/{marketplaceUnit}', [MarketplaceController::class, 'destroyListing'])->name('listings.destroy');
        Route::get('visits', [MarketplaceController::class, 'visitsApi'])->name('visits');
        Route::post('visits/cancel/{marketplaceVisit}', [MarketplaceController::class, 'cancelVisit'])->name('visits.cancel.legacy');
        Route::get('communities', [MarketplaceController::class, 'communitiesApi'])->name('communities.index');
        Route::get('communities/list', [MarketplaceController::class, 'listedCommunitiesApi'])->name('communities.list-index');

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

        // Resident visitor invitation CRUD
        Route::prefix('invitations')->name('invitations.')->group(function () {
            Route::get('/', [VisitorInvitationController::class, 'index'])->name('index');
            Route::get('create', [VisitorInvitationController::class, 'create'])->name('create');
            Route::post('/', [VisitorInvitationController::class, 'store'])->name('store');
            Route::get('{visitorInvitation}', [VisitorInvitationController::class, 'show'])->name('show');
            Route::post('{visitorInvitation}/cancel', [VisitorInvitationController::class, 'cancel'])->name('cancel');
        });
    });

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('requires-attention', [DashboardController::class, 'requiresAttention'])->name('requires-attention');
        Route::get('statistics', [DashboardController::class, 'statistics'])->name('statistics');
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

// Public lease quote preview — no auth required. Prospect opens link from email.
Route::get('quotes/{token}', [QuoteController::class, 'preview'])->name('quotes.preview');

require __DIR__.'/settings.php';
