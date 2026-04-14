<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaseApplicationController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\LeasingModuleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\SubLeaseController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UnitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

    // Properties module routes
    Route::get('properties-list/communities', [CommunityController::class, 'index'])
        ->name('properties-list.communities.index');
    Route::get('properties-list/new/community', [CommunityController::class, 'create'])
        ->name('properties-list.communities.create');
    Route::get('properties-list/communities/community/details/{community}', [CommunityController::class, 'show'])
        ->name('properties-list.communities.show');
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
    Route::resource('contacts', ContactController::class);

    // Service Requests module routes
    Route::resource('service-requests', ServiceRequestController::class);

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
    Route::get('transactions/money-in', function (Request $request, TransactionController $controller) {
        $request->query->set('filter_type', 'money_in');

        return $controller->index($request);
    })->name('transactions.money-in');
    Route::get('transactions/money-out', function (Request $request, TransactionController $controller) {
        $request->query->set('filter_type', 'money_out');

        return $controller->index($request);
    })->name('transactions.money-out');
    Route::get('accounting', [TransactionController::class, 'index'])->name('accounting.index');

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
    Route::get('leasing/leases/{lease}', [LeaseController::class, 'show'])->name('leasing.leases.show');
    Route::get('leasing/leases/{lease}/renew', [LeaseController::class, 'renewForm'])->name('leasing.leases.renew');
    Route::get('leasing/leases/renew/{lease}', [LeaseController::class, 'renewForm'])->name('leasing.leases.renew.legacy');
    Route::get('leasing/details/{lease}', [LeaseController::class, 'show'])->name('leasing.details.show');

    // Reports module routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/leases', [ReportController::class, 'leases'])->name('leases');
        Route::get('/maintenance', [ReportController::class, 'maintenance'])->name('maintenance');
    });

    // Notifications module routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
    });

    // Announcements module routes
    Route::resource('announcements', AnnouncementController::class);
    Route::post('announcements/{announcement}/publish', [AnnouncementController::class, 'publish'])->name('announcements.publish');
    Route::post('announcements/{announcement}/cancel', [AnnouncementController::class, 'cancel'])->name('announcements.cancel');

    // Directory module routes
    Route::get('directory', [AnnouncementController::class, 'directory'])->name('directory.index');
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
