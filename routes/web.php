<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboard;
use App\Http\Controllers\Employee\ServiceEntryController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ServiceSaleReportController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ProfileSettingsController;
use App\Http\Controllers\Employee\ProfileSettingsController as EmployeeProfileSettings;


use App\Models\Service;

Route::get('/', function () {
   
    $services = Service::where('status', true)->get();

    return view('welcome', compact('services'));
})->name('welcome');



// রোল অনুযায়ী ড্যাশবোর্ড রিডাইরেক্ট রাউট
Route::get('/dashboard', function () {
    if (auth()->user()->hasRole('super-admin')) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('employee.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {



    Route::middleware(['role:super-admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // Employee Routes
        Route::resource('employees', EmployeeController::class)->except(['show', 'destroy']);
        Route::patch('/employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');

        // Service Management Routes
        Route::resource('services', ServiceController::class)->except(['show', 'destroy']);
        Route::patch('/services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');

        // Service Sales Analytics Route
        Route::get('/reports/service-sales', [ServiceSaleReportController::class, 'index'])->name('reports.service-sales');

        // Customer Directory Route
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');

        // Admin Profile & Security Settings
        Route::get('/settings/profile', [ProfileSettingsController::class, 'edit'])->name('settings.profile');
        Route::put('/settings/profile', [ProfileSettingsController::class, 'updateProfile'])->name('settings.profile.update');
        Route::put('/settings/password', [ProfileSettingsController::class, 'updatePassword'])->name('settings.password.update');
    });

    // এমপ্লয়ি প্যানেল
    Route::middleware(['role:employee'])->prefix('employee')->name('employee.')->group(function () {
        Route::get('/dashboard', [EmployeeDashboard::class, 'index'])->name('dashboard');
        Route::get('/entries/create', [ServiceEntryController::class, 'create'])->name('entries.create');
        Route::post('/entries', [ServiceEntryController::class, 'store'])->name('entries.store');

        // Employee Profile & Security Routes
        Route::get('/settings/profile', [EmployeeProfileSettings::class, 'edit'])->name('settings.profile');
        Route::put('/settings/profile', [EmployeeProfileSettings::class, 'updateProfile'])->name('settings.profile.update');
        Route::put('/settings/password', [EmployeeProfileSettings::class, 'updatePassword'])->name('settings.password.update');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
