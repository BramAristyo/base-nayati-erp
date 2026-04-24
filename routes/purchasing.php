<?php

use App\Http\Controllers\Purchasing\PurchaseOrderController;
use App\Http\Controllers\Purchasing\PurchaseRequestController;
use App\Http\Controllers\Purchasing\ReceivingController;
use Illuminate\Support\Facades\Route;

// --- Purchase Request ---
Route::middleware('auth')->prefix('purchasing/purchase-requests')->name('purchasing.purchase-requests.')->group(function () {
    Route::get('/', [PurchaseRequestController::class, 'paginate'])->name('index');
    Route::get('/export', [PurchaseRequestController::class, 'export'])->name('export');
    Route::get('/{id}', [PurchaseRequestController::class, 'show'])->name('show');
});

// --- Purchase Order ---
Route::middleware('auth')->prefix('purchasing/purchase-orders')->name('purchasing.purchase-orders.')->group(function () {
    Route::get('/', [PurchaseOrderController::class, 'paginate'])->name('index');
    Route::get('/export', [PurchaseOrderController::class, 'export'])->name('export');
    Route::get('/{id}', [PurchaseOrderController::class, 'show'])->name('show');
});

// --- Receiving ---
Route::middleware('auth')->prefix('purchasing/receivings')->name('purchasing.receivings.')->group(function () {
    Route::get('/', [ReceivingController::class, 'paginate'])->name('index');
    Route::get('/export', [ReceivingController::class, 'export'])->name('export');
    Route::get('/{id}', [ReceivingController::class, 'show'])->name('show');
});
