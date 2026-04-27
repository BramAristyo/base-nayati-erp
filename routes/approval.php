<?php

use App\Http\Controllers\Approval\PurchasingApprovalController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('approval')->name('approval.')->group(function () {
    
    Route::prefix('purchasing')->name('purchasing.')->group(function () {
        Route::get('/purchase-requests', [PurchasingApprovalController::class, 'purchaseRequest'])->name('purchase-requests.index');
    });

});
