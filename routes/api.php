<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

Route::middleware(['web', 'auth'])->group(function () {
    // Dashboard API routes
    Route::prefix('dashboard')->name('api.dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'data'])->name('data');
        Route::get('/requires-attention', [DashboardController::class, 'requiresAttention'])->name('requires-attention');
        Route::get('/units', [DashboardController::class, 'units'])->name('units');
        Route::get('/leases', [DashboardController::class, 'leases'])->name('leases');
        Route::get('/service-requests', [DashboardController::class, 'serviceRequests'])->name('service-requests');
        Route::get('/marketplace', [DashboardController::class, 'marketplace'])->name('marketplace');
        Route::get('/financials', [DashboardController::class, 'financials'])->name('financials');
        Route::get('/facilities', [DashboardController::class, 'facilities'])->name('facilities');
        Route::get('/visitors', [DashboardController::class, 'visitors'])->name('visitors');
    });

    // Reports API routes
    Route::prefix('reports')->name('api.reports.')->group(function () {
        // Lease reports
        Route::get('/leases/statistics', [ReportController::class, 'leaseStatistics'])->name('leases.statistics');
        Route::get('/leases/by-status', [ReportController::class, 'leasesByStatus'])->name('leases.by-status');
        Route::get('/leases/expiring', [ReportController::class, 'expiringLeases'])->name('leases.expiring');
        Route::get('/leases/rent-collection', [ReportController::class, 'rentCollection'])->name('leases.rent-collection');

        // Maintenance reports
        Route::get('/maintenance/statistics', [ReportController::class, 'maintenanceStatistics'])->name('maintenance.statistics');
        Route::get('/maintenance/by-category', [ReportController::class, 'maintenanceByCategory'])->name('maintenance.by-category');
        Route::get('/maintenance/by-priority', [ReportController::class, 'maintenanceByPriority'])->name('maintenance.by-priority');
        Route::get('/maintenance/trend', [ReportController::class, 'maintenanceTrend'])->name('maintenance.trend');

        // Occupancy reports
        Route::get('/occupancy', [ReportController::class, 'occupancy'])->name('occupancy');
    });
});
