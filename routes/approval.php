<?php

use App\Http\Controllers\Approval\ApprovalController;
use App\Http\Controllers\Approval\PurchasingApprovalController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('approval')->name('approval.')->group(function () {

    Route::get('/', [ApprovalController::class, 'index'])->name('index');

    Route::prefix('purchasing')->name('purchasing.')->group(function () {
        Route::get('/purchase-requests/pending', [PurchasingApprovalController::class, 'pendingPurchaseRequest'])->name('purchase-requests.pending');
        Route::get('/purchase-requests/processed', [PurchasingApprovalController::class, 'processedPurchaseRequest'])->name('purchase-requests.processed');

        Route::get('/purchase-requests', function() {
            return redirect()->route('approval.purchasing.purchase-requests.pending');
        })->name('purchase-requests.index');

        Route::get('/purchase-orders/pending', [PurchasingApprovalController::class, 'pendingPurchaseOrder'])->name('purchase-orders.pending');
        Route::get('/purchase-orders/processed', [PurchasingApprovalController::class, 'processedPurchaseOrder'])->name('purchase-orders.processed');

        Route::get('/purchase-orders', function() {
            return redirect()->route('approval.purchasing.purchase-orders.pending');
        })->name('purchase-orders.index');

        Route::get('/receivings/pending', [PurchasingApprovalController::class, 'pendingReceiving'])->name('receivings.pending');
        Route::get('/receivings/processed', [PurchasingApprovalController::class, 'processedReceiving'])->name('receivings.processed');

        Route::get('/receivings', function() {
            return redirect()->route('approval.purchasing.receivings.pending');
        })->name('receivings.index');
    });

});
