<?php

use App\Http\Controllers\DevController;
use Illuminate\Support\Facades\Route;

Route::name('dev.')->prefix('dev')->group(function () {
    Route::get('/reset/product', [DevController::class, 'resetProduct'])
        ->name('reset.product'); // dev.reset.product

    Route::get('/test', [DevController::class, 'test'])
        ->name('test'); // dev.test

    Route::get('/e-commerce', [DevController::class, 'eCommerce'])
        ->name('e_commerce'); // dev.e_commerce

    Route::get('/relation', [DevController::class, 'relation'])
        ->name('relation'); // dev.relation

    Route::get('/seeder', [DevController::class, 'seeder'])
        ->name('seeder'); // dev.seeder

    Route::get('/seed', [DevController::class, 'seed'])
        ->name('seed'); // dev.seed

    Route::get('/next', [DevController::class, 'next'])
        ->name('next'); // dev.next
});
