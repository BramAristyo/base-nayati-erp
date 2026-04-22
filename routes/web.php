<?php

use App\Http\Controllers\Utility\AuthController;
use App\Http\Controllers\Utility\RolePermissionController;
use App\Http\Controllers\Utility\UserController;
use App\Http\Controllers\Utility\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'check_password_changed'])->get('/dashboard', function () {
    return inertia('Utility/Dashboard');
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/change-password', [UserController::class, 'showChangePasswordForm'])->name('user.change-password');
    Route::post('/change-password', [UserController::class, 'changePassword'])->name('user.change-password.post');
    
    Route::get('/settings', [UserController::class, 'showSettingForm'])->name('user.settings');
    Route::post('/settings', [UserController::class, 'updateSetting'])->name('user.settings.post');
});

Route::middleware('auth')->prefix('auth')->name('auth.')->group(function (){
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change-password');
    Route::post('/is-password-changed', [AuthController::class, 'isPasswordChanged'])->name('is-password-changed');
});

Route::middleware('auth')->prefix('utility')->name('utility.')->group(function (){
    Route::get('/users', [UserController::class, 'paginate'])->name('users.paginate');
    Route::get('/users/show/{id}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
});


// ---------------- API ----------------
Route::middleware('auth')->prefix('api')->name('api.')->group(function (){
    Route::get('/me', [AuthController::class, 'me'])->name('me');

    Route::prefix('utility')->name('utility.')->group(function (){
        Route::get('/warehouses/all', [WarehouseController::class, 'getAll'])->name('warehouses.all');
        Route::get('/roles/all', [RolePermissionController::class, 'getAll'])->name('roles.all');
    });
});