<?php

use App\Http\Controllers\Purchasing\PurchaseRequestController;
use App\Http\Controllers\Utility\AuditTrailController;
use App\Http\Controllers\Utility\AuthController;
use App\Http\Controllers\Utility\MonitoringController;
use App\Http\Controllers\Utility\RolePermissionController;
use App\Http\Controllers\Utility\UserController;
use App\Http\Controllers\Utility\WarehouseController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth', 'check_password_changed'])->group(function () {
    Route::get('/dashboard', fn() => inertia('Utility/Dashboard'))->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/change-password', [UserController::class, 'showChangePasswordForm'])->name('user.change-password');
    Route::post('/change-password', [UserController::class, 'changePassword'])->name('user.change-password.post');
    
    Route::get('/settings', [UserController::class, 'showSettingForm'])->name('user.settings');
    Route::post('/settings', [UserController::class, 'updateSetting'])->name('user.settings.post');
});

// Auth Utility Routes
Route::middleware('auth')->prefix('auth')->name('auth.')->group(function () {
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change-password');
    Route::post('/is-password-changed', [AuthController::class, 'isPasswordChanged'])->name('is-password-changed');
});

// Utility Module Routes
Route::middleware('auth')->prefix('utility')->name('utility.')->group(function () {
    Route::get('/users', [UserController::class, 'paginate'])->name('users.paginate');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/show/{id}', [UserController::class, 'show'])->name('users.show');
    Route::post('/users/update/{id}', [UserController::class, 'update'])->name('users.update');

    Route::get('/roles', [RolePermissionController::class, 'paginate'])->name('roles.paginate');
    Route::get('/roles/create', [RolePermissionController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RolePermissionController::class, 'store'])->name('roles.store');
    Route::get('/roles/show/{id}', [RolePermissionController::class, 'show'])->name('roles.show');
    Route::post('/roles/update/{id}', [RolePermissionController::class, 'update'])->name('roles.update');
    Route::delete('/roles/delete/{id}', [RolePermissionController::class, 'delete'])->name('roles.delete');

    Route::get('/audit-trails', [AuditTrailController::class, 'paginate'])->name('audit-trails.paginate');
});

// Purchasing Module Router
Route::middleware('auth')->prefix('purchasing')->name('purchasing.')->group(function () {
    Route::get('/purchase-requests', [PurchaseRequestController::class, 'paginate'])->name('purchase-requests.index');
    Route::get('/purchase-requests/{id}', [PurchaseRequestController::class, 'show'])->name('purchase-requests.show');
});

// API Routes
Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    Route::get('/me', [AuthController::class, 'me'])->name('me');
    Route::get('/me/permissions', [AuthController::class, 'getMePermissions'])->name('me.permissions');

    Route::prefix('utility')->name('utility.')->group(function () {
        Route::get('/warehouses/all', [WarehouseController::class, 'getAll'])->name('warehouses.all');
        Route::get('/roles/all', [RolePermissionController::class, 'getAll'])->name('roles.all');
        Route::get('/permissions/all', [RolePermissionController::class, 'permissions'])->name('permissions.all');
        Route::get('/monitoring/stats', [MonitoringController::class, 'show'])->name('monitoring.stats');
    });

    Route::prefix('purchasing')->name('purchasing.')->group(function () {
        Route::get('/purchase-requests/{id}', [PurchaseRequestController::class, 'find'])->name('purchase-requests.show');
    });
});
