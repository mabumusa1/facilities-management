<?php

use App\Http\Controllers\Accounting\TransactionController;
use App\Http\Controllers\Communication\AnnouncementController;
use App\Http\Controllers\Contacts\AdminController;
use App\Http\Controllers\Contacts\OwnerController;
use App\Http\Controllers\Contacts\ProfessionalController;
use App\Http\Controllers\Contacts\ResidentController;
use App\Http\Controllers\Facilities\FacilityBookingController;
use App\Http\Controllers\Facilities\FacilityController;
use App\Http\Controllers\Leasing\LeaseController;
use App\Http\Controllers\Properties\BuildingController;
use App\Http\Controllers\Properties\CommunityController;
use App\Http\Controllers\Properties\UnitController;
use App\Http\Controllers\Requests\ServiceRequestController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified', 'tenant'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    // Properties
    Route::resource('communities', CommunityController::class);
    Route::resource('buildings', BuildingController::class);
    Route::resource('units', UnitController::class);

    // Leasing
    Route::resource('leases', LeaseController::class);

    // Requests
    Route::resource('requests', ServiceRequestController::class)->parameters([
        'requests' => 'serviceRequest',
    ]);

    // Facilities
    Route::resource('facilities', FacilityController::class);
    Route::resource('facility-bookings', FacilityBookingController::class)->only([
        'index', 'show', 'update', 'destroy',
    ]);

    // Accounting
    Route::resource('transactions', TransactionController::class);

    // Communication
    Route::resource('announcements', AnnouncementController::class);

    // Contacts
    Route::resource('owners', OwnerController::class);
    Route::resource('residents', ResidentController::class);
    Route::resource('admins', AdminController::class);
    Route::resource('professionals', ProfessionalController::class);
});

require __DIR__.'/settings.php';
