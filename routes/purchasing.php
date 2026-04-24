<?php

use App\Http\Controllers\Purchasing\PurchaseRequestController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('purchasing')->name('purchasing.')->group(function () {
    Route::get('/purchase-requests', [PurchaseRequestController::class, 'paginate'])->name('purchase-requests.index');
    Route::get('/purchase-requests/{id}', [PurchaseRequestController::class, 'show'])->name('purchase-requests.show');
});

Route::middleware('auth')->prefix('api/purchasing')->name('api.purchasing.')->group(function () {
    Route::get('/purchase-requests/{id}', [PurchaseRequestController::class, 'find'])->name('purchase-requests.show');
});
