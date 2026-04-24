<?php

use App\Http\Controllers\Purchasing\PurchaseOrderController;
use App\Http\Controllers\Purchasing\PurchaseRequestController;
use Illuminate\Support\Facades\Route;

// --- Purchase Request ---
Route::middleware('auth')->prefix('purchasing/purchase-requests')->name('purchasing.purchase-requests.')->group(function () {
    Route::get('/', [PurchaseRequestController::class, 'paginate'])->name('index');
    Route::get('/{id}', [PurchaseRequestController::class, 'show'])->name('show');
});

// --- Purchase Order ---
Route::middleware('auth')->prefix('purchasing/purchase-orders')->name('purchasing.purchase-orders.')->group(function () {
    Route::get('/', [PurchaseOrderController::class, 'paginate'])->name('index');
    Route::get('/{id}', [PurchaseOrderController::class, 'show'])->name('show');
});
