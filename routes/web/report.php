<?php

use App\Http\Controllers\ExportController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('export')->name('export.')->middleware(['auth', 'access.permission'])->group(function () {
    Route::get('/sale', [ExportController::class, 'sale'])
        ->name('sales');  // export.sales

    Route::get('/orders', [ExportController::class, 'order'])
        ->name('orders');  // export.orders

    Route::get('/expense', [ExportController::class, 'expense'])
        ->name('expenses');  // export.expenses

    Route::get('/sale-products', [ExportController::class, 'saleProduct'])
        ->name('sale_products');  // export.sale_products

    Route::get('/sale-items', [ExportController::class, 'saleItem'])
        ->name('sale_items');  // export.sale_items
});

Route::prefix('report')->name('report.')->middleware(['auth', 'access.permission'])->group(function () {
    Route::get('/sale', [ReportController::class, 'sale'])
        ->name('sale'); // report.sale

    Route::get('/employee', [ReportController::class, 'employee'])
        ->name('employee'); // report.employee

    Route::get('/vat', [ReportController::class, 'vat'])
        ->name('vat'); // report.vat

    Route::get('/item-requisition/{requisition:id}', [ReportController::class, 'requisition'])
        ->name('requisition'); // report.requisition

    Route::get('/product-requisition/{product_requisition:id}', [ReportController::class, 'product_requisition'])
        ->name('product_requisition'); // report.product_requisition

    Route::get('/kitchen-delivery/{kitchen_delivery:id}', [ReportController::class, 'kitchen_delivery'])
        ->name('kitchen_delivery'); // report.kitchen_delivery

    Route::get('/item-stock', [ReportController::class, 'item_stock'])
        ->name('item_stock'); // report.item_stock

    Route::get('/item-inventories', [ReportController::class, 'item_inventory'])
        ->name('item_inventory'); // report.item_inventory

    Route::get('/product-stock', [ReportController::class, 'product_stock'])
        ->name('product_stock'); // report.product_stock

    // Route::get('/product-inventories', [ReportController::class, 'product_inventory'])
    //              ->name('product_inventory');

    Route::get('/product-inventories/{product_inventory:id}', [ReportController::class, 'product_inventory_detail'])
        ->name('product_inventory_detail'); // report.product_inventory_detail

    // Route::get('/invoices/{order:id}', [ReportController::class, 'invoice'])
    //              ->name('order.invoice');
});
