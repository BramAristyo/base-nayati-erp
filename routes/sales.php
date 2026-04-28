<?php

use App\Http\Controllers\Sales\DeliveryOrderController;
use App\Http\Controllers\Sales\InvoiceController;
use App\Http\Controllers\Sales\ProformaController;
use App\Http\Controllers\Sales\SalesOrderController;
use App\Http\Controllers\Sales\ShipmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'check_password_changed'])->prefix('sales')->name('sales.')->group(function () {
    // Sales Orders
    Route::get('/orders', [SalesOrderController::class, 'paginate'])->name('orders.index');
    Route::get('/orders/export', [SalesOrderController::class, 'export'])->name('orders.export');
    Route::get('/orders/{id}', [SalesOrderController::class, 'show'])->name('orders.show');

    // Proformas
    Route::get('/proformas', [ProformaController::class, 'paginate'])->name('proformas.index');
    Route::get('/proformas/export', [ProformaController::class, 'export'])->name('proformas.export');
    Route::get('/proformas/{id}', [ProformaController::class, 'show'])->name('proformas.show');

    // Delivery Orders
    Route::get('/delivery-orders', [DeliveryOrderController::class, 'paginate'])->name('delivery-orders.index');
    Route::get('/delivery-orders/export', [DeliveryOrderController::class, 'export'])->name('delivery-orders.export');
    Route::get('/delivery-orders/{id}', [DeliveryOrderController::class, 'show'])->name('delivery-orders.show');

    // Shipments
    Route::get('/shipments', [ShipmentController::class, 'paginate'])->name('shipments.index');
    Route::get('/shipments/export', [ShipmentController::class, 'export'])->name('shipments.export');
    Route::get('/shipments/{id}', [ShipmentController::class, 'show'])->name('shipments.show');

    // Invoices
    Route::get('/invoices', [InvoiceController::class, 'paginate'])->name('invoices.index');
    Route::get('/invoices/export', [InvoiceController::class, 'export'])->name('invoices.export');
    Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
});
