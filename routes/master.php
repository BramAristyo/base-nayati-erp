<?php

use App\Http\Controllers\Master\BranchController;
use App\Http\Controllers\Master\CurrencyController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\DeliveryTermController;
use App\Http\Controllers\Master\DepartmentController;
use App\Http\Controllers\Master\EmployeeController;
use App\Http\Controllers\Master\SupplierController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('master')->name('master.')->group(function () {
    Route::get('/branches', [BranchController::class, 'paginate'])->name('branches.index');
    Route::get('/currencies', [CurrencyController::class, 'paginate'])->name('currencies.index');
    Route::get('/customers', [CustomerController::class, 'paginate'])->name('customers.index');
    Route::get('/delivery-terms', [DeliveryTermController::class, 'paginate'])->name('delivery-terms.index');
    // Renamed from department to match others
    Route::get('/departments', [DepartmentController::class, 'paginate'])->name('departments.index');
    Route::get('/employees', [EmployeeController::class, 'paginate'])->name('employees.index');
    Route::get('/suppliers', [SupplierController::class, 'paginate'])->name('suppliers.index');
});