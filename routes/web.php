<?php

use App\Http\Controllers\Utility\AuthController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Main Authenticated Entry Point
Route::middleware(['auth', 'check_password_changed'])->group(function () {
    Route::get('/dashboard', fn() => inertia('Utility/Dashboard'))->name('dashboard');
});

// Module Routes
require __DIR__ . '/utility.php';
require __DIR__ . '/purchasing.php';
