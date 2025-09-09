<?php

use App\Http\Controllers\System\DesignationController;
use App\Http\Controllers\System\ExpenseCategoryController;
use App\Http\Controllers\System\ItemCategoryController;
use App\Http\Controllers\System\LocationController;
use App\Http\Controllers\System\OnlineProductCategoryController;
use App\Http\Controllers\System\OtherSaleCategoryController;
use App\Http\Controllers\System\PaymentMethodController;
use App\Http\Controllers\System\ProductCategoryController;
use App\Http\Controllers\System\RoleController;
use App\Http\Controllers\System\SettingController;
use App\Http\Controllers\System\StuffController;
use App\Http\Controllers\System\UnitController;
use Illuminate\Support\Facades\Route;

Route::prefix('system')->middleware(['auth', 'access.permission'])->group(function () {
    Route::name('other_sale_category.')->prefix('other-sale-categories')->group(function () {
        Route::get('/', [OtherSaleCategoryController::class, 'index'])
            ->name('index'); // other_sale_category.index

        Route::get('/create', [OtherSaleCategoryController::class, 'create'])
            ->name('create'); // other_sale_category.create

        Route::post('/store', [OtherSaleCategoryController::class, 'store'])
            ->name('store'); // other_sale_category.store

        Route::get('/{other_sale_category:id}', [OtherSaleCategoryController::class, 'show'])
            ->name('show'); // other_sale_category.show

        Route::get('/{other_sale_category:id}/edit', [OtherSaleCategoryController::class, 'edit'])
            ->name('edit'); // other_sale_category.edit

        Route::patch('/{other_sale_category:id}/update', [OtherSaleCategoryController::class, 'update'])
            ->name('update'); // other_sale_category.update

        Route::delete('/{other_sale_category:id}/destroy', [OtherSaleCategoryController::class, 'destroy'])
            ->name('destroy'); // other_sale_category.destroy
    });

    Route::name('expense_category.')->prefix('expense-categories')->group(function () {
        Route::get('/', [ExpenseCategoryController::class, 'index'])
            ->name('index'); // expense_category.index

        Route::get('/create', [ExpenseCategoryController::class, 'create'])
            ->name('create'); // expense_category.create

        Route::post('/store', [ExpenseCategoryController::class, 'store'])
            ->name('store'); // expense_category.store

        Route::get('/{expense_category:id}', [ExpenseCategoryController::class, 'show'])
            ->name('show'); // expense_category.show

        Route::get('/{expense_category:id}/edit', [ExpenseCategoryController::class, 'edit'])
            ->name('edit'); // expense_category.edit

        Route::patch('/{expense_category:id}/update', [ExpenseCategoryController::class, 'update'])
            ->name('update'); // expense_category.update

        Route::delete('/{expense_category:id}/destroy', [ExpenseCategoryController::class, 'destroy'])
            ->name('destroy'); // expense_category.destroy
    });

    Route::name('item_category.')->prefix('item-categories')->group(function () {
        Route::get('/', [ItemCategoryController::class, 'index'])
            ->name('index'); // item_category.index

        Route::get('/create', [ItemCategoryController::class, 'create'])
            ->name('create'); // item_category.create

        Route::post('/store', [ItemCategoryController::class, 'store'])
            ->name('store'); // item_category.store

        Route::get('/{item_category:id}', [ItemCategoryController::class, 'show'])
            ->name('show'); // item_category.show

        Route::get('/{item_category:id}/edit', [ItemCategoryController::class, 'edit'])
            ->name('edit'); // item_category.edit

        Route::patch('/{item_category:id}/update', [ItemCategoryController::class, 'update'])
            ->name('update'); // item_category.update

        Route::delete('/{item_category:id}/destroy', [ItemCategoryController::class, 'destroy'])
            ->name('destroy'); // item_category.destroy
    });

    Route::name('product_category.')->prefix('product-categories')->group(function () {
        Route::get('/', [ProductCategoryController::class, 'index'])
            ->name('index'); // product_category.index

        Route::get('/create', [ProductCategoryController::class, 'create'])
            ->name('create'); // product_category.create

        Route::post('/store', [ProductCategoryController::class, 'store'])
            ->name('store'); // product_category.store

        Route::get('/specification', [ProductCategoryController::class, 'specification'])
            ->name('specification'); // product_category.specification

        Route::post('/specification-update', [ProductCategoryController::class, 'specificationUpdate'])
            ->name('specification_update'); // product_category.specification_update

        Route::get('/{product_category:id}', [ProductCategoryController::class, 'show'])
            ->name('show'); // product_category.show

        Route::get('/{product_category:id}/edit', [ProductCategoryController::class, 'edit'])
            ->name('edit'); // product_category.edit

        Route::post('/{product_category:id}/update', [ProductCategoryController::class, 'update'])
            ->name('update'); // product_category.update

        Route::delete('/{product_category:id}/destroy', [ProductCategoryController::class, 'destroy'])
            ->name('destroy'); // product_category.destroy
    });

    Route::name('online_product_category.')->prefix('online/product-categories')->group(function () {
        Route::get('/', [OnlineProductCategoryController::class, 'index'])
            ->name('index'); // online_product_category.index

        Route::get('/create', [OnlineProductCategoryController::class, 'create'])
            ->name('create'); // online_product_category.create

        Route::post('/store', [OnlineProductCategoryController::class, 'store'])
            ->name('store'); // online_product_category.store

        Route::get('/order', [OnlineProductCategoryController::class, 'order'])
            ->name('order'); // online_product_category.order

        Route::post('/order-update', [OnlineProductCategoryController::class, 'orderUpdate'])
            ->name('order_update'); // online_product_category.order_update

        Route::post('/positioning', [OnlineProductCategoryController::class, 'positioning'])
            ->name('positioning'); // online_product_category.positioning

        Route::get('/{online_product_category:id}', [OnlineProductCategoryController::class, 'show'])
            ->name('show'); // online_product_category.show

        Route::get('/{online_product_category:id}/edit', [OnlineProductCategoryController::class, 'edit'])
            ->name('edit'); // online_product_category.edit

        Route::post('/{online_product_category:id}/update', [OnlineProductCategoryController::class, 'update'])
            ->name('update'); // online_product_category.update

        Route::delete('/{online_product_category:id}/destroy', [OnlineProductCategoryController::class, 'destroy'])
            ->name('destroy'); // online_product_category.destroy
    });

    Route::name('method.')->prefix('methods')->group(function () {
        Route::get('/', [PaymentMethodController::class, 'index'])
            ->name('index'); // method.index

        Route::get('/create', [PaymentMethodController::class, 'create'])
            ->name('create'); // method.create

        Route::post('/store', [PaymentMethodController::class, 'store'])
            ->name('store'); // method.store

        Route::get('/{method:id}', [PaymentMethodController::class, 'show'])
            ->name('show'); // method.show

        Route::get('/{method:id}/edit', [PaymentMethodController::class, 'edit'])
            ->name('edit'); // method.edit

        Route::patch('/{method:id}/update', [PaymentMethodController::class, 'update'])
            ->name('update'); // method.update

        Route::delete('/{method:id}/destroy', [PaymentMethodController::class, 'destroy'])
            ->name('destroy'); // method.destroy
    });

    Route::name('unit.')->prefix('units')->group(function () {
        Route::get('/', [UnitController::class, 'index'])
            ->name('index'); // unit.index

        Route::get('/create', [UnitController::class, 'create'])
            ->name('create'); // unit.create

        Route::post('/store', [UnitController::class, 'store'])
            ->name('store'); // unit.store

        Route::get('/{unit:id}', [UnitController::class, 'show'])
            ->name('show'); // unit.show

        Route::get('/{unit:id}/edit', [UnitController::class, 'edit'])
            ->name('edit'); // unit.edit

        Route::patch('/{unit:id}/update', [UnitController::class, 'update'])
            ->name('update'); // unit.update

        Route::delete('/{unit:id}/destroy', [UnitController::class, 'destroy'])
            ->name('destroy'); // unit.destroy
    });

    Route::name('designation.')->prefix('designations')->group(function () {
        Route::get('/', [DesignationController::class, 'index'])
            ->name('index'); // designation.index

        Route::get('/create', [DesignationController::class, 'create'])
            ->name('create'); // designation.create

        Route::post('/store', [DesignationController::class, 'store'])
            ->name('store'); // designation.store

        Route::get('/{designation:id}', [DesignationController::class, 'show'])
            ->name('show'); // designation.show

        Route::get('/{designation:id}/role', [DesignationController::class, 'role'])
            ->name('role'); // designation.role

        Route::get('/{designation:id}/edit', [DesignationController::class, 'edit'])
            ->name('edit'); // designation.edit

        Route::patch('/{designation:id}/update', [DesignationController::class, 'update'])
            ->name('update'); // designation.update

        Route::delete('/{designation:id}/destroy', [DesignationController::class, 'destroy'])
            ->name('destroy'); // designation.destroy
    });

    Route::name('location.')->prefix('locations')->group(function () {
        Route::get('/', [LocationController::class, 'index'])
            ->name('index'); // location.index

        Route::get('/create', [LocationController::class, 'create'])
            ->name('create'); // location.create

        Route::post('/store', [LocationController::class, 'store'])
            ->name('store'); // location.store

        Route::get('/{location:id}', [LocationController::class, 'show'])
            ->name('show'); // location.show

        Route::get('/{location:id}/role', [LocationController::class, 'role'])
            ->name('role'); // location.role

        Route::get('/{location:id}/edit', [LocationController::class, 'edit'])
            ->name('edit'); // location.edit

        Route::patch('/{location:id}/update', [LocationController::class, 'update'])
            ->name('update'); // location.update

        Route::delete('/{location:id}/destroy', [LocationController::class, 'destroy'])
            ->name('destroy'); // location.destroy
    });

    Route::name('stuff.')->prefix('tables')->group(function () {
        Route::get('/', [StuffController::class, 'index'])
            ->name('index'); // stuff.index

        Route::get('/create', [StuffController::class, 'create'])
            ->name('create'); // stuff.create

        Route::post('/store', [StuffController::class, 'store'])
            ->name('store'); // stuff.store

        Route::get('/{stuff:id}', [StuffController::class, 'show'])
            ->name('show'); // stuff.show

        Route::get('/{stuff:id}/role', [StuffController::class, 'role'])
            ->name('role'); // stuff.role

        Route::get('/{stuff:id}/edit', [StuffController::class, 'edit'])
            ->name('edit'); // stuff.edit

        Route::patch('/{stuff:id}/update', [StuffController::class, 'update'])
            ->name('update'); // stuff.update

        Route::delete('/{stuff:id}/destroy', [StuffController::class, 'destroy'])
            ->name('destroy'); // stuff.destroy
    });

    Route::name('role.')->prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])
            ->name('index'); // role.index

        Route::get('/create', [RoleController::class, 'create'])
            ->name('create'); // role.create

        Route::post('/store', [RoleController::class, 'store'])
            ->name('store'); // role.store

        Route::get('/{role:id}/edit', [RoleController::class, 'edit'])
            ->name('edit'); // role.edit

        Route::patch('/{role:id}/update', [RoleController::class, 'update'])
            ->name('update'); // role.update

        Route::delete('/{role:id}/destroy', [RoleController::class, 'destroy'])
            ->name('destroy'); // role.destroy

        Route::get('/{role:id}/permission', [RoleController::class, 'permission'])
            ->name('permission.edit'); // role.permission.edit

        Route::patch('/{role:id}/permission', [RoleController::class, 'permissionUpdate'])
            ->name('permission.update'); // role.permission.update
    });

    Route::name('setting.')->prefix('setting')->group(function () {
        Route::get('/', [SettingController::class, 'edit'])
            ->name('edit'); // setting.edit

        Route::patch('/', [SettingController::class, 'update'])
            ->name('update'); // setting.update
    });
});
