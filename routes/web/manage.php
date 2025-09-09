<?php

use App\Http\Controllers\Manage\AccountController;
use App\Http\Controllers\Manage\BranchController;
use App\Http\Controllers\Manage\CentralController;
use App\Http\Controllers\Manage\CustomerController;
use App\Http\Controllers\Manage\EmployeeController;
use App\Http\Controllers\Manage\ItemController;
use App\Http\Controllers\Manage\ProductController;
use App\Http\Controllers\Manage\ToppingController;
use App\Http\Controllers\Manage\UserController;
use Illuminate\Support\Facades\Route;

// , 'access.permission'
Route::prefix('manage')->middleware(['auth', 'access.permission'])->group(function () {
    Route::name('topping.')->prefix('toppings')->group(function () {
        Route::get('/', [ToppingController::class, 'index'])
            ->name('index'); // topping.index

        Route::get('/create', [ToppingController::class, 'create'])
            ->name('create'); // topping.create

        Route::post('/store', [ToppingController::class, 'store'])
            ->name('store'); // topping.store

        Route::get('/{topping:id}', [ToppingController::class, 'show'])
            ->name('show'); // topping.show

        Route::get('/{topping:id}/edit', [ToppingController::class, 'edit'])
            ->name('edit'); // topping.edit

        Route::post('/{topping:id}/update', [ToppingController::class, 'update'])
            ->name('update'); // topping.update

        Route::delete('/{topping:id}/destroy', [ToppingController::class, 'destroy'])
            ->name('destroy'); // topping.destroy
    });

    Route::name('customer.')->prefix('customers')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])
            ->name('index'); // customer.index

        Route::get('/create', [CustomerController::class, 'create'])
            ->name('create'); // customer.create

        Route::post('/store', [CustomerController::class, 'store'])
            ->name('store'); // customer.store

        Route::get('/{customer:id}', [CustomerController::class, 'show'])
            ->name('show'); // customer.show

        Route::get('/{customer:id}/edit', [CustomerController::class, 'edit'])
            ->name('edit'); // customer.edit

        Route::patch('/{customer:id}/update', [CustomerController::class, 'update'])
            ->name('update'); // customer.update

        Route::delete('/{customer:id}/destroy', [CustomerController::class, 'destroy'])
            ->name('destroy'); // customer.destroy
    });

    Route::name('branch.')->prefix('branches')->group(function () {
        Route::get('/', [BranchController::class, 'index'])
            ->name('index'); // branch.index

        Route::get('/create', [BranchController::class, 'create'])
            ->name('create'); // branch.create

        Route::post('/store', [BranchController::class, 'store'])
            ->name('store'); // branch.store

        Route::get('/access', [BranchController::class, 'access'])
            ->name('access'); // branch.access

        Route::post('/access-update', [BranchController::class, 'accessUpdate'])
            ->name('access_update'); // branch.access.update

        Route::get('/token', [BranchController::class, 'token'])
            ->name('token'); // branch.token

        Route::post('/token-update', [BranchController::class, 'tokenUpdate'])
            ->name('token_update'); // branch.token_update

        Route::get('/{branch:id}', [BranchController::class, 'show'])
            ->name('show'); // branch.show

        Route::get('/{branch:id}/edit', [BranchController::class, 'edit'])
            ->name('edit'); // branch.edit

        Route::post('/{branch:id}/update', [BranchController::class, 'update'])
            ->name('update'); // branch.update

        Route::patch('/{branch:id}/status/update/{status?}', [BranchController::class, 'updateStatus'])
            ->name('status.update'); // branch.status.update

        Route::delete('/{branch:id}/destroy', [BranchController::class, 'destroy'])
            ->name('destroy'); // branch.destroy
    });

    Route::name('central.')->prefix('central-kitchens')->group(function () {
        Route::get('/', [CentralController::class, 'index'])
            ->name('index'); // central.index

        Route::get('/create', [CentralController::class, 'create'])
            ->name('create'); // central.create

        Route::post('/store', [CentralController::class, 'store'])
            ->name('store'); // central.store

        Route::get('/access', [CentralController::class, 'access'])
            ->name('access'); // central.access

        Route::post('/access-update', [CentralController::class, 'accessUpdate'])
            ->name('access_update'); // central.access_update

        Route::get('/{central_kitchen:id}', [CentralController::class, 'show'])
            ->name('show'); // central.show

        Route::get('/{central_kitchen:id}/edit', [CentralController::class, 'edit'])
            ->name('edit'); // central.edit

        Route::post('/{central_kitchen:id}/update', [CentralController::class, 'update'])
            ->name('update'); // central.update

        Route::delete('/{central_kitchen:id}/destroy', [CentralController::class, 'destroy'])
            ->name('destroy'); // central.destroy
    });

    Route::name('product.')->prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])
            ->name('index'); // product.index

        Route::get('/create', [ProductController::class, 'create'])
            ->name('create'); // product.create

        Route::post('/store', [ProductController::class, 'store'])
            ->name('store'); // product.store

        Route::get('/{product:id}', [ProductController::class, 'show'])
            ->name('show'); // product.show

        Route::get('/{product:id}/edit', [ProductController::class, 'edit'])
            ->name('edit'); // product.edit

        Route::post('/{product:id}/update', [ProductController::class, 'update'])
            ->name('update'); // product.update
        
            Route::patch('/{product:id}/status/update/{status?}', [ProductController::class, 'updateStatus'])
            ->name('status.update');

        Route::delete('/{product:id}/destroy', [ProductController::class, 'destroy'])
            ->name('destroy'); // product.destroy
    });

    Route::name('item.')->prefix('items')->group(function () {
        Route::get('/', [ItemController::class, 'index'])
            ->name('index'); // item.index

        Route::get('/create', [ItemController::class, 'create'])
            ->name('create'); // item.create

        Route::post('/store', [ItemController::class, 'store'])
            ->name('store'); // item.store

        Route::get('/{item:id}', [ItemController::class, 'show'])
            ->name('show'); // item.show

        Route::get('/{item:id}/edit', [ItemController::class, 'edit'])
            ->name('edit'); // item.edit

        Route::post('/{item:id}/update', [ItemController::class, 'update'])
            ->name('update'); // item.update

        Route::delete('/{item:id}/destroy', [ItemController::class, 'destroy'])
            ->name('destroy'); // item.destroy
    });

    Route::name('employee.')->prefix('employees')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])
            ->name('index'); // employee.index

        Route::get('/create', [EmployeeController::class, 'create'])
            ->name('create'); // employee.create

        Route::post('/store', [EmployeeController::class, 'store'])
            ->name('store'); // employee.store

        Route::get('/{employee:id}', [EmployeeController::class, 'show'])
            ->name('show'); // employee.show

        Route::get('/{employee:id}/user', [EmployeeController::class, 'user'])
            ->name('user'); // employee.user

        Route::get('/{employee:id}/edit', [EmployeeController::class, 'edit'])
            ->name('edit'); // employee.edit

        Route::patch('/{employee:id}/update', [EmployeeController::class, 'update'])
            ->name('update'); // employee.update

        Route::delete('/{employee:id}/destroy', [EmployeeController::class, 'destroy'])
            ->name('destroy'); // employee.destroy
    });

    Route::name('account.')->prefix('accounts')->group(function () {
        Route::get('/', [AccountController::class, 'index'])
            ->name('index'); // account.index

        Route::get('/create', [AccountController::class, 'create'])
            ->name('create'); // account.create

        Route::post('/store', [AccountController::class, 'store'])
            ->name('store'); // account.store

        Route::get('/{account:id}', [AccountController::class, 'show'])
            ->name('show'); // account.show

        Route::get('/{account:id}/edit', [AccountController::class, 'edit'])
            ->name('edit'); // account.edit

        Route::patch('/{account:id}/update', [AccountController::class, 'update'])
            ->name('update'); // account.update

        Route::delete('/{account:id}/destroy', [AccountController::class, 'destroy'])
            ->name('destroy'); // account.destroy
    });

    Route::name('user.')->prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])
            ->name('index'); // user.index

        Route::get('/create', [UserController::class, 'create'])
            ->name('create'); // user.create

        Route::post('/store', [UserController::class, 'store'])
            ->name('store'); // user.store

        Route::get('/email-digest', [UserController::class, 'digest'])
            ->name('digest'); // user.digest

        Route::post('/email-digest-update', [UserController::class, 'digestUpdate'])
            ->name('digest.update'); // user.digest.update

        Route::get('/{user:id}', [UserController::class, 'show'])
            ->name('show'); // user.show

        Route::get('/{user:id}/login', [UserController::class, 'login'])
            ->name('login'); // user.login

        Route::get('/{user:id}/edit', [UserController::class, 'edit'])
            ->name('edit'); // user.edit

        Route::patch('/{user:id}/update', [UserController::class, 'update'])
            ->name('update'); // user.update

        Route::delete('/{user:id}/destroy', [UserController::class, 'destroy'])
            ->name('destroy'); // user.destroy

        Route::patch('/{user:id}/status/update/{status?}', [UserController::class, 'updateStatus'])
            ->name('status.update'); // user.status.update

        Route::post('/{user:id}/image/update', [UserController::class, 'updateImage'])
            ->name('image.update'); // user.image.update

        Route::get('/history', [UserController::class, 'history'])
            ->name('history'); // user.history
    });
});
