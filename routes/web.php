<?php

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MessDashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\WeeklyMenuController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/', [MessDashboardController::class, 'index'])->name('dashboard');

Route::middleware(['auth', 'mess.active'])->group(function () {
    Route::get('/mess/settings', [MessDashboardController::class, 'settings'])->name('mess.settings');
    Route::put('/mess/settings', [MessDashboardController::class, 'updateSettings'])->name('mess.settings.update');
    Route::get('/menus/weekly', [WeeklyMenuController::class, 'edit'])->name('menus.weekly');
    Route::put('/menus/weekly', [WeeklyMenuController::class, 'update'])->name('menus.weekly.update');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/expenses', [ReportController::class, 'expenses'])->name('reports.expenses');

    Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');
    Route::post('/members', [MemberController::class, 'store'])->name('members.store');
    Route::get('/members/{member}/edit', [MemberController::class, 'edit'])->name('members.edit');
    Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');
    Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');
    Route::post('/members/{member}/restore', [MemberController::class, 'restore'])->name('members.restore');
    Route::get('/members/trash', [MemberController::class, 'trash'])->name('members.trash');

    Route::get('/meals/create', [MealController::class, 'create'])->name('meals.create');
    Route::post('/meals', [MealController::class, 'store'])->name('meals.store');
    Route::get('/meals/bulk/create', [MealController::class, 'createBulk'])->name('meals.bulk.create');
    Route::post('/meals/bulk', [MealController::class, 'storeBulk'])->name('meals.bulk.store');

    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
    Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::post('/expenses/{expense}/restore', [ExpenseController::class, 'restore'])->name('expenses.restore');

    Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/account/password/edit', [PasswordController::class, 'editOwn'])->name('account.password.edit');
    Route::put('/account/password', [PasswordController::class, 'updateOwn'])->name('account.password.update');
    
});

Route::middleware('auth')->group(function () {
    Route::post('/stop-impersonating', [\App\Http\Controllers\SuperAdminController::class, 'stopImpersonating'])->name('stop.impersonating');
});

Route::middleware(['auth', 'superadmin'])->group(function () {
    Route::get('/superadmin', [\App\Http\Controllers\SuperAdminController::class, 'index'])->name('superadmin.dashboard');
    Route::get('/superadmin/site-settings', [\App\Http\Controllers\SuperAdminController::class, 'siteSettings'])->name('superadmin.site.settings');
    Route::put('/superadmin/site-settings', [\App\Http\Controllers\SuperAdminController::class, 'updateSiteSettings'])->name('superadmin.site.settings.update');
    Route::get('/superadmin/messes/create', [\App\Http\Controllers\SuperAdminController::class, 'createMess'])->name('superadmin.messes.create');
    Route::post('/superadmin/messes', [\App\Http\Controllers\SuperAdminController::class, 'storeMess'])->name('superadmin.messes.store');
    Route::get('/superadmin/messes/{mess}/edit', [\App\Http\Controllers\SuperAdminController::class, 'editMess'])->name('superadmin.messes.edit');
    Route::put('/superadmin/messes/{mess}', [\App\Http\Controllers\SuperAdminController::class, 'updateMess'])->name('superadmin.messes.update');
    Route::post('/superadmin/impersonate/{mess_id}', [\App\Http\Controllers\SuperAdminController::class, 'impersonate'])->name('superadmin.impersonate');
    Route::post('/superadmin/messes/{mess}/deactivate', [\App\Http\Controllers\SuperAdminController::class, 'deactivateMess'])->name('superadmin.messes.deactivate');
    Route::post('/superadmin/messes/{mess}/activate', [\App\Http\Controllers\SuperAdminController::class, 'activateMess'])->name('superadmin.messes.activate');
    Route::get('/superadmin/activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('superadmin.activity.logs');
    Route::post('/superadmin/activity-logs/{activityLog}/restore', [\App\Http\Controllers\ActivityLogController::class, 'restore'])->name('superadmin.activity.logs.restore');
    Route::get('/superadmin/warnings', [\App\Http\Controllers\SuperAdminController::class, 'warnings'])->name('superadmin.warnings');
    Route::get('/superadmin/users/{user}/password/edit', [PasswordController::class, 'editForUser'])->name('superadmin.users.password.edit');
    Route::put('/superadmin/users/{user}/password', [PasswordController::class, 'updateForUser'])->name('superadmin.users.password.update');
});
