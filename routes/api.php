<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// For POS
Route::get('/basic', [HomeController::class, 'basic']);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::post('/auth', [AuthController::class, 'auth']);
// Route::post('/users', [AuthController::class, 'users']);

Route::get('/locations', [HomeController::class, 'locations']);
Route::get('/categories', [HomeController::class, 'categories']);
Route::get('/products', [HomeController::class, 'products']);

Route::any('/customer/auth', [CustomerController::class, 'auth']);
Route::post('/customer/{customer:id}/update', [CustomerController::class, 'update']);

Route::post('/order/store', [OrderController::class, 'store']);
Route::post('/order/{number}/show', [OrderController::class, 'show']);
