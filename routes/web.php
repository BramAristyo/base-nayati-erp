<?php

use App\Http\Controllers\Utility\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->prefix('api')->name('api.')->group(function (){
    Route::get('/me', [AuthController::class, 'me'])->name('me');
});