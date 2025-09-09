<?php

use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SummaryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', [DashboardController::class, 'index'])->name('index');
Route::post('/', [DashboardController::class, 'use'])->name('use');
Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

Route::get('/order-approval', [DashboardController::class, 'orderApproval'])->name('order.approval');
Route::post('/order-approval', [DashboardController::class, 'orderApprovalUpdate'])->name('order.approval.update');

Route::name('summary.')->prefix('/summary')->group(function () {
    Route::get('/', [SummaryController::class, 'overview'])->name('overview');
    Route::get('/sale/product', [SummaryController::class, 'product'])->name('product');
    Route::get('/sale/item', [SummaryController::class, 'item'])->name('item');
    Route::get('/sale/hourly', [SummaryController::class, 'hourly'])->name('hourly');
});

Route::name('profile.')->prefix('profile')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [ProfileController::class, 'profile'])->name('index');
    Route::patch('/update', [ProfileController::class, 'update'])->name('update');
    Route::get('/password', [ProfileController::class, 'password'])->name('password');
    Route::patch('/password/update', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::post('/image/update', [ProfileController::class, 'updateImage'])->name('image.update');
});

require __DIR__.'/web/dev.php';

require __DIR__.'/web/report.php';

require __DIR__.'/web/operation.php';

require __DIR__.'/web/manage.php';

require __DIR__.'/web/system.php';

require __DIR__.'/auth.php';
