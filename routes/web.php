<?php

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MessDashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [MessDashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

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

    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');

    Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    
    // Stop impersonating (accessible to any authenticated user who has the session key)
    Route::post('/stop-impersonating', [\App\Http\Controllers\SuperAdminController::class, 'stopImpersonating'])->name('stop.impersonating');
});

Route::middleware(['auth', 'superadmin'])->group(function () {
    Route::get('/superadmin', [\App\Http\Controllers\SuperAdminController::class, 'index'])->name('superadmin.dashboard');
    Route::get('/superadmin/messes/create', [\App\Http\Controllers\SuperAdminController::class, 'createMess'])->name('superadmin.messes.create');
    Route::post('/superadmin/messes', [\App\Http\Controllers\SuperAdminController::class, 'storeMess'])->name('superadmin.messes.store');
    Route::get('/superadmin/messes/{mess}/edit', [\App\Http\Controllers\SuperAdminController::class, 'editMess'])->name('superadmin.messes.edit');
    Route::put('/superadmin/messes/{mess}', [\App\Http\Controllers\SuperAdminController::class, 'updateMess'])->name('superadmin.messes.update');
    Route::post('/superadmin/impersonate/{mess_id}', [\App\Http\Controllers\SuperAdminController::class, 'impersonate'])->name('superadmin.impersonate');
    Route::delete('/superadmin/messes/{mess_id}', [\App\Http\Controllers\SuperAdminController::class, 'deleteMess'])->name('superadmin.messes.delete');
    Route::get('/superadmin/activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('superadmin.activity.logs');
    Route::get('/superadmin/warnings', [\App\Http\Controllers\SuperAdminController::class, 'warnings'])->name('superadmin.warnings');
});
