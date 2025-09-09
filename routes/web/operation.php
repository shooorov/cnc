<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\Operation\DeliveryController;
use App\Http\Controllers\Operation\EventController;
use App\Http\Controllers\Operation\ExpenseController;
use App\Http\Controllers\Operation\ItemInventoryController;
use App\Http\Controllers\Operation\KitchenController;
use App\Http\Controllers\Operation\KitchenDeliveryController;
use App\Http\Controllers\Operation\OrderController;
use App\Http\Controllers\Operation\OtherSaleController;
use App\Http\Controllers\Operation\POS2Controller;
use App\Http\Controllers\Operation\POSController;
use App\Http\Controllers\Operation\ProductInventoryController;
use App\Http\Controllers\Operation\ProductionController;
use App\Http\Controllers\Operation\ProductRequisitionController;
use App\Http\Controllers\Operation\RequisitionController;
use App\Http\Controllers\Operation\TransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'access.permission', 'access.branch'])->group(function () {

    Route::name('pos.')->prefix('pos')->group(function () {
        Route::get('/{order:id?}', [POSController::class, 'create'])
            ->name('create'); // pos.create

        Route::post('/store', [POSController::class, 'store'])
            ->name('store'); // pos.store

        Route::get('/{order:id}/print', [POSController::class, 'print'])
            ->name('print'); // pos.print

        Route::post('/printed', [POSController::class, 'printed'])
            ->name('printed'); // pos.printed
    });

    Route::name('pos2.')->prefix('pos2')->group(function () {
        Route::get('/{order:id?}', [POS2Controller::class, 'create'])
            ->name('create'); // pos2.create

        Route::post('/store', [POS2Controller::class, 'store'])
            ->name('store'); // pos2.store

        Route::get('/{order:id}/print', [POS2Controller::class, 'print'])
            ->name('print'); // pos2.print

        Route::post('/printed', [POS2Controller::class, 'printed'])
            ->name('printed'); // pos2.printed
    });

    Route::name('kitchen.')->prefix('kitchen')->group(function () {
        Route::get('/', [KitchenController::class, 'index'])
            ->name('index'); // kitchen.index

        Route::post('/clock', [KitchenController::class, 'clock'])
            ->name('clock'); // kitchen.clock
    });

    Route::name('production.')->prefix('productions')->group(function () {
        Route::get('/', [ProductionController::class, 'index'])
            ->name('index'); // production.index

        Route::post('/status', [ProductionController::class, 'status'])
            ->name('status'); // production.status

        Route::post('/view', [ProductionController::class, 'view'])
            ->name('view'); // production.view

        Route::post('/delivered', [ProductionController::class, 'delivered'])
            ->name('delivered'); // production.delivered
    });
});

Route::middleware(['auth', 'access.permission'])->group(function () {

    Route::name('order.')->prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])
            ->name('index'); // order.index

        Route::get('/load', [OrderController::class, 'load'])
            ->name('load'); // order.load

        Route::get('/{order:id}', [OrderController::class, 'show'])
            ->name('show'); // order.show

        Route::patch('/{order:id}/status/update/{status?}', [OrderController::class, 'updateStatus'])
            ->name('status.update'); // order.status.update

        Route::delete('/{order:id}/destroy', [OrderController::class, 'destroy'])
            ->name('destroy'); // order.destroy
    });

    Route::name('delivery.')->prefix('delivery')->group(function () {
        Route::get('/', [DeliveryController::class, 'index'])
            ->name('index'); // delivery.index

        Route::get('/load', [DeliveryController::class, 'load'])
            ->name('load'); // delivery.load

        Route::get('/pending/{order:id?}', [DeliveryController::class, 'show'])
            ->name('show'); // delivery.show

        Route::patch('/{order:id}/status/update', [DeliveryController::class, 'updateStatus'])
            ->name('status.update'); // delivery.status.update
    });

    Route::name('other_sale.')->prefix('other-sales')->group(function () {
        Route::get('/', [OtherSaleController::class, 'index'])
            ->name('index'); // other_sale.index

        Route::get('/create', [OtherSaleController::class, 'create'])
            ->name('create'); // other_sale.create

        Route::post('/store', [OtherSaleController::class, 'store'])
            ->name('store'); // other_sale.store

        Route::get('/{other_sale:id}', [OtherSaleController::class, 'show'])
            ->name('show'); // other_sale.show

        Route::get('/{other_sale:id}/edit', [OtherSaleController::class, 'edit'])
            ->name('edit'); // other_sale.edit

        Route::patch('/{other_sale:id}/update', [OtherSaleController::class, 'update'])
            ->name('update'); // other_sale.update

        Route::delete('/{other_sale:id}/destroy', [OtherSaleController::class, 'destroy'])
            ->name('destroy'); // other_sale.destroy
    });

    Route::name('expense.')->prefix('expenses')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])
            ->name('index'); // expense.index

        Route::get('/load', [ExpenseController::class, 'load'])
            ->name('load'); // expense.load

        Route::get('/create', [ExpenseController::class, 'create'])
            ->name('create'); // expense.create

        Route::post('/store', [ExpenseController::class, 'store'])
            ->name('store'); // expense.store

        Route::get('/{expense:id}', [ExpenseController::class, 'show'])
            ->name('show'); // expense.show

        Route::get('/{expense:id}/edit', [ExpenseController::class, 'edit'])
            ->name('edit'); // expense.edit

        Route::patch('/{expense:id}/update', [ExpenseController::class, 'update'])
            ->name('update'); // expense.update

        Route::patch('/{expense:id}/status/update/{status?}', [ExpenseController::class, 'updateStatus'])
            ->name('status.update'); // expense.status.update

        Route::delete('/{expense:id}/destroy', [ExpenseController::class, 'destroy'])
            ->name('destroy'); // expense.destroy
    });

    Route::name('requisition.')->prefix('requisitions')->group(function () {
        Route::get('/', [RequisitionController::class, 'index'])
            ->name('index'); // requisition.index

        Route::get('/create', [RequisitionController::class, 'create'])
            ->name('create'); // requisition.create

        Route::post('/store', [RequisitionController::class, 'store'])
            ->name('store'); // requisition.store

        Route::get('/{requisition:id}', [RequisitionController::class, 'show'])
            ->name('show'); // requisition.show

        Route::get('/{requisition:id}/edit', [RequisitionController::class, 'edit'])
            ->name('edit'); // requisition.edit

        Route::patch('/{requisition:id}/update', [RequisitionController::class, 'update'])
            ->name('update'); // requisition.update

        Route::delete('/{requisition:id}/destroy', [RequisitionController::class, 'destroy'])
            ->name('destroy'); // requisition.destroy
    });

    Route::name('product_requisition.')->prefix('product-requisitions')->group(function () {
        Route::get('/', [ProductRequisitionController::class, 'index'])
            ->name('index'); // product_requisition.index

        Route::get('/create', [ProductRequisitionController::class, 'create'])
            ->name('create'); // product_requisition.create

        Route::post('/store', [ProductRequisitionController::class, 'store'])
            ->name('store'); // product_requisition.store

        Route::get('/{product_requisition:id}', [ProductRequisitionController::class, 'show'])
            ->name('show'); // product_requisition.show

        Route::get('/{product_requisition:id}/edit', [ProductRequisitionController::class, 'edit'])
            ->name('edit'); // product_requisition.edit

        Route::patch('/{product_requisition:id}/update', [ProductRequisitionController::class, 'update'])
            ->name('update'); // product_requisition.update

        Route::delete('/{product_requisition:id}/destroy', [ProductRequisitionController::class, 'destroy'])
            ->name('destroy'); // product_requisition.destroy
    });

    Route::name('kitchen_delivery.')->prefix('kitchen-deliveries')->group(function () {
        Route::get('/', [KitchenDeliveryController::class, 'index'])
            ->name('index'); // kitchen_delivery.index

        Route::get('/create', [KitchenDeliveryController::class, 'create'])
            ->name('create'); // kitchen_delivery.create

        Route::post('/store', [KitchenDeliveryController::class, 'store'])
            ->name('store'); // kitchen_delivery.store

        Route::get('/{kitchen_delivery:id}', [KitchenDeliveryController::class, 'show'])
            ->name('show'); // kitchen_delivery.show

        Route::get('/{kitchen_delivery:id}/edit', [KitchenDeliveryController::class, 'edit'])
            ->name('edit'); // kitchen_delivery.edit

        Route::patch('/{kitchen_delivery:id}/update', [KitchenDeliveryController::class, 'update'])
            ->name('update'); // kitchen_delivery.update

        Route::delete('/{kitchen_delivery:id}/destroy', [KitchenDeliveryController::class, 'destroy'])
            ->name('destroy'); // kitchen_delivery.destroy
    });

    Route::name('event.')->prefix('events')->group(function () {
        Route::get('/', [EventController::class, 'index'])
            ->name('index'); // event.index

        Route::get('/calendar', [EventController::class, 'calendar'])
            ->name('calendar'); // event.calendar

        Route::get('/create', [EventController::class, 'create'])
            ->name('create'); // event.create

        Route::post('/store', [EventController::class, 'store'])
            ->name('store'); // event.store

        Route::get('/{event:id}', [EventController::class, 'show'])
            ->name('show'); // event.show

        Route::get('/{event:id}/edit', [EventController::class, 'edit'])
            ->name('edit'); // event.edit

        Route::patch('/{event:id}/update', [EventController::class, 'update'])
            ->name('update'); // event.update

        Route::delete('/{event:id}/destroy', [EventController::class, 'destroy'])
            ->name('destroy'); // event.destroy
    });

    Route::get('item-inventories/stock', [ItemInventoryController::class, 'stock'])
        ->name('item_inventory_stock'); // item_inventory_stock

    Route::get('item-inventories/out', [ItemInventoryController::class, 'out'])
        ->name('item_inventory_out'); // item_inventory_out

    Route::name('item_inventory.')->prefix('item-inventories')->group(function () {
        Route::get('/compare', [ItemInventoryController::class, 'compare'])
            ->name('compare'); // item_inventory.compare

        Route::get('/activities', [ItemInventoryController::class, 'activities'])
            ->name('activities'); // item_inventory.activities

        Route::get('/in', [ItemInventoryController::class, 'in'])
            ->name('in'); // item_inventory.in

        Route::get('/index', [ItemInventoryController::class, 'index'])
            ->name('index'); // item_inventory.index

        Route::get('/create', [ItemInventoryController::class, 'create'])
            ->name('create'); // item_inventory.create

        Route::post('/store', [ItemInventoryController::class, 'store'])
            ->name('store'); // item_inventory.store

        Route::get('/{item_inventory:id}', [ItemInventoryController::class, 'show'])
            ->name('show'); // item_inventory.show

        Route::get('/{item_inventory:id}/document', [ItemInventoryController::class, 'document'])
            ->name('document'); // item_inventory.document

        Route::post('/{item_inventory:id}/document/update', [ItemInventoryController::class, 'document_update'])
            ->name('document_update'); // item_inventory.document_update

        Route::get('/{item_inventory:id}/edit', [ItemInventoryController::class, 'edit'])
            ->name('edit'); // item_inventory.edit

        Route::patch('/{item_inventory:id}/update', [ItemInventoryController::class, 'update'])
            ->name('update'); // item_inventory.update

        Route::delete('/{item_inventory:id}/destroy', [ItemInventoryController::class, 'destroy'])
            ->name('destroy'); // item_inventory.destroy
    });

    Route::get('product-inventories/stock', [ProductInventoryController::class, 'stock'])
        ->name('product_inventory_stock'); // product_inventory_stock

    Route::get('product-inventories/out', [ProductInventoryController::class, 'out'])
        ->name('product_inventory_out'); // product_inventory_out

    Route::name('product_inventory.')->prefix('product-inventories')->group(function () {
        Route::get('/in', [ProductInventoryController::class, 'in'])
            ->name('in'); // product_inventory.in

        Route::get('/{product_inventory:id}', [ProductInventoryController::class, 'show'])
            ->name('show'); // product_inventory.show

        Route::delete('/{product_inventory:id}/destroy', [ProductInventoryController::class, 'destroy'])
            ->name('destroy'); // product_inventory.destroy
    });

    Route::delete('/document/{document:id}/destroy', [DocumentController::class, 'destroy'])
        ->name('document.destroy'); // document.destroy

    Route::name('transaction.')->prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])
            ->name('index'); // transaction.index

        Route::get('/transfer', [TransactionController::class, 'transfer'])
            ->name('transfer'); // transaction.transfer

        Route::post('/store', [TransactionController::class, 'store'])
            ->name('store'); // transaction.store
    });
});
