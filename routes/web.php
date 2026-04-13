<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

// Test routes for RBAC middleware (only in testing environment)
if (app()->environment('testing')) {
    Route::middleware(['auth', 'permission:view-communities'])
        ->get('/test-permission-communities', fn () => response('OK'));

    Route::middleware(['auth', 'capability:manage-properties'])
        ->get('/test-capability-properties', fn () => response('OK'));
}

require __DIR__.'/settings.php';
