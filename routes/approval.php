<?php

use App\Http\Controllers\Approval\PurchasingApprovalController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('approval')->name('approval.')->group(function () {
    
    Route::prefix('purchasing')->name('purchasing.')->group(function () {
        // Explicit endpoints for Pending and Processed
        Route::get('/purchase-requests/pending', [PurchasingApprovalController::class, 'pendingPurchaseRequest'])->name('purchase-requests.pending');
        Route::get('/purchase-requests/processed', [PurchasingApprovalController::class, 'processedPurchaseRequest'])->name('purchase-requests.processed');
        
        // Keep old index for backward compatibility or redirect
        Route::get('/purchase-requests', function() {
            return redirect()->route('approval.purchasing.purchase-requests.pending');
        })->name('purchase-requests.index');
    });

});
