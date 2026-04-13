<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaseApplicationController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\UnitController;
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
    Route::resource('communities', CommunityController::class);
    Route::resource('buildings', BuildingController::class);
    Route::resource('units', UnitController::class);

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
