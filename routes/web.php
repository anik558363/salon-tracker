<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Employee\ServiceEntryController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ServiceSaleReportController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ProfileSettingsController;
use App\Http\Controllers\Admin\ExpenseCategoryController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Employee\ProfileSettingsController as EmployeeProfileSettings;
use App\Http\Controllers\Admin\EmployeeReportController;
use App\Models\Service;
use App\Models\User;

// পাবলিক হোমপেজ
Route::get('/', function () {
    $services = Service::where('status', true)->get();
    $stylists = User::role('employee')
        ->where('status', true)
        ->latest()
        ->get();

    return view('welcome', compact('services', 'stylists'));
})->name('welcome');

// লগইন পরবর্তী রোল অনুযায়ী রিডাইরেক্ট
Route::get('/dashboard', function () {
    if (auth()->user()->hasRole('super-admin')) {
        return redirect()->route('admin.dashboard');
    }
    // এমপ্লয়ি সরাসরি সার্ভিস এন্ট্রি পেজে যাবে
    return redirect()->route('employee.entries.create');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {

    // সুপার এডমিন প্যানেল
    Route::middleware(['role:super-admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // Employee Routes
        Route::resource('employees', EmployeeController::class)->except(['show', 'destroy']);
        Route::patch('/employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');

        // Service Management Routes
        Route::resource('services', ServiceController::class)->except(['show', 'destroy']);
        Route::patch('/services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');

        // Expense Categories Explicit Routes
        Route::get('/expense-categories', [ExpenseCategoryController::class, 'index'])->name('expense-categories.index');
        Route::get('/expense-categories/create', [ExpenseCategoryController::class, 'create'])->name('expense-categories.create');
        Route::post('/expense-categories', [ExpenseCategoryController::class, 'store'])->name('expense-categories.store');
        Route::get('/expense-categories/{expenseCategory}/edit', [ExpenseCategoryController::class, 'edit'])->name('expense-categories.edit');
        Route::put('/expense-categories/{expenseCategory}', [ExpenseCategoryController::class, 'update'])->name('expense-categories.update');
        Route::delete('/expense-categories/{expenseCategory}', [ExpenseCategoryController::class, 'destroy'])->name('expense-categories.destroy');

        // Expenses Explicit Routes
        Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
        Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
        Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
        Route::get('/expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
        Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
        Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');



        // Reports & Customers
        Route::get('/reports/service-sales', [ServiceSaleReportController::class, 'index'])->name('reports.service-sales');
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');

        // Admin Profile Settings
        Route::get('/settings/profile', [ProfileSettingsController::class, 'edit'])->name('settings.profile');
        Route::put('/settings/profile', [ProfileSettingsController::class, 'updateProfile'])->name('settings.profile.update');
        Route::put('/settings/password', [ProfileSettingsController::class, 'updatePassword'])->name('settings.password.update');



        // Employee Activity & Service Report
        Route::get('/reports/employee-performance', [EmployeeReportController::class, 'index'])->name('reports.employee');
        Route::get('/reports/employee-performance', [EmployeeReportController::class, 'index'])->name('reports.employee');
        Route::post('/reports/employee-salary-pay', [EmployeeReportController::class, 'paySalary'])->name('reports.employee.pay-salary');
    });

    // এমপ্লয়ি প্যানেল (ড্যাশবোর্ড ছাড়া, সরাসরি সার্ভিস এন্ট্রি ও প্রোফাইল সেটিংস)
    Route::middleware(['role:employee'])->prefix('employee')->name('employee.')->group(function () {
        // সার্ভিস এন্ট্রি
        Route::get('/entries/create', [ServiceEntryController::class, 'create'])->name('entries.create');
        Route::post('/entries', [ServiceEntryController::class, 'store'])->name('entries.store');

        // এমপ্লয়ি প্রোফাইল সেটিংস
        Route::get('/settings/profile', [EmployeeProfileSettings::class, 'edit'])->name('settings.profile');
        Route::put('/settings/profile', [EmployeeProfileSettings::class, 'updateProfile'])->name('settings.profile.update');
        Route::put('/settings/password', [EmployeeProfileSettings::class, 'updatePassword'])->name('settings.password.update');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
